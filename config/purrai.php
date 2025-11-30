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
        'opacity' => 92,
        'blur' => 8,
    ],

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

    /**
     * Whisper.cpp: Local speech-to-text provider.
     * Available models: 'base', 'small', 'medium', 'large'
     */
    'whisper' => [
        'data_dir' => env('WHISPER_DATA_DIR'),
        'binary_path' => env('WHISPER_BINARY_PATH'),
        'model_path' => env('WHISPER_MODEL_PATH'),
        'ffmpeg_path' => env('FFMPEG_PATH'),
        'model' => env('WHISPER_MODEL', 'base.en'),
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
                    'label' => 'settings.ai_providers.text_models',
                    'placeholder' => 'settings.ai_providers.openai_models_placeholder',
                    'helper' => 'settings.ai_providers.models_helper',
                ],
                [
                    'name' => 'models_image',
                    'type' => 'text',
                    'label' => 'settings.ai_providers.image_models',
                    'placeholder' => 'settings.ai_providers.image_models_placeholder',
                    'helper' => 'settings.ai_providers.models_helper',
                ],
                [
                    'name' => 'models_audio',
                    'type' => 'text',
                    'label' => 'settings.ai_providers.audio_models',
                    'placeholder' => 'settings.ai_providers.audio_models_placeholder',
                    'helper' => 'settings.ai_providers.models_helper',
                ],
                [
                    'name' => 'models_video',
                    'type' => 'text',
                    'label' => 'settings.ai_providers.video_models',
                    'placeholder' => 'settings.ai_providers.video_models_placeholder',
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
                    'label' => 'settings.ai_providers.text_models',
                    'placeholder' => 'settings.ai_providers.anthropic_models_placeholder',
                    'helper' => 'settings.ai_providers.models_helper',
                ],
                [
                    'name' => 'models_image',
                    'type' => 'text',
                    'label' => 'settings.ai_providers.image_models',
                    'placeholder' => 'settings.ai_providers.image_models_placeholder',
                    'helper' => 'settings.ai_providers.models_helper',
                ],
                [
                    'name' => 'models_audio',
                    'type' => 'text',
                    'label' => 'settings.ai_providers.audio_models',
                    'placeholder' => 'settings.ai_providers.audio_models_placeholder',
                    'helper' => 'settings.ai_providers.models_helper',
                ],
                [
                    'name' => 'models_video',
                    'type' => 'text',
                    'label' => 'settings.ai_providers.video_models',
                    'placeholder' => 'settings.ai_providers.video_models_placeholder',
                    'helper' => 'settings.ai_providers.models_helper',
                ],
            ],
            'models' => [
                'speech_to_text' => [],
                'text' => [],
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
                    'label' => 'settings.ai_providers.text_models',
                    'placeholder' => 'settings.ai_providers.google_models_placeholder',
                    'helper' => 'settings.ai_providers.models_helper',
                ],
                [
                    'name' => 'models_image',
                    'type' => 'text',
                    'label' => 'settings.ai_providers.image_models',
                    'placeholder' => 'settings.ai_providers.image_models_placeholder',
                    'helper' => 'settings.ai_providers.models_helper',
                ],
                [
                    'name' => 'models_audio',
                    'type' => 'text',
                    'label' => 'settings.ai_providers.audio_models',
                    'placeholder' => 'settings.ai_providers.audio_models_placeholder',
                    'helper' => 'settings.ai_providers.models_helper',
                ],
                [
                    'name' => 'models_video',
                    'type' => 'text',
                    'label' => 'settings.ai_providers.video_models',
                    'placeholder' => 'settings.ai_providers.video_models_placeholder',
                    'helper' => 'settings.ai_providers.models_helper',
                ],
            ],
            'models' => [
                'speech_to_text' => [],
                'text' => [],
            ],
        ],
        [
            'key' => 'xai',
            'name' => 'settings.ai_providers.xai',
            'config_key' => 'xai_config',
            'encrypted' => true,
            'fields' => [
                [
                    'name' => 'key',
                    'type' => 'password',
                    'label' => 'settings.ai_providers.xai',
                    'placeholder' => 'settings.ai_providers.xai_placeholder',
                ],
                [
                    'name' => 'models',
                    'type' => 'text',
                    'label' => 'settings.ai_providers.text_models',
                    'placeholder' => 'settings.ai_providers.xai_models_placeholder',
                    'helper' => 'settings.ai_providers.models_helper',
                ],
                [
                    'name' => 'models_image',
                    'type' => 'text',
                    'label' => 'settings.ai_providers.image_models',
                    'placeholder' => 'settings.ai_providers.image_models_placeholder',
                    'helper' => 'settings.ai_providers.models_helper',
                ],
                [
                    'name' => 'models_audio',
                    'type' => 'text',
                    'label' => 'settings.ai_providers.audio_models',
                    'placeholder' => 'settings.ai_providers.audio_models_placeholder',
                    'helper' => 'settings.ai_providers.models_helper',
                ],
                [
                    'name' => 'models_video',
                    'type' => 'text',
                    'label' => 'settings.ai_providers.video_models',
                    'placeholder' => 'settings.ai_providers.video_models_placeholder',
                    'helper' => 'settings.ai_providers.models_helper',
                ],
            ],
            'models' => [
                'speech_to_text' => [],
                'text' => [],
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
                    'label' => 'settings.ai_providers.text_models',
                    'placeholder' => 'settings.ai_providers.ollama_models_placeholder',
                    'helper' => 'settings.ai_providers.models_helper',
                ],
                [
                    'name' => 'models_image',
                    'type' => 'text',
                    'label' => 'settings.ai_providers.image_models',
                    'placeholder' => 'settings.ai_providers.image_models_placeholder',
                    'helper' => 'settings.ai_providers.models_helper',
                ],
                [
                    'name' => 'models_audio',
                    'type' => 'text',
                    'label' => 'settings.ai_providers.audio_models',
                    'placeholder' => 'settings.ai_providers.audio_models_placeholder',
                    'helper' => 'settings.ai_providers.models_helper',
                ],
                [
                    'name' => 'models_video',
                    'type' => 'text',
                    'label' => 'settings.ai_providers.video_models',
                    'placeholder' => 'settings.ai_providers.video_models_placeholder',
                    'helper' => 'settings.ai_providers.models_helper',
                ],
            ],
            'models' => [
                'speech_to_text' => [],
                'text' => [],
            ],
        ],
    ],
];
