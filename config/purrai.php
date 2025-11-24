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
        'opacity' => 0.95
    ],
];
