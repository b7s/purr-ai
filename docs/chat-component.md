# Chat Component

The Chat component (`app/Livewire/Chat.php`) is the main interface for user interactions with the AI assistant.

## Component Structure

### File Location
- **Livewire Component**: `app/Livewire/Chat.php`
- **Blade View**: `resources/views/livewire/chat.blade.php`
- **Route**: `/{conversationId?}` (named: `chat`)

## Properties

### Public Properties

```php
public ?int $conversationId = null;        // Current conversation ID
public string $message = '';                // User input message
public array $attachments = [];             // File attachments
public array $conversations = [];           // Conversation history
public ?int $editingConversationId = null; // Conversation being edited
public string $editingTitle = '';          // New title during edit
public int $currentPage = 1;               // Pagination page
public string $selectedModel = '';         // Selected AI model
public string $searchQuery = '';           // History search query
public bool $isProcessing = false;         // AI response in progress
```

## Methods

### mount(?int $conversationId = null)
Initializes the component with optional conversation ID.

```php
public function mount(?int $conversationId = null): void
{
    $this->conversationId = $conversationId;
    $this->conversations = $this->getConversationsHistory();
    $this->selectedModel = Setting::getSelectedModel() ?? '';
    $this->loadDraft();
    $this->dispatch('reset-window-state');
}
```

### sendMessage()
Handles message submission and initiates AI streaming.

```php
public function sendMessage(): void
{
    // 1. Validate message
    $this->validate([
        'message' => 'required|string|max:'.config('purrai.limits.max_message_length'),
    ]);

    // 2. Create conversation if needed
    if (!$this->conversationId) {
        $conversation = Conversation::create([
            'title' => mb_substr($this->message, 0, 100),
        ]);
        $this->conversationId = $conversation->id;
    }

    // 3. Save user message
    Message::create([
        'conversation_id' => $this->conversationId,
        'role' => 'user',
        'content' => $this->message,
    ]);

    // 4. Clear draft and reset
    $this->clearDraft();
    $this->message = '';
    $this->isProcessing = true;

    // 5. Dispatch events
    $this->dispatch('message-sent');
    $this->dispatch('scroll-to-user-message');
    $this->dispatch('start-ai-stream', [
        'conversationId' => $this->conversationId,
        'selectedModel' => $this->selectedModel,
    ]);
}
```

### streamComplete()
Called when AI response streaming finishes.

```php
public function streamComplete(): void
{
    $this->isProcessing = false;
}
```

### newConversation()
Starts a new conversation.

```php
public function newConversation(): void
{
    $this->clearDraft();
    $this->conversationId = null;
    $this->message = '';
    response()->redirectToRoute('chat');
}
```

### loadConversation(int $conversationId)
Loads an existing conversation from history.

```php
public function loadConversation(int $conversationId): void
{
    $this->conversationId = $conversationId;
    $this->loadDraft();
    $this->redirect(route('chat', ['conversationId' => $conversationId]));
}
```

### saveTitleDirect(int $conversationId, string $title)
Updates conversation title without reloading.

```php
public function saveTitleDirect(int $conversationId, string $title): bool
{
    $title = trim($title);
    
    if (empty($title) || strlen($title) > 255) {
        return false;
    }

    $conversation = Conversation::find($conversationId);
    if (!$conversation) {
        return false;
    }

    $conversation->timestamps = false;
    $conversation->update(['title' => $title]);
    $conversation->timestamps = true;

    return true;
}
```

### Draft Management

#### saveDraft()
Saves current message as draft.

```php
public function saveDraft(): void
{
    $content = trim($this->message);

    if (empty($content)) {
        $this->clearDraft();
        return;
    }

    MessageDraft::updateOrCreate(
        ['conversation_id' => $this->conversationId],
        ['content' => $content]
    );
}
```

#### loadDraft()
Loads saved draft for current conversation.

```php
private function loadDraft(): void
{
    $draft = MessageDraft::where('conversation_id', $this->conversationId)->first();
    
    if ($draft) {
        $this->message = $draft->content;
    }
}
```

#### clearDraft()
Removes draft for current conversation.

```php
private function clearDraft(): void
{
    MessageDraft::where('conversation_id', $this->conversationId)->delete();
}
```

### History Management

#### getConversationsHistory(?int $limit = null)
Retrieves paginated conversation history.

```php
private function getConversationsHistory(?int $limit = null): array
{
    $limit ??= config('purrai.limits.conversations_per_page');
    $currentPage = max(1, $this->currentPage);
    $offset = ($currentPage - 1) * $limit;

    $query = Conversation::query()
        ->selectRaw('id, SUBSTR(title, 1, 60) as title, created_at, updated_at');

    if (!empty($this->searchQuery)) {
        $query->where('title', 'like', "%{$this->searchQuery}%");
    }

    return $query
        ->orderByDesc('updated_at')
        ->skip($offset)
        ->take($limit)
        ->get()
        ->map(fn($conv) => [
            'id' => $conv->id,
            'title' => $conv->title,
            'created_at' => $conv->created_at->format(__('chat.date_format')),
            'updated_at' => $conv->updated_at->format(__('chat.date_format')),
            'updated_at_human' => $conv->updated_at->diffForHumans(),
        ])
        ->toArray();
}
```

#### loadMoreHistoryConversations()
Loads next page of conversations.

```php
public function loadMoreHistoryConversations(): array
{
    $this->currentPage++;
    $newConversations = $this->getConversationsHistory();
    $this->conversations = [...$this->conversations, ...$newConversations];

    return $newConversations;
}
```

#### updatedSearchQuery()
Reacts to search query changes.

```php
public function updatedSearchQuery(): void
{
    $this->currentPage = 1;
    $this->conversations = $this->getConversationsHistory();
    $this->dispatch('conversations-updated', conversations: $this->conversations);
}
```

## View Structure

### Main Sections

1. **Header Actions** - New chat and history buttons
2. **Messages Container** - Displays conversation messages
3. **Streaming Container** - Shows AI response in real-time
4. **Input Dock** - Message input and controls

### Alpine.js Data

```javascript
x-data="{
    scrollToBottom() {
        let container = document.getElementById('messages-container');
        if (container) {
            setTimeout(() => {
                container.scrollTo({
                    top: container.scrollHeight,
                    behavior: 'smooth'
                });
            }, 50);
        }
    },
    focusInput() {
        let textarea = document.querySelector('.input-field');
        if (textarea) {
            setTimeout(() => {
                textarea.focus();
                this.scrollToBottom();
            }, 150);
        }
    }
}"
```

### History Dropdown

Alpine.js component for conversation history:

```javascript
x-data="historyDropdown(@js($conversations), {{ $hasMorePages ? 'true' : 'false' }}, @js($searchQuery))"
```

**Features:**
- Search conversations
- Load more pagination
- Edit conversation titles
- Navigate to conversations

### Message Input

Auto-resizing textarea with draft saving:

```javascript
x-data="{
    maxHeight: 100,
    adjustHeight() {
        const textarea = $refs.messageInput;
        const container = $refs.textareaContainer;
        if (!textarea || !container) return;
        textarea.style.height = 'auto';
        const newHeight = Math.min(textarea.scrollHeight, this.maxHeight);
        textarea.style.height = newHeight + 'px';
        container.style.height = newHeight + 'px';
        textarea.style.overflowY = textarea.scrollHeight > this.maxHeight ? 'auto' : 'hidden';
    },
    syncValue() {
        const textarea = $refs.messageInput;
        if (!textarea) return;
        $wire.set('message', textarea.value);
        this.adjustHeight();
    }
}"
```

## Events

### Dispatched Events

- `reset-window-state` - Resets window state on mount
- `message-sent` - Triggered after user message is saved
- `scroll-to-user-message` - Scrolls to latest message
- `start-ai-stream` - Initiates AI response streaming
- `conversations-updated` - Updates history list

### Listened Events

- `scroll-to-user-message.window` - Scrolls to bottom

## Validation Rules

```php
[
    'message' => 'required|string|max:' . config('purrai.limits.max_message_length'),
]
```

Default max length: 10,000 characters

## Configuration

### purrai.php

```php
'limits' => [
    'max_message_length' => env('PURRAI_MAX_MESSAGE_LENGTH', 10000),
    'truncate_words' => 45,
    'conversations_per_page' => 10,
],

'ui' => [
    'show_timestamps' => true,
],
```

## Usage Examples

### Basic Usage

```php
// Navigate to chat
<a href="{{ route('chat') }}" wire:navigate>Chat</a>

// Navigate to specific conversation
<a href="{{ route('chat', ['conversationId' => 1]) }}" wire:navigate>View Conversation</a>
```

### Programmatic Control

```javascript
// Start new conversation
Livewire.find(componentId).call('newConversation');

// Load conversation
Livewire.find(componentId).call('loadConversation', conversationId);

// Send message
Livewire.find(componentId).set('message', 'Hello');
Livewire.find(componentId).call('sendMessage');
```

## Testing

```php
it('can send a message', function () {
    $this->livewire(Chat::class)
        ->set('message', 'Hello AI')
        ->call('sendMessage')
        ->assertSet('message', '')
        ->assertSet('isProcessing', true);
});

it('creates conversation on first message', function () {
    $this->livewire(Chat::class)
        ->set('message', 'First message')
        ->call('sendMessage');

    expect(Conversation::count())->toBe(1);
    expect(Message::count())->toBe(1);
});
```
