<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Setting;
use Livewire\Component;

class Settings extends Component
{
    public string $mascotName = '';

    public string $userName = '';

    public string $userDescription = '';

    public string $responseDetail = 'detailed';

    public string $responseTone = 'basic';

    public bool $respondAsACat = false;

    public string $openaiKey = '';

    public string $anthropicKey = '';

    public string $googleKey = '';

    public string $ollamaUrl = '';

    public int $deleteOldMessagesDays = 0;

    public int $windowOpacity = 95;

    public function mount(): void
    {
        $this->mascotName = Setting::get('mascot_name', config('app.name'));
        $this->userName = Setting::get('user_name', '');
        $this->userDescription = Setting::get('user_description', '');
        $this->responseDetail = Setting::get('response_detail', 'detailed');
        $this->responseTone = Setting::get('response_tone', 'basic');
        $this->respondAsACat = (bool) Setting::get('respond_as_cat', false);

        $this->openaiKey = Setting::getEncrypted('openai_key', '');
        $this->anthropicKey = Setting::getEncrypted('anthropic_key', '');
        $this->googleKey = Setting::getEncrypted('google_key', '');
        $this->ollamaUrl = Setting::get('ollama_url', 'http://localhost:11434');

        $this->deleteOldMessagesDays = (int) Setting::get('delete_old_messages_days', 0);
        $this->windowOpacity = (int) Setting::get('window_opacity', config('purrai.window.opacity'));
    }

    public function save(): void
    {
        Setting::set('mascot_name', $this->mascotName);
        Setting::set('user_name', $this->userName);
        Setting::set('user_description', $this->userDescription);
        Setting::set('response_detail', $this->responseDetail);
        Setting::set('response_tone', $this->responseTone);
        Setting::set('respond_as_cat', $this->respondAsACat);

        if ($this->openaiKey) {
            Setting::setEncrypted('openai_key', $this->openaiKey);
        }
        if ($this->anthropicKey) {
            Setting::setEncrypted('anthropic_key', $this->anthropicKey);
        }
        if ($this->googleKey) {
            Setting::setEncrypted('google_key', $this->googleKey);
        }
        Setting::set('ollama_url', $this->ollamaUrl);

        Setting::set('delete_old_messages_days', $this->deleteOldMessagesDays);
        Setting::set('window_opacity', $this->windowOpacity);

        $this->dispatch('settings-saved');
        $this->dispatch('opacity-changed', opacity: $this->windowOpacity);
    }

    public function updatedWindowOpacity(): void
    {
        $this->save();
    }

    public function updatedResponseDetail(): void
    {
        $this->save();
    }

    public function updatedResponseTone(): void
    {
        $this->save();
    }

    public function updatedRespondAsACat(): void
    {
        $this->save();
    }

    public function updatedDeleteOldMessagesDays(): void
    {
        if ($this->deleteOldMessagesDays < 0) {
            $this->deleteOldMessagesDays = 0;
        }
        $this->save();
    }

    public function render(): mixed
    {
        return view('livewire.settings')->layout('components.layouts.app');
    }
}
