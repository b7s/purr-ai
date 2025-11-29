<?php

declare(strict_types=1);

namespace App\Services\Prism;

use App\Models\Setting;
use Carbon\Carbon;

class SystemPromptBuilder
{
    private string $mascotName;

    private string $userName;

    private string $userDescription;

    private string $responseDetail;

    private string $responseTone;

    private bool $respondAsCat;

    public function __construct()
    {
        $this->mascotName = Setting::get('mascot_name', '') ?: config('app.name');
        $this->userName = Setting::get('user_name', '');
        $this->userDescription = Setting::get('user_description', '');
        $this->responseDetail = Setting::get('response_detail', 'detailed');
        $this->responseTone = Setting::get('response_tone', 'normal');
        $this->respondAsCat = (bool) Setting::get('respond_as_cat', false);
    }

    public function build(): string
    {
        $parts = [];

        $parts[] = $this->buildIdentityPrompt();
        $parts[] = $this->buildResponseStylePrompt();

        if ($this->respondAsCat) {
            $parts[] = $this->buildCatPersonalityPrompt();
        }

        if (! empty($this->userDescription)) {
            $parts[] = $this->buildUserProfilePrompt();
        }

        $parts[] = $this->buildGeneralInstructions();
        $parts[] = $this->buildMissingProfileInstructions();
        $parts[] = $this->extraInfo();

        return implode("\n\n", array_filter($parts));
    }

    private function buildIdentityPrompt(): string
    {
        $prompt = "You are {$this->mascotName}, a helpful and friendly AI assistant mascot.";

        if (! empty($this->userName)) {
            $prompt .= " You are always ready to help your owner and tutor, {$this->userName} (his/her name), with whatever they need.";
        }

        return $prompt;
    }

    private function buildResponseStylePrompt(): string
    {
        $detailDescription = $this->getDetailDescription();
        $toneDescription = $this->getToneDescription();

        return "You should respond in a {$detailDescription} manner with a {$toneDescription} tone.";
    }

    private function buildCatPersonalityPrompt(): string
    {
        return 'You have a playful cat personality! Occasionally incorporate cat-like expressions such as "meow", "purr", or "*stretches*" into your responses. You might mention cat-related things like napping in sunbeams, chasing laser pointers, or knocking things off tables. Keep it subtle and charming, not overwhelming.';
    }

    private function buildUserProfilePrompt(): string
    {
        return "Your owner's profile: {$this->userDescription}. Use this information to personalize your responses when relevant.";
    }

    private function buildGeneralInstructions(): string
    {
        return 'Always be helpful, accurate, and respectful. If you are unsure about something, say so. Format your responses using Markdown when appropriate for better readability.';
    }

    private function buildMissingProfileInstructions(): string
    {
        $missing = [];

        if (empty($this->userName)) {
            $missing[] = 'name';
        }

        if (empty($this->userDescription)) {
            $missing[] = 'description';
        }

        if (empty($missing)) {
            return '';
        }

        return 'IMPORTANT: If the user profile is incomplete (missing: '.implode(', ', $missing).'). '.
            'When the user asks personal questions like "what is my name?" or similar, '.
            'use the user_profile tool with action "get" first to check current data, '.
            'then politely ask them to provide the missing information. '.
            'Once they provide it, use the user_profile tool with action "update" to save it.'.
            'Inform him that he can adjust the options by going to "Settings".';
    }

    private function getDetailDescription(): string
    {
        return match ($this->responseDetail) {
            'short' => 'concise and brief',
            'detailed' => 'detailed and comprehensive',
            default => 'balanced',
        };
    }

    private function getToneDescription(): string
    {
        $tones = config('purrai.response_tones', []);

        foreach ($tones as $tone) {
            if ($tone['value'] === $this->responseTone) {
                $label = __($tone['label']);
                $description = __($tone['description']);

                return "{$label} ({$description})";
            }
        }

        return 'normal (balanced)';
    }

    private function extraInfo(): string
    {
        $datetime = Carbon::now(date_default_timezone_get())->toIso8601String();

        return "User's datetime now in ISO 8601 format is: $datetime.".
            'Format the returned text with markdown in important places with bold, italics, link, quote, etc.'.
            'Never return technical system information, such as column names, variables, functions, etc.';
    }
}
