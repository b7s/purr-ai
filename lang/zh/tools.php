<?php

declare(strict_types=1);

return [
    'filesystem' => [
        'path_required' => '请提供路径以继续。',
        'not_directory' => '路径“:path”不是目录。',
        'permission_denied' => '你无权访问“:path”。',
        'read_failed' => '读取目录内容失败。',
        'list_summary' => '在“**:path**”中找到 :total 个项目中的 :count 个',
        'list_failed' => '列出目录内容失败。',
        'not_found' => '未找到文件或目录“:path”。',
        'info_retrieved' => '已获取“:name”的信息。',
        'info_failed' => '获取文件信息失败。',
        'pattern_required' => '请提供搜索模式。',
        'search_summary' => '在“**:**path**”中找到符合“**:**pattern**”的 :count 个结果',
        'search_failed' => '搜索失败。',
    ],
    'user_profile' => [
        'fields' => [
            'user_name' => '姓名',
            'user_description' => '简介',
            'response_detail' => '回复细节',
            'response_tone' => '回复语气',
            'timezone' => '时区',
        ],
        'profile_complete' => ':name，这是你的资料信息。',
        'profile_incomplete' => '你的资料缺少：:fields。想补充这些信息吗？',
        'profile_updated' => '资料更新成功：:fields。',
        'errors' => [
            'get_failed' => '获取资料信息失败。',
            'update_failed' => '更新资料失败。',
            'no_updates' => '未提供可更新的信息。',
            'validation_failed' => '部分更新失败：:errors',
            'name_too_long' => '姓名必须少于或等于 100 个字符。',
            'description_too_long' => '简介必须少于或等于 1000 个字符。',
            'invalid_detail' => '回复细节无效。有效选项：:valid',
            'invalid_tone' => '回复语气无效。有效选项：:valid',
            'invalid_timezone' => '时区无效。请使用 IANA 格式（例如 America/New_York）。',
        ],
    ],
];
