<?php

declare(strict_types=1);

return [
    'limits' => [
        'max_message_length' => env('PURRAI_MAX_MESSAGE_LENGTH', 10000),
        'max_attachments_per_message' => env('PURRAI_MAX_ATTACHMENTS', 10),
        'max_attachment_size' => env('PURRAI_MAX_ATTACHMENT_SIZE', 10485760), // 10MB
        'allowed_mime_types' => [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
            'application/pdf',
            'text/plain',
            'text/markdown',
            'application/json',
        ],
    ],

    'ui' => [
        'theme' => env('PURRAI_THEME', 'auto'), // auto, light, dark
        'messages_per_page' => env('PURRAI_MESSAGES_PER_PAGE', 50),
        'show_timestamps' => env('PURRAI_SHOW_TIMESTAMPS', true),
    ],

    'ai' => [
        'default_provider' => env('PURRAI_DEFAULT_PROVIDER', null),
        'default_model' => env('PURRAI_DEFAULT_MODEL', null),
        'temperature' => env('PURRAI_TEMPERATURE', 0.7),
        'max_tokens' => env('PURRAI_MAX_TOKENS', 2000),
    ],

    'storage' => [
        'attachments_disk' => env('PURRAI_ATTACHMENTS_DISK', 'local'),
        'attachments_path' => env('PURRAI_ATTACHMENTS_PATH', 'attachments'),
    ],

    'window' => [
        'default_width' => 800,
        'default_height' => 600,
        'min_width' => 480,
        'min_height' => 550,
    ],
];
