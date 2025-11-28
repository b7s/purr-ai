<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\MessageDraft;
use App\Models\Setting;
use Livewire\Component;
use Livewire\WithFileUploads;

class Chat extends Component
{
    use WithFileUploads;

    public ?int $conversationId = null;

    public string $message = '';

    /** @var array<int, mixed> */
    public array $attachments = [];

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

    public function sendMessage(): void
    {
        $this->message = trim($this->message);

        $this->validate([
            'message' => 'required|string|max:'.config('purrai.limits.max_message_length'),
        ]);

        // Validate model is selected
        if (empty($this->selectedModel)) {
            $this->addError('message', __('chat.errors.no_model_selected'));

            return;
        }

        if (! $this->conversationId) {
            $conversation = Conversation::create([
                'title' => mb_substr($this->message, 0, 100),
            ]);

            $this->conversationId = $conversation->id;
        }

        Message::create([
            'conversation_id' => $this->conversationId,
            'role' => 'user',
            'content' => $this->message,
        ]);

        $this->clearDraft();
        $this->message = '';
        $this->isProcessing = true;

        $this->dispatch('message-sent');
        $this->dispatch('scroll-to-user-message');
        $this->dispatch('start-ai-stream', [
            'conversationId' => $this->conversationId,
            'selectedModel' => $this->selectedModel,
        ]);
    }

    public function streamComplete(): void
    {
        $this->isProcessing = false;
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
            ->toArray();
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
