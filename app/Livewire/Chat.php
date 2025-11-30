<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Attachment;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\MessageDraft;
use App\Models\Setting;
use App\Services\Prism\ProviderConfig;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class Chat extends Component
{
    use WithFileUploads;

    public ?int $conversationId = null;

    public string $message = '';

    /** @var array<int, mixed> */
    public array $pendingFiles = [];

    /** @var array<int, array{name: string, type: string, size: int, preview: string|null, tempPath: string, mimeType: string}> */
    public array $pendingAttachments = [];

    /** @var array<int, array<string, mixed>> */
    public array $conversations = [];

    public ?int $editingConversationId = null;

    public string $editingTitle = '';

    public int $currentPage = 1;

    public string $selectedModel = '';

    public string $searchQuery = '';

    public bool $isProcessing = false;

    public function mount(?int $conversationId = null): void
    {
        $this->conversationId = $conversationId;
        $this->conversations = $this->getConversationsHistory();
        $this->selectedModel = Setting::getSelectedModel() ?? '';

        // Auto-select first available model if none selected
        if (empty($this->selectedModel)) {
            $availableModels = $this->getAvailableModels();
            if (! empty($availableModels)) {
                foreach ($availableModels as $providerKey => $providerData) {
                    if (! empty($providerData['models'])) {
                        $providerIdentifier = str_replace('_config', '', $providerKey);
                        $firstModel = $providerData['models'][0];
                        $this->selectedModel = "{$providerIdentifier}:{$firstModel}";
                        Setting::setSelectedModel($this->selectedModel);
                        break;
                    }
                }
            }
        }

        $this->loadDraft();

        $this->dispatch('reset-window-state');
    }

    public function updatedSearchQuery(): void
    {
        $this->currentPage = 1;
        $this->conversations = $this->getConversationsHistory();
        $this->dispatch('conversations-updated', conversations: $this->conversations);
    }

    public function newConversation(): void
    {
        $this->clearDraft();
        $this->conversationId = null;
        $this->message = '';
        response()->redirectToRoute('chat');
    }

    public function loadConversation(int $conversationId): void
    {
        $this->conversationId = $conversationId;
        $this->loadDraft();
        $this->redirect(route('chat', ['conversationId' => $conversationId]));
    }

    public function loadMoreHistoryConversations(): array
    {
        $this->currentPage++;
        $newConversations = $this->getConversationsHistory();
        $this->conversations = [...$this->conversations, ...$newConversations];

        return $newConversations;
    }

    public function saveTitleDirect(int $conversationId, string $title): bool
    {
        try {
            $title = trim($title);

            if (empty($title) || strlen($title) > 255) {
                return false;
            }

            $conversation = Conversation::query()->find($conversationId);

            if (! $conversation) {
                return false;
            }

            $conversation->timestamps = false;
            $conversation->update(['title' => $title]);
            $conversation->timestamps = true;

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function saveDraft(): void
    {
        $content = trim($this->message);

        if (empty($content)) {
            $this->clearDraft();

            return;
        }

        MessageDraft::query()->updateOrCreate(
            ['conversation_id' => $this->conversationId],
            ['content' => $content]
        );
    }

    /**
     * @param  array<int, mixed>  $files
     */
    public function addFiles(array $files): void
    {
        foreach ($files as $file) {
            if (! $file instanceof TemporaryUploadedFile) {
                continue;
            }

            $mimeType = $file->getMimeType() ?: 'application/octet-stream';

            $type = $this->getAttachmentType($mimeType);
            $preview = null;

            if ($type === 'image' && str_starts_with($mimeType, 'image/')) {
                $preview = $file->temporaryUrl();
            }

            $this->pendingAttachments[] = [
                'name' => $file->getClientOriginalName(),
                'type' => $type,
                'size' => $file->getSize(),
                'preview' => $preview,
                'tempPath' => $file->getRealPath(),
                'mimeType' => $mimeType,
            ];

            $this->pendingFiles[] = $file;
        }
    }

    public function removeAttachment(int $index): void
    {
        if (isset($this->pendingAttachments[$index])) {
            unset($this->pendingAttachments[$index]);
            unset($this->pendingFiles[$index]);
            $this->pendingAttachments = array_values($this->pendingAttachments);
            $this->pendingFiles = array_values($this->pendingFiles);
        }
    }

    public function clearAttachments(): void
    {
        $this->pendingAttachments = [];
        $this->pendingFiles = [];
    }

    /**
     * @return array<string>
     */
    public function getSupportedMediaTypes(): array
    {
        if (empty($this->selectedModel)) {
            return [];
        }

        $providerConfig = app(ProviderConfig::class);
        $parsed = $providerConfig->parseSelectedModel($this->selectedModel);

        if (! $parsed) {
            return [];
        }

        return $providerConfig->getSupportedMediaTypes($parsed['provider']);
    }

    public function sendMessage(): void
    {
        $this->message = trim($this->message);

        $hasAttachments = \count($this->pendingAttachments) > 0;

        $this->validate([
            'message' => $hasAttachments ? 'nullable|string|max:'.config('purrai.limits.max_message_length') : 'required|string|max:'.config('purrai.limits.max_message_length'),
        ]);

        if (empty($this->selectedModel)) {
            $this->addError('message', __('chat.errors.no_model_selected'));

            return;
        }

        if (! $this->conversationId) {
            $title = ! empty($this->message) ? mb_substr($this->message, 0, 100) : __('chat.attachment_conversation');
            $conversation = Conversation::create([
                'title' => $title,
            ]);

            $this->conversationId = $conversation->id;
        }

        $message = Message::create([
            'conversation_id' => $this->conversationId,
            'role' => 'user',
            'content' => $this->message,
        ]);

        $this->saveAttachments($message);

        $this->clearDraft();
        $this->message = '';
        $this->clearAttachments();
        $this->isProcessing = true;

        $this->dispatch('message-sent');
        $this->dispatch('scroll-to-user-message');
        $this->dispatch('start-ai-stream', [
            'conversationId' => $this->conversationId,
            'selectedModel' => $this->selectedModel,
        ]);
    }

    private function saveAttachments(Message $message): void
    {
        foreach ($this->pendingFiles as $index => $file) {
            if (! $file instanceof TemporaryUploadedFile) {
                continue;
            }

            if (! isset($this->pendingAttachments[$index])) {
                continue;
            }

            $attachmentData = $this->pendingAttachments[$index];
            $path = $file->store('attachments', 'local');

            if (! $path) {
                continue;
            }

            Attachment::query()->create([
                'message_id' => $message->id,
                'type' => $attachmentData['type'],
                'filename' => $attachmentData['name'],
                'path' => $path,
                'mime_type' => $attachmentData['mimeType'],
                'size' => $attachmentData['size'],
            ]);
        }
    }

    private function getAttachmentType(string $mimeType): string
    {
        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        }

        if (str_starts_with($mimeType, 'audio/')) {
            return 'audio';
        }

        if (str_starts_with($mimeType, 'video/')) {
            return 'video';
        }

        return 'document';
    }

    public function streamComplete(): void
    {
        $this->isProcessing = false;
    }

    public function updatedPendingFiles(): void
    {
        $this->addFiles($this->pendingFiles);
    }

    public function updatedSelectedModel(): void
    {
        if (! empty($this->selectedModel)) {
            Setting::setSelectedModel($this->selectedModel);
        }
    }

    /**
     * @return array<string, array{provider: string, models: array<string>}>
     */
    public function getAvailableModels(): array
    {
        return Setting::getAvailableModels();
    }

    public function render(): mixed
    {
        $conversation = $this->conversationId
            ? Conversation::with('messages.attachments')->find($this->conversationId)
            : null;

        $totalConversations = Conversation::query()->count();
        $hasMorePages = \count($this->conversations) < $totalConversations;
        $availableModels = $this->getAvailableModels();

        $viewData = [
            'conversation' => $conversation,
            'hasMorePages' => $hasMorePages,
            'availableModels' => $availableModels,
        ];

        return view('livewire.chat', $viewData);
    }

    private function getConversationsHistory(?int $limit = null): array
    {
        $limit ??= config('purrai.limits.conversations_per_page');
        $currentPage = max(1, $this->currentPage);
        $offset = ($currentPage - 1) * $limit;

        $query = Conversation::query()
            ->selectRaw('id, SUBSTR(title, 1, 60) as title, created_at, updated_at');

        if (! empty($this->searchQuery)) {
            $query->where('title', 'like', '%'.$this->searchQuery.'%');
        }

        return $query
            ->orderByDesc('updated_at')
            ->skip($offset)
            ->take($limit)
            ->get()
            ->map(fn ($conv) => [
                'id' => $conv->id,
                'title' => $conv->title,
                'created_at' => $conv->created_at->format(__('chat.date_format')),
                'updated_at' => $conv->updated_at->format(__('chat.date_format')),
                'updated_at_human' => $conv->updated_at->diffForHumans(),
            ])
            ->all();
    }

    private function loadDraft(): void
    {
        $draft = MessageDraft::where('conversation_id', $this->conversationId)->first();

        if ($draft) {
            $this->message = $draft->content;
        }
    }

    private function clearDraft(): void
    {
        MessageDraft::where('conversation_id', $this->conversationId)->delete();
    }
}
