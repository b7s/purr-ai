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

    public function mount(?int $conversationId = null): void
    {
        $this->conversationId = $conversationId;
        $this->conversations = $this->getConversationsHistory();
        $this->selectedModel = Setting::getSelectedModel() ?? '';

        $this->loadDraft();

        $this->dispatch('reset-window-state');
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

        if (! $this->conversationId) {
            $conversation = Conversation::create([
                'title' => substr($this->message, 0, 100),
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
        $this->dispatch('message-sent');
        $this->dispatch('scroll-to-user-message');
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

        return Conversation::query()
            ->selectRaw('id, SUBSTR(title, 1, 60) as title, created_at, updated_at')
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
