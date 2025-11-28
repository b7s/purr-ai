# AI Integration

PurrAI integrates with multiple AI providers through PrismPHP, enabling streaming responses with markdown support and multimodal capabilities.

## Architecture Overview

The AI integration consists of several key components:

### 1. Services Layer (`app/Services/Prism/`)

#### SystemPromptBuilder
Constructs the system prompt based on user settings:

```php
$builder = new SystemPromptBuilder();
$systemPrompt = $builder->build();
```

**Settings Used:**
- `mascot_name` - AI assistant name (defaults to app name)
- `user_name` - User's name for personalization
- `user_description` - User profile for context
- `response_detail` - Response length (detailed/short)
- `response_tone` - Tone style (normal/professional/friendly/etc.)
- `respond_as_cat` - Adds playful cat personality

#### ProviderConfig
Manages AI provider configurations and capabilities:

```php
$config = new ProviderConfig();
$parsed = $config->parseSelectedModel('openai:gpt-4');
$provider = $config->getPrismProvider('openai');
$providerConfig = $config->getProviderConfig('openai');
```

**Supported Providers:**
- OpenAI (images)
- Anthropic (images, documents)
- Google Gemini (images, audio, video, documents)
- xAI (images)
- Ollama (images)

#### PrismService
Main service for streaming AI responses:

```php
$service = new PrismService($systemPromptBuilder, $providerConfig);

foreach ($service->streamResponse($selectedModel, $messages) as $chunk) {
    echo $chunk;
}
```

**Features:**
- Streaming text generation
- Multimodal support (images, audio, video, documents)
- Error handling (rate limits, provider errors)
- Automatic message conversion

### 2. Controller Layer

#### ChatStreamController
Handles streaming endpoint `/api/chat/stream`:

```php
POST /api/chat/stream
{
    "conversation_id": 1,
    "selected_model": "openai:gpt-4"
}
```

**Response Format:**
Server-Sent Events (SSE) with JSON data:

```
data: {"chunk": "Hello"}
data: {"chunk": " world"}
data: {"done": true, "message_id": "uuid"}
```

### 3. Frontend Layer

#### chat-stream.js
JavaScript module for consuming streams:

```javascript
// Automatically listens for 'start-ai-stream' event
Livewire.on('start-ai-stream', (params) => {
    streamAIResponse(params.conversationId, params.selectedModel);
});
```

**Features:**
- Real-time markdown rendering with `marked`
- HTML sanitization with `dompurify`
- Auto-scrolling
- Error handling

## Configuration

### AI Provider Setup

Configure providers in Settings → AI Providers:

1. **OpenAI**
   - API Key: `sk-...`
   - Models: `gpt-4, gpt-4-turbo, gpt-3.5-turbo`

2. **Anthropic**
   - API Key: `sk-ant-...`
   - Models: `claude-3-opus, claude-3-sonnet, claude-3-haiku`

3. **Google Gemini**
   - API Key: `AIza...`
   - Models: `gemini-pro, gemini-pro-vision`

4. **xAI**
   - API Key: `abc123...`
   - Models: `grok-beta`

5. **Ollama (Local)**
   - URL: `http://localhost:11434`
   - Models: `llama2, mistral, codellama`

### Chat Settings

Configure AI behavior in Settings → Chat:

- **Mascot Name**: AI assistant name
- **Response Detail**: Detailed or Short
- **Response Tone**: Normal, Professional, Friendly, Frank, Quirky, Efficient, Nerdy, Cynical
- **Respond as Cat**: Adds playful cat personality
- **User Name**: Your name for personalization
- **User Description**: Profile for context-aware responses

## Usage Flow

1. **User sends message** → `Chat.php::sendMessage()`
2. **Message saved to database** → `Message::create()`
3. **Livewire dispatches event** → `start-ai-stream`
4. **JavaScript initiates stream** → `POST /api/chat/stream`
5. **Controller streams response** → `ChatStreamController`
6. **PrismService generates** → Streaming chunks
7. **Frontend renders markdown** → Real-time display
8. **Response saved** → `Message::create()` with role 'assistant'

## Multimodal Support

### Sending Images

```php
use Prism\Prism\ValueObjects\Media\Image;

$image = Image::fromLocalPath('/path/to/image.jpg');
$message = new UserMessage('What is in this image?', [$image]);
```

### Sending Documents

```php
use Prism\Prism\ValueObjects\Media\Document;

$doc = Document::fromLocalPath('/path/to/doc.pdf', 'Document Title');
$message = new UserMessage('Summarize this document', [$doc]);
```

### Sending Audio/Video

```php
use Prism\Prism\ValueObjects\Media\Audio;
use Prism\Prism\ValueObjects\Media\Video;

$audio = Audio::fromLocalPath('/path/to/audio.mp3');
$video = Video::fromLocalPath('/path/to/video.mp4');
```

## Error Handling

The system handles various error scenarios:

### Rate Limiting
```php
catch (PrismRateLimitedException $e) {
    $retryAfter = $e->retryAfter; // seconds
    // Display: "Rate limit exceeded. Please try again in X seconds."
}
```

### Provider Errors
```php
catch (PrismException $e) {
    // Display: "An error occurred: {message}"
}
```

### Configuration Errors
- No model selected
- Invalid provider
- Provider not configured (missing API key)

## Testing

Run tests for AI integration:

```bash
# Stream controller tests
php artisan test tests/Feature/ChatStreamTest.php

# System prompt builder tests
php artisan test tests/Feature/Prism/SystemPromptBuilderTest.php

# All tests
php artisan test
```

## Troubleshooting

### Stream not working
1. Check if model is selected
2. Verify API key is configured
3. Check browser console for errors
4. Verify `/api/chat/stream` endpoint is accessible

### Markdown not rendering
1. Ensure `marked` and `dompurify` are installed: `npm install`
2. Build assets: `npm run build`
3. Check browser console for JS errors

### Provider errors
1. Verify API key is correct
2. Check provider status/quota
3. Review error message in chat
4. Check Laravel logs: `storage/logs/laravel.log`
