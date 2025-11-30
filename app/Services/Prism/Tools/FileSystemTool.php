<?php

declare(strict_types=1);

namespace App\Services\Prism\Tools;

use Prism\Prism\Schema\EnumSchema;
use Prism\Prism\Schema\NumberSchema;
use Prism\Prism\Schema\StringSchema;
use Prism\Prism\Tool;

class FileSystemTool
{
    public static function make(): Tool
    {
        return (new Tool)
            ->as('file_system')
            ->for(
                'Browse and manage files and directories on the user\'s system. Can list directory contents, get file information, search for files, get system information (OS, drives, disk space), '.
                'and perform destructive operations (delete, rename, move) if enabled in settings. '.
                'Always confirm with the user before executing any danger command, detailing what will be done, how it was before, and how it will be afterward. '.
                'Use this when the user asks about their files, folders, system info, or wants to find/manage specific files.'
            )
            ->withParameter(new EnumSchema(
                'action',
                'The action to perform on the file system',
                ['list', 'info', 'search', 'get_home', 'system_info', 'delete', 'rename', 'move']
            ), required: true)
            ->withParameter(new StringSchema(
                'path',
                'Path to directory or file (required for list/info actions). Use absolute paths or ~ for home directory.'
            ), required: false)
            ->withParameter(new StringSchema(
                'pattern',
                'Search pattern for finding files (e.g., "*.pdf", "document*", required for search action)'
            ), required: false)
            ->withParameter(new NumberSchema(
                'limit',
                'Maximum number of results to return (default: 50, max: 200)'
            ), required: false)
            ->withParameter(new StringSchema(
                'destination',
                'Destination path for rename/move operations (required for rename/move actions)'
            ), required: false)
            ->using(fn (string $action, ?string $path = null, ?string $pattern = null, ?int $limit = null, ?string $destination = null): string => (new FileSystemToolHandler)->handle($action, $path, $pattern, $limit, $destination));
    }
}
