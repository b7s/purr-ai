<?php

declare(strict_types=1);

return [
    'filesystem' => [
        'path_required' => '続行するにはパスを指定してください。',
        'not_directory' => '「:path」はディレクトリではありません。',
        'permission_denied' => '「:path」にアクセスする権限がありません。',
        'read_failed' => 'ディレクトリの内容を読み取れませんでした。',
        'list_summary' => '"**:path**" で :total 件中 :count 件を見つけました',
        'list_failed' => 'ディレクトリの内容を一覧表示できませんでした。',
        'not_found' => 'ファイルまたはディレクトリ「:path」が見つかりません。',
        'info_retrieved' => '「:name」の情報を取得しました。',
        'info_failed' => 'ファイル情報を取得できませんでした。',
        'pattern_required' => '検索パターンを入力してください。',
        'search_summary' => '"**:path**" で "**:**pattern**" に一致する :count 件を見つけました',
        'search_failed' => '検索に失敗しました。',
    ],
    'user_profile' => [
        'fields' => [
            'user_name' => '名前',
            'user_description' => '説明',
            'response_detail' => '応答の詳細',
            'response_tone' => '応答のトーン',
            'timezone' => 'タイムゾーン',
        ],
        'profile_complete' => ':name さんのプロフィール情報はこちらです。',
        'profile_incomplete' => 'プロフィールに不足している項目: :fields。入力しますか？',
        'profile_updated' => 'プロフィールを更新しました: :fields。',
        'errors' => [
            'get_failed' => 'プロフィール情報を取得できませんでした。',
            'update_failed' => 'プロフィールを更新できませんでした。',
            'no_updates' => '更新する情報が提供されていません。',
            'validation_failed' => '一部の更新に失敗しました: :errors',
            'name_too_long' => '名前は100文字以内で入力してください。',
            'description_too_long' => '説明は1000文字以内で入力してください。',
            'invalid_detail' => '無効な応答詳細です。利用可能なオプション: :valid',
            'invalid_tone' => '無効な応答トーンです。利用可能なオプション: :valid',
            'invalid_timezone' => '無効なタイムゾーンです。IANA形式（例: America/New_York）を使用してください。',
        ],
    ],
];
