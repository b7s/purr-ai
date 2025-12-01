<?php

declare(strict_types=1);

return [
    'filesystem' => [
        'path_required' => 'Будь ласка, вкажіть шлях, щоб продовжити.',
        'not_directory' => 'Шлях ":path" не є каталогом.',
        'permission_denied' => 'У вас немає доступу до ":path".',
        'read_failed' => 'Не вдалося прочитати вміст каталогу.',
        'list_summary' => 'Знайдено :count із :total елементів у "**:path**"',
        'list_failed' => 'Не вдалося перелічити вміст каталогу.',
        'not_found' => 'Файл або каталог ":path" не знайдено.',
        'info_retrieved' => 'Отримано інформацію для ":name".',
        'info_failed' => 'Не вдалося отримати інформацію про файл.',
        'pattern_required' => 'Будь ласка, вкажіть шаблон пошуку.',
        'search_summary' => 'Знайдено :count результатів для "**:**pattern**" у "**:**path**"',
        'search_failed' => 'Помилка під час пошуку.',
    ],
    'user_profile' => [
        'fields' => [
            'user_name' => 'ім’я',
            'user_description' => 'опис',
            'response_detail' => 'детальність відповіді',
            'response_tone' => 'тон відповіді',
            'timezone' => 'часовий пояс',
        ],
        'profile_complete' => 'Ось інформація вашого профілю, :name.',
        'profile_incomplete' => 'У профілі бракує: :fields. Бажаєте додати ці дані?',
        'profile_updated' => 'Профіль успішно оновлено: :fields.',
        'errors' => [
            'get_failed' => 'Не вдалося отримати інформацію профілю.',
            'update_failed' => 'Не вдалося оновити профіль.',
            'no_updates' => 'Не надано інформації для оновлення.',
            'validation_failed' => 'Деякі оновлення не вдалися: :errors',
            'name_too_long' => 'Ім’я повинно містити не більше 100 символів.',
            'description_too_long' => 'Опис повинен містити не більше 1000 символів.',
            'invalid_detail' => 'Недійсна детальність відповіді. Доступні варіанти: :valid',
            'invalid_tone' => 'Недійсний тон відповіді. Доступні варіанти: :valid',
            'invalid_timezone' => 'Недійсний часовий пояс. Використовуйте формат IANA (наприклад, Europe/Kyiv).',
        ],
    ],
];
