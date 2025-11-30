<?php

declare(strict_types=1);

namespace App\Services\Prism\Tools;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

/**
 * FileSystemToolHandler - File system browser and manager
 *
 * This handler provides access to the user's file system.
 * Destructive operations (delete, rename, move) require explicit user permission in settings.
 *
 * Supported operations:
 * - list: Browse directory contents
 * - info: Get file/directory information
 * - search: Find files matching patterns
 * - get_home: Get user's home directory path
 * - system_info: Get system information (OS, hostname, drives, disk space)
 * - delete: Delete files/directories (requires permission)
 * - rename: Rename files/directories (requires permission)
 * - move: Move files/directories (requires permission)
 *
 * Security: Destructive operations are blocked unless enabled in settings.
 * Performance: Uses optimized OS-specific terminal commands.
 */
class FileSystemToolHandler
{
    private const MAX_RESULTS = 200;

    private const DEFAULT_LIMIT = 50;

    private string $os;

    public function __construct()
    {
        $this->os = $this->detectOS();
    }

    public function handle(string $action, ?string $path, ?string $pattern, ?int $limit, ?string $destination = null): string
    {
        return match ($action) {
            'list' => $this->listDirectory($path, $limit),
            'info' => $this->getFileInfo($path),
            'search' => $this->searchFiles($path, $pattern, $limit),
            'get_home' => $this->getHomeDirectory(),
            'system_info' => $this->getSystemInfo(),
            'delete' => $this->deleteFile($path),
            'rename' => $this->renameFile($path, $destination),
            'move' => $this->moveFile($path, $destination),
            default => json_encode(['error' => "Action [{$action}] not found"]),
        };
    }

    private function detectOS(): string
    {
        $os = strtolower(PHP_OS_FAMILY);

        return match ($os) {
            'windows' => 'windows',
            'darwin' => 'mac',
            'linux', 'bsd' => 'linux',
            default => 'linux',
        };
    }

    private function isWindows(): bool
    {
        return $this->os === 'windows';
    }

    private function isUnix(): bool
    {
        return \in_array($this->os, ['linux', 'mac']);
    }

    private function listDirectory(?string $path, ?int $limit): string
    {
        if (empty($path)) {
            return json_encode([
                'error' => 'path is required for list action',
                'user_message' => __('chat.filesystem.path_required'),
            ]);
        }

        try {
            $expandedPath = $this->expandPath($path);

            if (! is_dir($expandedPath)) {
                return json_encode([
                    'error' => 'Path is not a directory',
                    'user_message' => __('chat.filesystem.not_directory', ['path' => $path]),
                ]);
            }

            if (! is_readable($expandedPath)) {
                return json_encode([
                    'error' => 'Permission denied',
                    'user_message' => __('chat.filesystem.permission_denied', ['path' => $path]),
                ]);
            }

            $maxResults = min($limit ?? self::DEFAULT_LIMIT, self::MAX_RESULTS);

            // Use optimized OS-specific commands
            if ($this->isUnix()) {
                $entries = $this->listDirectoryUnix($expandedPath, $maxResults);
            } else {
                $entries = $this->listDirectoryWindows($expandedPath, $maxResults);
            }

            $totalItems = \count($entries);

            $summary = __('chat.filesystem.list_summary', [
                'count' => \count($entries),
                'total' => $totalItems,
                'path' => $path,
            ]);

            return json_encode([
                'success' => true,
                'path' => $expandedPath,
                'count' => \count($entries),
                'total' => $totalItems,
                'truncated' => $totalItems > $maxResults,
                'entries' => $entries,
                'user_message' => $summary,
            ]);
        } catch (\Throwable $e) {
            Log::error('FileSystemTool: Failed to list directory', [
                'path' => $path,
                'error' => $e->getMessage(),
            ]);

            return json_encode([
                'error' => $e->getMessage(),
                'user_message' => __('chat.filesystem.list_failed'),
            ]);
        }
    }

    /**
     * @return array<int, array{name: string, path: string, is_directory: bool, size: int|null, size_human: string|null, modified: string|null, permissions: string|null, extension: string|null, mime_type: string|null, external_url: string|null}>
     */
    private function listDirectoryUnix(string $path, int $maxResults): array
    {
        // Use ls with detailed format for Unix systems
        $escapedPath = escapeshellarg($path);
        $result = Process::run("ls -lAh --time-style=long-iso {$escapedPath} 2>/dev/null | head -n {$maxResults}");

        if (! $result->successful()) {
            // Fallback to PHP functions
            return $this->listDirectoryFallback($path, $maxResults);
        }

        $entries = [];
        $lines = explode("\n", trim($result->output()));

        foreach ($lines as $line) {
            if (empty($line) || str_starts_with($line, 'total')) {
                continue;
            }

            $parsed = $this->parseUnixLsLine($line, $path);
            if ($parsed !== null) {
                $entries[] = $parsed;
            }
        }

        return $entries;
    }

    /**
     * @return array{name: string, path: string, is_directory: bool, size: int|null, size_human: string|null, modified: string|null, permissions: string|null, extension: string|null, mime_type: string|null, external_url: string|null}|null
     */
    private function parseUnixLsLine(string $line, string $basePath): ?array
    {
        // Parse ls -lAh output: permissions links owner group size date time name
        $parts = preg_split('/\s+/', $line, 9);

        if (\count($parts) < 9) {
            return null;
        }

        $permissions = $parts[0];
        $isDir = str_starts_with($permissions, 'd');
        $sizeHuman = $parts[4];
        $date = $parts[5];
        $time = $parts[6];
        $name = $parts[8];

        // Skip . and ..
        if ($name === '.' || $name === '..') {
            return null;
        }

        $fullPath = $basePath.DIRECTORY_SEPARATOR.$name;
        $extension = $isDir ? null : pathinfo($name, PATHINFO_EXTENSION);

        return [
            'name' => $name,
            'path' => $fullPath,
            'is_directory' => $isDir,
            'size' => null, // Size in bytes not easily available from ls -h
            'size_human' => $isDir ? null : $sizeHuman,
            'modified' => "{$date} {$time}",
            'permissions' => substr($permissions, 1), // Remove first char (d/-)
            'extension' => $extension,
            'mime_type' => null,
            'external_url' => "file://{$fullPath}",
        ];
    }

    /**
     * @return array<int, array{name: string, path: string, is_directory: bool, size: int|null, size_human: string|null, modified: string|null, permissions: string|null, extension: string|null, mime_type: string|null, external_url: string|null}>
     */
    private function listDirectoryWindows(string $path, int $maxResults): array
    {
        // Use dir command for Windows
        $escapedPath = escapeshellarg($path);
        $result = Process::run("dir {$escapedPath} /A:-S /O:GN 2>nul");

        if (! $result->successful()) {
            // Fallback to PHP functions
            return $this->listDirectoryFallback($path, $maxResults);
        }

        $entries = [];
        $lines = explode("\n", trim($result->output()));
        $count = 0;

        foreach ($lines as $line) {
            if ($count >= $maxResults) {
                break;
            }

            $line = trim($line);

            // Skip header and footer lines
            if (empty($line) || str_contains($line, 'Volume') || str_contains($line, 'Directory of') || str_contains($line, 'File(s)') || str_contains($line, 'Dir(s)')) {
                continue;
            }

            $parsed = $this->parseWindowsDirLine($line, $path);
            if ($parsed !== null) {
                $entries[] = $parsed;
                $count++;
            }
        }

        return $entries;
    }

    /**
     * @return array{name: string, path: string, is_directory: bool, size: int|null, size_human: string|null, modified: string|null, permissions: string|null, extension: string|null, mime_type: string|null, external_url: string|null}|null
     */
    private function parseWindowsDirLine(string $line, string $basePath): ?array
    {
        // Parse dir output: date time <DIR>/size name
        $parts = preg_split('/\s+/', $line, 4);

        if (\count($parts) < 4) {
            return null;
        }

        $date = $parts[0];
        $time = $parts[1];
        $sizeOrDir = $parts[2];
        $name = $parts[3];

        // Skip . and ..
        if ($name === '.' || $name === '..') {
            return null;
        }

        $isDir = $sizeOrDir === '<DIR>';
        $fullPath = $basePath.DIRECTORY_SEPARATOR.$name;
        $extension = $isDir ? null : pathinfo($name, PATHINFO_EXTENSION);

        return [
            'name' => $name,
            'path' => $fullPath,
            'is_directory' => $isDir,
            'size' => $isDir ? null : (is_numeric($sizeOrDir) ? (int) $sizeOrDir : null),
            'size_human' => $isDir ? null : ($isDir ? null : $this->formatBytes((int) $sizeOrDir)),
            'modified' => "{$date} {$time}",
            'permissions' => null,
            'extension' => $extension,
            'mime_type' => null,
            'external_url' => "file://{$fullPath}",
        ];
    }

    /**
     * @return array<int, array{name: string, path: string, is_directory: bool, size: int|null, size_human: string|null, modified: string|null, permissions: string|null, extension: string|null, mime_type: string|null, external_url: string|null}>
     */
    private function listDirectoryFallback(string $path, int $maxResults): array
    {
        $items = scandir($path);
        if ($items === false) {
            return [];
        }

        $items = array_filter($items, fn ($item) => $item !== '.' && $item !== '..');
        $items = \array_slice($items, 0, $maxResults);

        $entries = [];
        foreach ($items as $item) {
            $fullPath = $path.DIRECTORY_SEPARATOR.$item;
            $entries[] = $this->getItemInfo($fullPath, $item);
        }

        usort($entries, fn ($a, $b) => ($b['is_directory'] <=> $a['is_directory']) ?: strcasecmp($a['name'], $b['name']));

        return $entries;
    }

    private function getFileInfo(?string $path): string
    {
        if (empty($path)) {
            return json_encode([
                'error' => 'path is required for info action',
                'user_message' => __('chat.filesystem.path_required'),
            ]);
        }

        try {
            $expandedPath = $this->expandPath($path);

            if (! file_exists($expandedPath)) {
                return json_encode([
                    'error' => 'File or directory not found',
                    'user_message' => __('chat.filesystem.not_found', ['path' => $path]),
                ]);
            }

            $info = $this->getItemInfo($expandedPath, basename($expandedPath));

            return json_encode([
                'success' => true,
                'info' => $info,
                'user_message' => __('chat.filesystem.info_retrieved', ['name' => $info['name']]),
            ]);
        } catch (\Throwable $e) {
            Log::error('FileSystemTool: Failed to get file info', [
                'path' => $path,
                'error' => $e->getMessage(),
            ]);

            return json_encode([
                'error' => $e->getMessage(),
                'user_message' => __('chat.filesystem.info_failed'),
            ]);
        }
    }

    private function searchFiles(?string $path, ?string $pattern, ?int $limit): string
    {
        if (empty($path)) {
            $path = '~';
        }

        if (empty($pattern)) {
            return json_encode([
                'error' => 'pattern is required for search action',
                'user_message' => __('chat.filesystem.pattern_required'),
            ]);
        }

        try {
            $expandedPath = $this->expandPath($path);

            if (! is_dir($expandedPath)) {
                return json_encode([
                    'error' => 'Search path is not a directory',
                    'user_message' => __('chat.filesystem.not_directory', ['path' => $path]),
                ]);
            }

            $maxResults = min($limit ?? self::DEFAULT_LIMIT, self::MAX_RESULTS);

            // Use optimized OS-specific search commands
            if ($this->isUnix()) {
                $results = $this->searchFilesUnix($expandedPath, $pattern, $maxResults);
            } else {
                $results = $this->searchFilesWindows($expandedPath, $pattern, $maxResults);
            }

            $summary = __('chat.filesystem.search_summary', [
                'count' => \count($results),
                'pattern' => $pattern,
                'path' => $path,
            ]);

            return json_encode([
                'success' => true,
                'search_path' => $expandedPath,
                'pattern' => $pattern,
                'count' => \count($results),
                'results' => $results,
                'user_message' => $summary,
            ]);
        } catch (\Throwable $e) {
            Log::error('FileSystemTool: Failed to search files', [
                'path' => $path,
                'pattern' => $pattern,
                'error' => $e->getMessage(),
            ]);

            return json_encode([
                'error' => $e->getMessage(),
                'user_message' => __('chat.filesystem.search_failed'),
            ]);
        }
    }

    /**
     * @return array<int, array{name: string, path: string, is_directory: bool, size: int|null, size_human: string|null, modified: string|null, permissions: string|null, extension: string|null, mime_type: string|null, external_url: string|null}>
     */
    private function searchFilesUnix(string $path, string $pattern, int $maxResults): array
    {
        // Use find command for Unix systems (much faster than PHP recursion)
        $escapedPath = escapeshellarg($path);
        $escapedPattern = escapeshellarg($pattern);

        // Convert glob pattern to find-compatible pattern
        $findPattern = str_replace('*', '*', $pattern);

        $result = Process::run("find {$escapedPath} -maxdepth 10 -iname {$escapedPattern} 2>/dev/null | head -n {$maxResults}");

        if (! $result->successful()) {
            // Fallback to PHP recursive search
            $results = [];
            $this->searchRecursive($path, $pattern, $results, $maxResults);

            return $results;
        }

        $results = [];
        $lines = explode("\n", trim($result->output()));

        foreach ($lines as $line) {
            if (empty($line)) {
                continue;
            }

            $fullPath = trim($line);
            $name = basename($fullPath);
            $results[] = $this->getItemInfo($fullPath, $name);
        }

        return $results;
    }

    /**
     * @return array<int, array{name: string, path: string, is_directory: bool, size: int|null, size_human: string|null, modified: string|null, permissions: string|null, extension: string|null, mime_type: string|null, external_url: string|null}>
     */
    private function searchFilesWindows(string $path, string $pattern, int $maxResults): array
    {
        // Use dir /s for Windows recursive search
        $escapedPath = escapeshellarg($path);
        $escapedPattern = escapeshellarg($pattern);

        $result = Process::run("dir {$escapedPath}\\{$escapedPattern} /s /b 2>nul");

        if (! $result->successful()) {
            // Fallback to PHP recursive search
            $results = [];
            $this->searchRecursive($path, $pattern, $results, $maxResults);

            return $results;
        }

        $results = [];
        $lines = explode("\n", trim($result->output()));
        $count = 0;

        foreach ($lines as $line) {
            if ($count >= $maxResults || empty($line)) {
                break;
            }

            $fullPath = trim($line);
            $name = basename($fullPath);
            $results[] = $this->getItemInfo($fullPath, $name);
            $count++;
        }

        return $results;
    }

    private function getHomeDirectory(): string
    {
        try {
            $home = getenv('HOME') ?: getenv('USERPROFILE');

            if (! $home) {
                return json_encode([
                    'error' => 'Could not determine home directory',
                    'user_message' => __('chat.filesystem.home_not_found'),
                ]);
            }

            return json_encode([
                'success' => true,
                'home_directory' => $home,
                'user_message' => __('chat.filesystem.home_found', ['path' => $home]),
            ]);
        } catch (\Throwable $e) {
            Log::error('FileSystemTool: Failed to get home directory', [
                'error' => $e->getMessage(),
            ]);

            return json_encode([
                'error' => $e->getMessage(),
                'user_message' => __('chat.filesystem.home_failed'),
            ]);
        }
    }

    /**
     * @return array{name: string, path: string, is_directory: bool, size: int|null, size_human: string|null, modified: string|null, permissions: string|null, extension: string|null, mime_type: string|null, external_url: string|null}
     */
    private function getItemInfo(string $fullPath, string $name): array
    {
        $isDir = is_dir($fullPath);
        $size = $isDir ? null : (is_readable($fullPath) ? filesize($fullPath) : null);
        $modified = is_readable($fullPath) ? date('Y-m-d H:i:s', filemtime($fullPath)) : null;
        $permissions = is_readable($fullPath) ? substr(\sprintf('%o', fileperms($fullPath)), -4) : null;
        $extension = $isDir ? null : pathinfo($fullPath, PATHINFO_EXTENSION);
        $mimeType = ! $isDir && is_readable($fullPath) && function_exists('mime_content_type') ? mime_content_type($fullPath) : null;

        return [
            'name' => $name,
            'path' => $fullPath,
            'is_directory' => $isDir,
            'size' => $size,
            'size_human' => $size !== null ? $this->formatBytes($size) : null,
            'modified' => $modified,
            'permissions' => $permissions,
            'extension' => $extension,
            'mime_type' => $mimeType,
            'external_url' => "file://{$fullPath}",
        ];
    }

    /**
     * @param  array<int, array{name: string, path: string, is_directory: bool, size: int|null, size_human: string|null, modified: string|null, permissions: string|null, extension: string|null, mime_type: string|null, external_url: string|null}>  $results
     */
    private function searchRecursive(string $dir, string $pattern, array &$results, int $maxResults, int $depth = 0): void
    {
        if (\count($results) >= $maxResults || $depth > 10) {
            return;
        }

        if (! is_readable($dir)) {
            return;
        }

        $items = @scandir($dir);
        if ($items === false) {
            return;
        }

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            if (\count($results) >= $maxResults) {
                return;
            }

            $fullPath = $dir.DIRECTORY_SEPARATOR.$item;

            if (fnmatch($pattern, $item, FNM_CASEFOLD)) {
                $results[] = $this->getItemInfo($fullPath, $item);
            }

            if (is_dir($fullPath) && is_readable($fullPath)) {
                $this->searchRecursive($fullPath, $pattern, $results, $maxResults, $depth + 1);
            }
        }
    }

    private function expandPath(string $path): string
    {
        if (str_starts_with($path, '~')) {
            $home = getenv('HOME') ?: getenv('USERPROFILE');
            $path = $home.substr($path, 1);
        }

        return realpath($path) ?: $path;
    }

    private function formatBytes(int|float $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, \count($units) - 1);
        $bytes /= (1024 ** $pow);

        return round($bytes, 2).' '.$units[$pow];
    }

    private function isDestructiveOperationsAllowed(): bool
    {
        return (bool) \App\Models\Setting::get('allow_destructive_file_operations', false);
    }

    private function deleteFile(?string $path): string
    {
        if (! $this->isDestructiveOperationsAllowed()) {
            return json_encode([
                'error' => 'Destructive operations are disabled',
                'user_message' => __('chat.filesystem.destructive_disabled'),
            ]);
        }

        if (empty($path)) {
            return json_encode([
                'error' => 'path is required for delete action',
                'user_message' => __('chat.filesystem.path_required'),
            ]);
        }

        try {
            $expandedPath = $this->expandPath($path);

            if (! file_exists($expandedPath)) {
                return json_encode([
                    'error' => 'File or directory not found',
                    'user_message' => __('chat.filesystem.not_found', ['path' => $path]),
                ]);
            }

            $isDir = is_dir($expandedPath);

            if ($isDir) {
                if (! $this->deleteDirectory($expandedPath)) {
                    throw new \Exception('Failed to delete directory');
                }
            } else {
                if (! unlink($expandedPath)) {
                    throw new \Exception('Failed to delete file');
                }
            }

            return json_encode([
                'success' => true,
                'path' => $expandedPath,
                'was_directory' => $isDir,
                'user_message' => __('chat.filesystem.deleted', ['path' => $path]),
            ]);
        } catch (\Throwable $e) {
            Log::error('FileSystemTool: Failed to delete', [
                'path' => $path,
                'error' => $e->getMessage(),
            ]);

            return json_encode([
                'error' => $e->getMessage(),
                'user_message' => __('chat.filesystem.delete_failed', ['path' => $path]),
            ]);
        }
    }

    private function renameFile(?string $path, ?string $destination): string
    {
        if (! $this->isDestructiveOperationsAllowed()) {
            return json_encode([
                'error' => 'Destructive operations are disabled',
                'user_message' => __('chat.filesystem.destructive_disabled'),
            ]);
        }

        if (empty($path) || empty($destination)) {
            return json_encode([
                'error' => 'path and destination are required for rename action',
                'user_message' => __('chat.filesystem.path_destination_required'),
            ]);
        }

        try {
            $expandedPath = $this->expandPath($path);
            $expandedDestination = $this->expandPath($destination);

            if (! file_exists($expandedPath)) {
                return json_encode([
                    'error' => 'Source file or directory not found',
                    'user_message' => __('chat.filesystem.not_found', ['path' => $path]),
                ]);
            }

            if (file_exists($expandedDestination)) {
                return json_encode([
                    'error' => 'Destination already exists',
                    'user_message' => __('chat.filesystem.destination_exists', ['path' => $destination]),
                ]);
            }

            if (! rename($expandedPath, $expandedDestination)) {
                throw new \Exception('Failed to rename');
            }

            return json_encode([
                'success' => true,
                'from' => $expandedPath,
                'to' => $expandedDestination,
                'user_message' => __('chat.filesystem.renamed', ['from' => $path, 'to' => $destination]),
            ]);
        } catch (\Throwable $e) {
            Log::error('FileSystemTool: Failed to rename', [
                'path' => $path,
                'destination' => $destination,
                'error' => $e->getMessage(),
            ]);

            return json_encode([
                'error' => $e->getMessage(),
                'user_message' => __('chat.filesystem.rename_failed'),
            ]);
        }
    }

    private function moveFile(?string $path, ?string $destination): string
    {
        return $this->renameFile($path, $destination);
    }

    private function deleteDirectory(string $dir): bool
    {
        if (! is_dir($dir)) {
            return false;
        }

        $items = scandir($dir);
        if ($items === false) {
            return false;
        }

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $path = $dir.DIRECTORY_SEPARATOR.$item;

            if (is_dir($path)) {
                if (! $this->deleteDirectory($path)) {
                    return false;
                }
            } else {
                if (! unlink($path)) {
                    return false;
                }
            }
        }

        return rmdir($dir);
    }

    private function getSystemInfo(): string
    {
        try {
            $info = [
                'os_name' => php_uname('s'),
                'os_release' => php_uname('r'),
                'os_version' => php_uname('v'),
                'machine' => php_uname('m'),
                'hostname' => php_uname('n'),
                'php_os' => PHP_OS,
                'php_os_family' => PHP_OS_FAMILY,
                'home_directory' => getenv('HOME') ?: getenv('USERPROFILE'),
                'current_user' => getenv('USER') ?: getenv('USERNAME'),
            ];

            // Get mounted drives/disks
            $info['drives'] = $this->getMountedDrives();

            // Get disk space for home directory
            $homeDir = $info['home_directory'];
            if ($homeDir && is_dir($homeDir)) {
                $total = disk_total_space($homeDir);
                $free = disk_free_space($homeDir);

                if ($total !== false && $free !== false) {
                    $info['home_disk_space'] = [
                        'total' => $total,
                        'free' => $free,
                        'used' => $total - $free,
                        'total_human' => $this->formatBytes($total),
                        'free_human' => $this->formatBytes($free),
                        'used_human' => $this->formatBytes($total - $free),
                    ];
                }
            }

            return json_encode([
                'success' => true,
                'system_info' => $info,
                'user_message' => __('chat.filesystem.system_info_retrieved'),
            ]);
        } catch (\Throwable $e) {
            Log::error('FileSystemTool: Failed to get system info', [
                'error' => $e->getMessage(),
            ]);

            return json_encode([
                'error' => $e->getMessage(),
                'user_message' => __('chat.filesystem.system_info_failed'),
            ]);
        }
    }

    /**
     * @return array<int, array{drive: string, mount_point: string|null, total: int|null, free: int|null, used: int|null, total_human: string|null, free_human: string|null, used_human: string|null}>
     */
    private function getMountedDrives(): array
    {
        $drives = [];

        if (PHP_OS_FAMILY === 'Windows') {
            // Windows: Get drive letters
            for ($letter = 'A'; $letter <= 'Z'; $letter++) {
                $drive = $letter.':';
                if (is_dir($drive)) {
                    $total = @disk_total_space($drive);
                    $free = @disk_free_space($drive);

                    $drives[] = [
                        'drive' => $drive,
                        'mount_point' => $drive.'\\',
                        'total' => ($total !== false) ? $total : null,
                        'free' => ($free !== false) ? $free : null,
                        'used' => ($total !== false && $free !== false) ? $total - $free : null,
                        'total_human' => ($total !== false) ? $this->formatBytes($total) : null,
                        'free_human' => ($free !== false) ? $this->formatBytes($free) : null,
                        'used_human' => ($total !== false && $free !== false) ? $this->formatBytes($total - $free) : null,
                    ];
                }
            }
        } else {
            // Unix/Linux/Mac: Parse mount points
            $result = Process::run('df -h 2>/dev/null || df');

            if ($result->successful()) {
                $lines = explode("\n", trim($result->output()));
                array_shift($lines); // Remove header

                foreach ($lines as $line) {
                    if (empty(trim($line))) {
                        continue;
                    }

                    $parts = preg_split('/\s+/', $line);
                    if (\count($parts) >= 6) {
                        $mountPoint = $parts[5] ?? null;
                        if ($mountPoint && is_dir($mountPoint)) {
                            $total = @disk_total_space($mountPoint);
                            $free = @disk_free_space($mountPoint);

                            $drives[] = [
                                'drive' => $parts[0],
                                'mount_point' => $mountPoint,
                                'total' => ($total !== false) ? $total : null,
                                'free' => ($free !== false) ? $free : null,
                                'used' => ($total !== false && $free !== false) ? $total - $free : null,
                                'total_human' => ($total !== false) ? $this->formatBytes($total) : null,
                                'free_human' => ($free !== false) ? $this->formatBytes($free) : null,
                                'used_human' => ($total !== false && $free !== false) ? $this->formatBytes($total - $free) : null,
                            ];
                        }
                    }
                }
            }
        }

        return $drives;
    }
}
