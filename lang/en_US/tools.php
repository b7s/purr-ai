<?php

declare(strict_types=1);

return [
    'filesystem' => [
        'path_required' => 'Please provide a path to continue.',
        'not_directory' => 'The path ":path" is not a directory.',
        'permission_denied' => 'You do not have permission to access ":path".',
        'read_failed' => 'Failed to read the directory contents.',
        'list_summary' => 'Found :count of :total items in "**:path**"',
        'list_failed' => 'Failed to list directory contents.',
        'not_found' => 'The file or directory ":path" was not found.',
        'info_retrieved' => 'Retrieved information for ":name".',
        'info_failed' => 'Failed to retrieve file information.',
        'pattern_required' => 'Please provide a search pattern.',
        'search_summary' => 'Found :count results for "**:**pattern**" in "**:**path**"',
        'search_failed' => 'Search operation failed.',
    ],
    'user_profile' => [
        'fields' => [
            'user_name' => 'name',
            'user_description' => 'description',
            'response_detail' => 'response detail',
            'response_tone' => 'response tone',
            'timezone' => 'timezone',
        ],
        'profile_complete' => 'Here is your profile information, :name.',
        'profile_incomplete' => 'Your profile is missing: :fields. Would you like to provide this information?',
        'profile_updated' => 'Profile updated successfully: :fields.',
        'errors' => [
            'get_failed' => 'Failed to retrieve profile information.',
            'update_failed' => 'Failed to update profile.',
            'no_updates' => 'No information provided to update.',
            'validation_failed' => 'Some updates failed: :errors',
            'name_too_long' => 'Name must be 100 characters or less.',
            'description_too_long' => 'Description must be 1000 characters or less.',
            'invalid_detail' => 'Invalid response detail. Valid options: :valid',
            'invalid_tone' => 'Invalid response tone. Valid options: :valid',
            'invalid_timezone' => 'Invalid timezone. Please use IANA format (e.g., America/New_York).',
        ],
    ],
];
