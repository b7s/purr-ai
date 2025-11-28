# Settings Component

The Settings component (`app/Livewire/Settings.php`) manages all application configuration including AI providers, chat behavior, speech recognition, and UI preferences.

## Component Structure

### File Location
- **Livewire Component**: `app/Livewire/Settings.php`
- **Blade View**: `resources/views/livewire/settings.blade.php`
- **Route**: `/settings` (named: `settings`)

## Properties

### Chat Settings

```php
public string $mascotName = '';           // AI assistant name
public string $userName = '';             // User's name
public string $userDescription = '';      // User profile description
public string $responseDetail = 'detailed'; // Response length
public string $responseTone = 'basic';    // Response tone style
public bool $respondAsACat = false;       // Cat personality mode
```

### AI Provider Settings

```php
public array $providers = [];             // Provider configurations
// Structure: ['openai' => ['key' => '...', 'models' => '...']]
```

### Speech Recognition Settings

```php
public bool $speechToTextEnabled = false;     // Enable speech input
public bool $useLocalSpeech = true;           // Use local Whisper
public string $speechProvider = '';           // Cloud provider
public string $noiseSuppressionLevel = 'medium'; // Noise filtering
public bool $autoSendAfterTranscription = false; // Auto-send
public array $whisperStatus = [];            // Whisper setup status
public bool $isDownloadingWhisper = false;   // Download in progress
public string $downloadProgress = '';         // Download status
public ?string $downloadError = null;         // Download error
```

### UI Settings

```php
public int $deleteOldMessagesDays = 0;    // Auto-delete threshold
public int $windowOpacity = 90;           // Window transparency
public int $windowBlur = 8;               // Background blur
public bool $disableTransparencyMaximized = true; // Disable when maximized
public string $themeMode = 'automatic';   // Theme: light/dark/automatic
public bool $openAtLogin = false;         // Auto-start on login
```

## Methods

### mount()
Initializes component with current settings.

```php
public function mount(): void
{
    $this->checkWhisperStatus();
    $this->speechToTextEnabled = (bool) Setting::get('speech_to_text_enabled', false);
    $this->useLocalSpeech = (bool) Setting::get('use_local_speech', true);
    $this->speechProvider = Setting::get('speech_provider', '');
    $this->noiseSuppressionLevel = Setting::get('noise_suppression_level', 'medium');
    $this->mascotName = Setting::get('mascot_name', config('app.name'));
    $this->userName = Setting::get('user_name', '');
    $this->userDescription = Setting::get('user_description', '');
    $this->responseDetail = Setting::get('response_detail', 'detailed');
    $this->responseTone = Setting::get('response_tone', 'normal');
    $this->respondAsACat = (bool) Setting::get('respond_as_cat', false);
    
    $this->loadProviders();
    
    $this->deleteOldMessagesDays = (int) Setting::get('delete_old_messages_days', 0);
    $this->windowOpacity = (int) Setting::get('window_opacity', config('purrai.window.opacity'));
    $this->windowBlur = (int) Setting::get('window_blur', config('purrai.window.blur', 48));
    $this->disableTransparencyMaximized = (bool) Setting::get('disable_transparency_maximized', true);
    $this->themeMode = Setting::get('theme_mode', 'automatic');
    $this->openAtLogin = (bool) Setting::get('open_at_login', false);
    $this->autoSendAfterTranscription = (bool) Setting::get('auto_send_after_transcription', false);
}
```

### save()
Persists all settings to database.

```php
public function save(): void
{
    Setting::set('mascot_name', $this->mascotName);
    Setting::set('user_name', $this->userName);
    Setting::set('user_description', $this->userDescription);
    Setting::set('response_detail', $this->responseDetail);
    Setting::set('response_tone', $this->responseTone);
    Setting::set('respond_as_cat', $this->respondAsACat);
    
    $this->saveProviders();
    
    Setting::set('delete_old_messages_days', $this->deleteOldMessagesDays);
    Setting::set('window_opacity', $this->windowOpacity);
    Setting::set('window_blur', $this->windowBlur);
    Setting::set('disable_transparency_maximized', $this->disableTransparencyMaximized);
    Setting::set('theme_mode', $this->themeMode);
    Setting::set('open_at_login', $this->openAtLogin);
    Setting::set('speech_to_text_enabled', $this->speechToTextEnabled);
    Setting::set('use_local_speech', $this->useLocalSpeech);
    Setting::set('speech_provider', $this->speechProvider);
    Setting::set('noise_suppression_level', $this->noiseSuppressionLevel);
    Setting::set('auto_send_after_transcription', $this->autoSendAfterTranscription);
    
    $this->dispatch('settings-saved');
    $this->dispatch('opacity-changed', opacity: $this->windowOpacity);
    $this->dispatch('blur-changed', blur: $this->windowBlur);
    $this->dispatch('theme-changed', theme: $this->themeMode);
}
```

### Provider Management

#### loadProviders()
Loads AI provider configurations from database.

```php
private function loadProviders(): void
{
    $providersConfig = config('purrai.ai_providers', []);
    
    foreach ($providersConfig as $providerConfig) {
        $configKey = $providerConfig['config_key'];
        $encrypted = $providerConfig['encrypted'];
        
        $data = $encrypted
            ? Setting::getJsonDecrypted($configKey, [])
            : Setting::getJson($configKey, []);
        
        $this->providers[$providerConfig['key']] = [];
        
        foreach ($providerConfig['fields'] as $field) {
            $fieldName = $field['name'];
            $value = $data[$fieldName] ?? '';
            
            if ($fieldName === 'models' && is_array($value)) {
                $value = implode(', ', $value);
            } elseif ($fieldName === 'key' && !empty($value)) {
                $value = self::$fakeKey; // Mask API key
            }
            
            $this->providers[$providerConfig['key']][$fieldName] = $value;
        }
    }
}
```

#### saveProviders()
Saves AI provider configurations to database.

```php
private function saveProviders(): void
{
    $providersConfig = config('purrai.ai_providers', []);
    
    foreach ($providersConfig as $providerConfig) {
        $configKey = $providerConfig['config_key'];
        $encrypted = $providerConfig['encrypted'];
        $providerKey = $providerConfig['key'];
        
        $data = [];
        
        foreach ($providerConfig['fields'] as $field) {
            $fieldName = $field['name'];
            $value = $this->providers[$providerKey][$fieldName] ?? '';
            
            if ($fieldName === 'key') {
                if ($value === self::$fakeKey) {
                    // Keep existing key if not changed
                    $existingConfig = $encrypted
                        ? Setting::getJsonDecrypted($configKey, [])
                        : Setting::getJson($configKey, []);
                    $value = $existingConfig['key'] ?? '';
                }
                $data[$fieldName] = $value;
            } elseif ($fieldName === 'models') {
                $data[$fieldName] = $this->parseModels($value);
            } else {
                $data[$fieldName] = $value;
            }
        }
        
        if ($encrypted) {
            Setting::setJsonEncrypted($configKey, $data);
        } else {
            Setting::setJson($configKey, $data);
        }
        
        Setting::query()->where('key', $configKey)->update(['is_ai_provider' => true]);
    }
}
```

#### parseModels(string $models)
Converts comma-separated models string to array.

```php
private function parseModels(string $models): array
{
    if (empty(trim($models))) {
        return [];
    }
    
    return array_values(array_filter(array_map(
        fn($model) => trim($model),
        explode(',', $models)
    )));
}
```

### Whisper Management

#### checkWhisperStatus()
Checks Whisper.cpp installation status.

```php
public function checkWhisperStatus(): void
{
    try {
        $whisperService = app(WhisperService::class);
        $this->whisperStatus = $whisperService->getStatus();
    } catch (\Throwable $e) {
        $this->whisperStatus = [
            'binary' => false,
            'model' => false,
            'ffmpeg' => false,
            'gpu' => false,
        ];
    }
}
```

#### downloadWhisper()
Downloads Whisper.cpp components.

```php
public function downloadWhisper(): void
{
    $this->isDownloadingWhisper = true;
    $this->downloadError = null;
    $this->downloadProgress = __('settings.other.download_starting');
    
    try {
        $whisperService = app(WhisperService::class);
        $status = $whisperService->getStatus();
        
        // Download FFmpeg
        if (!$status['ffmpeg']) {
            $this->downloadProgress = __('settings.other.downloading_ffmpeg');
            $this->dispatch('progress-updated');
            
            if (!$whisperService->downloadFfmpeg()) {
                throw new \Exception(__('settings.other.ffmpeg_download_failed'));
            }
        }
        
        // Download Whisper binary
        if (!$status['binary']) {
            $this->downloadProgress = __('settings.other.downloading_whisper_binary');
            $this->dispatch('progress-updated');
            
            if (!$whisperService->downloadBinary()) {
                throw new \Exception(__('settings.other.whisper_binary_download_failed'));
            }
        }
        
        // Download model
        if (!$status['model']) {
            $this->downloadProgress = __('settings.other.downloading_whisper_model');
            $this->dispatch('progress-updated');
            
            if (!$whisperService->downloadModel()) {
                throw new \Exception(__('settings.other.whisper_model_download_failed'));
            }
        }
        
        $this->downloadProgress = __('settings.other.download_complete');
        $this->checkWhisperStatus();
        $this->dispatch('whisper-setup-complete');
    } catch (\Exception $e) {
        $this->downloadError = $e->getMessage();
        $this->downloadProgress = __('settings.other.download_failed');
        $this->dispatch('whisper-setup-failed', message: $e->getMessage());
    } finally {
        $this->isDownloadingWhisper = false;
    }
}
```

### Auto-Save Hooks

Settings are automatically saved when properties change:

```php
public function updatedWindowOpacity(): void { $this->save(); }
public function updatedWindowBlur(): void { $this->save(); }
public function updatedResponseDetail(): void { $this->save(); }
public function updatedResponseTone(): void { $this->save(); }
public function updatedRespondAsACat(): void { $this->save(); }
public function updatedDeleteOldMessagesDays(): void { $this->save(); }
public function updatedDisableTransparencyMaximized(): void { $this->save(); }
public function updatedThemeMode(): void { $this->save(); }
public function updatedProviders(): void { $this->save(); }
public function updatedMascotName(): void { $this->save(); }
public function updatedUserName(): void { $this->save(); }
public function updatedSpeechToTextEnabled(): void { $this->save(); }
public function updatedUseLocalSpeech(): void { $this->save(); }
public function updatedSpeechProvider(): void { $this->save(); }
public function updatedNoiseSuppressionLevel(): void { $this->save(); }
public function updatedUserDescription(): void { $this->save(); }
public function updatedAutoSendAfterTranscription(): void { $this->save(); }

public function updatedOpenAtLogin(): void
{
    if (!is_linux()) {
        $this->save();
        if (class_exists(App::class)) {
            App::openAtLogin($this->openAtLogin);
        }
    }
}
```

## View Structure

### Tabs

Settings are organized into three tabs:

1. **Chat** - AI behavior and speech recognition
2. **AI Providers** - API keys and models
3. **Other** - UI preferences and system settings

### Tab Navigation

```php
$tabs = [
    'chat' => ['label' => __('settings.tabs.chat')],
    'ai_providers' => ['label' => __('settings.tabs.ai_providers'), 'icon' => 'sparks'],
    'other' => ['label' => __('settings.tabs.other')],
];
```

Access via URL: `/settings?tab=chat`

## Configuration

### purrai.php - Response Tones

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
    // ... more tones
],
```

### purrai.php - AI Providers

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
            'speech_to_text' => ['GPT-4o-Transcribe', 'GPT-4o-Mini-Transcribe'],
            'text' => [],
        ],
    ],
    // ... more providers
],
```

## Events

### Dispatched Events

- `settings-saved` - Triggered after save
- `opacity-changed` - Window opacity updated
- `blur-changed` - Window blur updated
- `theme-changed` - Theme mode changed
- `transparency-setting-changed` - Transparency setting changed
- `progress-updated` - Whisper download progress
- `whisper-setup-complete` - Whisper setup finished
- `whisper-setup-failed` - Whisper setup error

## Usage Examples

### Accessing Settings

```php
// Get setting value
$mascotName = Setting::get('mascot_name', 'PurrAI');

// Set setting value
Setting::set('mascot_name', 'MyBot');

// Encrypted settings
$apiKey = Setting::getEncrypted('openai_api_key');
Setting::setEncrypted('openai_api_key', 'sk-...');

// JSON settings
$config = Setting::getJson('openai_config', []);
Setting::setJson('openai_config', ['key' => '...', 'models' => [...]]);

// JSON with encryption
$config = Setting::getJsonDecrypted('openai_config', []);
Setting::setJsonEncrypted('openai_config', ['key' => '...', 'models' => [...]]);
```

### Programmatic Control

```javascript
// Navigate to settings
window.location.href = '/settings';

// Navigate to specific tab
window.location.href = '/settings?tab=ai_providers';

// Update setting via Livewire
Livewire.find(componentId).set('mascotName', 'NewName');
```

## Testing

```php
it('settings can be saved', function () {
    $this->livewire(Settings::class)
        ->set('mascotName', 'TestBot')
        ->call('save');
    
    expect(Setting::get('mascot_name'))->toBe('TestBot');
});

it('encrypted settings can be saved and retrieved', function () {
    Setting::setEncrypted('test_key', 'secret_value');
    
    expect(Setting::getEncrypted('test_key'))->toBe('secret_value');
});
```

## Security

- API keys are encrypted using Laravel's `Crypt` facade
- Keys are masked in UI with fake placeholder
- Only changed keys are saved (prevents overwriting with mask)
- All settings are stored server-side (not in localStorage)
