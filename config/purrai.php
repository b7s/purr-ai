<?php

declare(strict_types=1);

return [
    'limits' => [
        'max_message_length' => env('PURRAI_MAX_MESSAGE_LENGTH', 10000),
    ],

    'window' => [
        'default_width' => 800,
        'default_height' => 600,
        'min_width' => 480,
        'min_height' => 550,

        'default_x' => 10,
        'default_y' => 10,
        'opacity' => 90,
    ],

    'response_tones' => [
        [
            'value' => 'basic',
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
];
