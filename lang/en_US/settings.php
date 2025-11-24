<?php

declare(strict_types=1);

return [
    'title' => 'Settings',
    'save' => 'Save Settings',
    'saved' => 'Settings saved successfully!',
    'saving' => 'Saving...',
    'auto_save_notice' => 'Changes are saved automatically',

    'tabs' => [
        'chat' => 'Chat',
        'ai_providers' => 'AI Providers',
        'other' => 'Other',
    ],

    'chat' => [
        'title' => 'Chat Settings',
        'mascot_name' => 'Mascot Name',
        'mascot_name_placeholder' => 'Enter mascot name',
        'user_name' => 'Your Name',
        'user_name_placeholder' => 'Enter your name',
        'user_description' => 'About You',
        'user_description_placeholder' => 'Brief description about yourself',
        'response_detail' => 'Response Detail',
        'response_detail_detailed' => 'Detailed',
        'response_detail_short' => 'Short',
        'response_tone' => 'Response Tone',
        'respond_as_cat' => 'Respond like a Cat',
    ],

    'tones' => [
        'basic' => 'Basic (Balanced)',
        'professional' => 'Professional (Precise and refined)',
        'friendly' => 'Friendly (Welcoming and talkative)',
        'frank' => 'Frank (Direct and encouraging)',
        'quirky' => 'Quirky (Fun and creative)',
        'efficient' => 'Efficient (Concise and simple)',
        'nerdy' => 'Nerdy (Exploratory and excited)',
        'cynical' => 'Cynical (Critical and sarcastic)',
    ],

    'ai_providers' => [
        'title' => 'AI Provider Keys',
        'description' => 'Configure your AI provider API keys. Keys are encrypted and stored securely.',
        'openai' => 'OpenAI API Key',
        'openai_placeholder' => 'sk-...',
        'anthropic' => 'Anthropic API Key',
        'anthropic_placeholder' => 'sk-ant-...',
        'google' => 'Google Gemini API Key',
        'google_placeholder' => 'AIza...',
        'ollama' => 'Ollama URL',
        'ollama_placeholder' => 'http://localhost:11434',
    ],

    'other' => [
        'title' => 'Other Settings',
        'delete_old_messages' => 'Delete Old Messages (Days)',
        'delete_old_messages_description' => 'Automatically delete messages older than the specified number of days',
        'delete_old_messages_helper' => 'Enter 0 to disable automatic deletion',
        'window_opacity' => 'Window Opacity',
        'window_opacity_description' => 'Adjust the transparency of the application window',
    ],
];
