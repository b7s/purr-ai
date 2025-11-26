<?php

declare(strict_types=1);

return [
    'limits' => [
        'max_message_length' => env('PURRAI_MAX_MESSAGE_LENGTH', 10000),
        'truncate_words' => 45,
        'conversations_per_page' => 10,
    ],

    'ui' => [
        'show_timestamps' => true,
    ],

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
        ],
        [
            'key' => 'anthropic',
            'name' => 'settings.ai_providers.anthropic',
            'config_key' => 'anthropic_config',
            'encrypted' => true,
            'fields' => [
                [
                    'name' => 'key',
                    'type' => 'password',
                    'label' => 'settings.ai_providers.anthropic',
                    'placeholder' => 'settings.ai_providers.anthropic_placeholder',
                ],
                [
                    'name' => 'models',
                    'type' => 'text',
                    'label' => 'settings.ai_providers.anthropic_models',
                    'placeholder' => 'settings.ai_providers.anthropic_models_placeholder',
                    'helper' => 'settings.ai_providers.models_helper',
                ],
            ],
        ],
        [
            'key' => 'google',
            'name' => 'settings.ai_providers.google',
            'config_key' => 'google_config',
            'encrypted' => true,
            'fields' => [
                [
                    'name' => 'key',
                    'type' => 'password',
                    'label' => 'settings.ai_providers.google',
                    'placeholder' => 'settings.ai_providers.google_placeholder',
                ],
                [
                    'name' => 'models',
                    'type' => 'text',
                    'label' => 'settings.ai_providers.google_models',
                    'placeholder' => 'settings.ai_providers.google_models_placeholder',
                    'helper' => 'settings.ai_providers.models_helper',
                ],
            ],
        ],
        [
            'key' => 'ollama',
            'name' => 'settings.ai_providers.ollama',
            'config_key' => 'ollama_config',
            'encrypted' => false,
            'fields' => [
                [
                    'name' => 'url',
                    'type' => 'text',
                    'label' => 'settings.ai_providers.ollama',
                    'placeholder' => 'settings.ai_providers.ollama_placeholder',
                ],
                [
                    'name' => 'models',
                    'type' => 'text',
                    'label' => 'settings.ai_providers.ollama_models',
                    'placeholder' => 'settings.ai_providers.ollama_models_placeholder',
                    'helper' => 'settings.ai_providers.models_helper',
                ],
            ],
        ],
    ],
];
