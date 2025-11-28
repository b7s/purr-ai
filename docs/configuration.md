# Configuration

PurrAI uses a centralized configuration file `config/purrai.php` for application-wide settings.

## Configuration File Structure

### Location
`config/purrai.php`

## Configuration Sections

### Limits

Controls message and pagination limits:

```php
'limits' => [
    'max_message_length' => env('PURRAI_MAX_MESSAGE_LENGTH', 10000),
    'truncate_words' => 45,
    'conversations_per_page' => 10,
],
```

**Options:**
- `max_message_length` - Maximum characters per message (default: 10,000)
- `truncate_words` - Word limit for message preview truncation (default: 45)
- `conversations_per_page` - Number of conversations per history page (default: 10)

**Environment Variable:**
```env
PURRAI_MAX_MESSAGE_LENGTH=10000
```

### UI Settings

Controls user interface behavior:

```php
'ui' => [
    'show_timestamps' => true,
],
```

**Options:**
- `show_timestamps` - Display message timestamps (default: true)

### Window Settings

Default window dimensions and appearance:

```php
'window' => [
    'main_id' => 'main',
    'default_width' => 1100,
    'default_height' => 618,
    'min_width' => 500,
    'min_height' => 690,
    'default_x' => 10,
    'default_y' => 10,
    'opacity' => 90,
    'blur' => 8,
],
```

**Options:**
- `main_id` - Main window identifier (default: 'main')
- `default_width` - Initial window width in pixels (default: 1100)
- `default_height` - Initial window height in pixels (default: 618)
- `min_width` - Minimum window width (default: 500)
- `min_height` - Minimum window height (default: 690)
- `default_x` - Initial X position (default: 10)
- `default_y` - Initial Y position (default: 10)
- `opacity` - Window opacity percentage (default: 90)
- `blur` - Background blur intensity (default: 8)

### Response Tones

Defines available AI response tone options:

```php
'response_tones' => [
    [
        'value' => 'normal',
        'label' => 'settings.tones.basic',
        'description' => 'settings.tones.basic_description',
        'icon' => 'chat-bubble',
    ],
    [
        'value' => 'professional',
        'label' => 'settings.tones.professional',
        'description' => 'settings.tones.professional_description',
        'icon' => 'suitcase',
    ],
    [
        'value' => 'friendly',
        'label' => 'settings.tones.friendly',
        'description' => 'settings.tones.friendly_description',
        'icon' => 'spock-hand-gesture',
    ],
    [
        'value' => 'frank',
        'label' => 'settings.tones.frank',
        'description' => 'settings.tones.frank_description',
        'icon' => 'message-text',
    ],
    [
        'value' => 'quirky',
        'label' => 'settings.tones.quirky',
        'description' => 'settings.tones.quirky_description',
        'icon' => 'emoji-talking-happy',
    ],
    [
        'value' => 'efficient',
        'label' => 'settings.tones.efficient',
        'description' => 'settings.tones.efficient_description',
        'icon' => 'flash',
    ],
    [
        'value' => 'nerdy',
        'label' => 'settings.tones.nerdy',
        'description' => 'settings.tones.nerdy_description',
        'icon' => 'code',
    ],
    [
        'value' => 'cynical',
        'label' => 'settings.tones.cynical',
        'description' => 'settings.tones.cynical_description',
        'icon' => 'emoji-think-left',
    ],
],
```

**Structure:**
- `value` - Internal identifier
- `label` - Translation key for display name
- `description` - Translation key for description
- `icon` - Iconoir icon name

**Adding Custom Tones:**

1. Add to config array:
```php
[
    'value' => 'custom',
    'label' => 'settings.tones.custom',
    'description' => 'settings.tones.custom_description',
    'icon' => 'icon-name',
],
```

2. Add translations in `lang/en_US/settings.php`:
```php
'tones' => [
    'custom' => 'Custom',
    'custom_description' => 'Your custom tone description',
],
```

### Whisper Configuration

Local speech-to-text settings:

```php
'whisper' => [
    'data_dir' => env('WHISPER_DATA_DIR'),
    'binary_path' => env('WHISPER_BINARY_PATH'),
    'model_path' => env('WHISPER_MODEL_PATH'),
    'ffmpeg_path' => env('FFMPEG_PATH'),
    'model' => env('WHISPER_MODEL', 'base.en'),
],
```

**Options:**
- `data_dir` - Directory for Whisper data files
- `binary_path` - Path to Whisper.cpp binary
- `model_path` - Path to Whisper model file
- `ffmpeg_path` - Path to FFmpeg binary
- `model` - Whisper model to use (base.en, small, medium, large)

**Environment Variables:**
```env
WHISPER_DATA_DIR=/path/to/whisper/data
WHISPER_BINARY_PATH=/path/to/whisper
WHISPER_MODEL_PATH=/path/to/model.bin
FFMPEG_PATH=/path/to/ffmpeg
WHISPER_MODEL=base.en
```

### AI Providers

Defines available AI provider configurations:

```php
'ai_providers' => [
    [
        'key' => 'openai',
        'name' => 'settings.ai_providers.openai',
        'config_key' => 'openai_config',
        'encrypted' => true,
        'fields' => [
            [
                'name' => 'key',
                'type' => 'password',
                'label' => 'settings.ai_providers.openai',
                'placeholder' => 'settings.ai_providers.openai_placeholder',
            ],
            [
                'name' => 'models',
                'type' => 'text',
                'label' => 'settings.ai_providers.openai_models',
                'placeholder' => 'settings.ai_providers.openai_models_placeholder',
                'helper' => 'settings.ai_providers.models_helper',
            ],
        ],
        'models' => [
            'speech_to_text' => [
                'GPT-4o-Transcribe',
                'GPT-4o-Mini-Transcribe',
            ],
            'text' => [],
        ],
    ],
    // ... more providers
],
```

**Provider Structure:**
- `key` - Internal identifier (e.g., 'openai', 'anthropic')
- `name` - Translation key for display name
- `config_key` - Database key for storing configuration
- `encrypted` - Whether to encrypt API keys (true/false)
- `fields` - Array of configuration fields
- `models` - Available models for different capabilities

**Field Structure:**
- `name` - Field identifier ('key', 'models', 'url')
- `type` - Input type ('password', 'text')
- `label` - Translation key for label
- `placeholder` - Translation key for placeholder
- `helper` - Translation key for help text (optional)

**Model Categories:**
- `speech_to_text` - Models for speech recognition
- `text` - Models for text generation

**Supported Providers:**

1. **OpenAI**
   - Key: `openai`
   - Encrypted: Yes
   - Fields: API Key, Models
   - Speech Models: GPT-4o-Transcribe, GPT-4o-Mini-Transcribe

2. **Anthropic**
   - Key: `anthropic`
   - Encrypted: Yes
   - Fields: API Key, Models

3. **Google Gemini**
   - Key: `google`
   - Encrypted: Yes
   - Fields: API Key, Models

4. **xAI**
   - Key: `xai`
   - Encrypted: Yes
   - Fields: API Key, Models

5. **Ollama**
   - Key: `ollama`
   - Encrypted: No
   - Fields: URL, Models

**Adding Custom Providers:**

1. Add to config array:
```php
[
    'key' => 'custom_provider',
    'name' => 'settings.ai_providers.custom',
    'config_key' => 'custom_provider_config',
    'encrypted' => true,
    'fields' => [
        [
            'name' => 'key',
            'type' => 'password',
            'label' => 'settings.ai_providers.custom',
            'placeholder' => 'settings.ai_providers.custom_placeholder',
        ],
        [
            'name' => 'models',
            'type' => 'text',
            'label' => 'settings.ai_providers.custom_models',
            'placeholder' => 'settings.ai_providers.custom_models_placeholder',
            'helper' => 'settings.ai_providers.models_helper',
        ],
    ],
    'models' => [
        'speech_to_text' => [],
        'text' => [],
    ],
],
```

2. Add translations in `lang/en_US/settings.php`:
```php
'ai_providers' => [
    'custom' => 'Custom Provider',
    'custom_placeholder' => 'Enter API key',
    'custom_models' => 'Custom Models',
    'custom_models_placeholder' => 'model-1, model-2',
],
```

3. Add provider support in `app/Services/Prism/ProviderConfig.php`:
```php
private const PROVIDER_MAP = [
    'custom_provider' => [
        'provider' => Provider::CustomProvider,
        'config_key' => 'custom_provider_config',
        'encrypted' => true,
        'api_key_field' => 'key',
    ],
];

private const MEDIA_SUPPORT = [
    'custom_provider' => ['image', 'audio'],
];
```

## Accessing Configuration

### In PHP

```php
// Get configuration value
$maxLength = config('purrai.limits.max_message_length');
$tones = config('purrai.response_tones');
$providers = config('purrai.ai_providers');

// Get with default
$opacity = config('purrai.window.opacity', 90);
```

### In Blade

```blade
{{ config('purrai.limits.max_message_length') }}
@foreach(config('purrai.response_tones') as $tone)
    {{ __($tone['label']) }}
@endforeach
```

### In JavaScript

Pass via Blade:
```blade
<script>
    window.maxMessageLength = @js(config('purrai.limits.max_message_length'));
    window.responseTones = @js(config('purrai.response_tones'));
</script>
```

## Environment Variables

Create `.env` file from `.env.example`:

```bash
cp .env.example .env
```

**Key Variables:**
```env
APP_NAME=PurrAI
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

# Message Limits
PURRAI_MAX_MESSAGE_LENGTH=10000

# Whisper Configuration
WHISPER_DATA_DIR=
WHISPER_BINARY_PATH=
WHISPER_MODEL_PATH=
FFMPEG_PATH=
WHISPER_MODEL=base.en
```

## Caching

Configuration is cached in production for performance:

```bash
# Cache configuration
php artisan config:cache

# Clear configuration cache
php artisan config:clear
```

**Note:** Always clear cache after modifying config files in production.

## Best Practices

1. **Use environment variables** for sensitive or environment-specific values
2. **Keep defaults** in config file for development
3. **Document custom settings** with comments
4. **Version control** config files, not `.env`
5. **Cache in production** for better performance
6. **Clear cache** after config changes
