<?php

declare(strict_types=1);

return [
    'date_format' => 'm/d/Y H:i',
    'date_format_full' => 'l, d F, Y · H:i',

    'title' => 'PurrAI',
    'welcome_title' => 'Welcome to PurrAI',
    'welcome_message' => 'Your adorable AI companion is ready to help. Start a conversation by typing a message below.',
    'placeholder' => 'Type your message...',
    'send' => 'Send',
    'new_chat' => 'New Chat',
    'history' => 'History',
    'settings' => 'Settings',
    'attach_file' => 'Attach file',
    'take_screenshot' => 'Take screenshot',
    'recent_conversation' => 'Recent conversation :number',
    'created' => 'Created',
    'updated' => 'Updated',
    'no_conversations' => 'No conversations yet',
    'load_more' => 'Load more',
    'edit_title' => 'Edit Conversation Title',
    'title_placeholder' => 'Enter conversation title...',
    'history_title' => 'History',
    'search_history' => 'Search history',
    'search_placeholder' => 'Search conversations...',
    'loading' => 'Loading...',
    'model_selector' => [
        'select_model' => 'Select a model',
        'no_models' => 'No AI models configured',
        'configure_providers' => 'Configure AI Providers',
        'filter_models' => 'Filter models',
        'filter_placeholder' => 'Filter...',
    ],
    'speech_recognition' => [
        'settings' => 'Settings',
        'audio_device' => 'AudioDevice',
        'default_audio_device' => 'Default',
        'speech_provider' => 'Speech Provider',
    ],

    'errors' => [
        'no_model_selected' => 'Please select an AI model before sending a message.',
        'invalid_provider' => 'The selected AI provider is not valid.',
        'provider_not_configured' => 'The selected AI provider is not configured. Please add your API key in Settings.',
        'rate_limited' => 'Rate limit exceeded. Please try again in:retry.',
        'unexpected' => 'An unexpected error occurred: :message',
        'stream_error' => 'An error occurred while streaming the response.',
    ],

    'code' => [
        'copy' => 'Copy code',
        'copied' => 'Copied!',
    ],
];
