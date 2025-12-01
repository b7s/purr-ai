# Update System

PurrAI includes an automatic update system that checks for new versions and allows users to install updates seamlessly.

## How It Works

### Automatic Update Checking

The application automatically checks for updates every 6 hours when it starts. This is handled by the `UpdateService` and registered in the `NativeAppServiceProvider`.

**Check Interval:** 6 hours (configurable in `UpdateService::CHECK_INTERVAL_HOURS`)

### Update Flow

1. **Application Starts** → Checks if 6 hours have passed since last check
2. **Update Available** → NativePHP fires `UpdateAvailable` event
3. **Update Stored** → Version and availability saved in Settings
4. **User Notified** → Alert badge appears on settings icon
5. **User Installs** → Clicks "Install Update" button
6. **Application Restarts** → Update is applied automatically

### Components

#### UpdateService (`app/Services/UpdateService.php`)

Core service that manages update logic:

```php
// Check if update check is needed
$service->shouldCheckForUpdates(): bool

// Manually trigger update check
$service->checkForUpdates(): void

// Mark update as available
$service->markUpdateAvailable(string $version): void

// Clear update availability
$service->clearUpdateAvailable(): void

// Get current app version
$service->getCurrentVersion(): string

// Get available update version
$service->getUpdateVersion(): ?string

// Check if update is available
$service->isUpdateAvailable(): bool

// Get last check time
$service->getLastCheckTime(): ?Carbon
```

#### Settings Storage

Update information is stored in the `settings` table using key/value pairs:

- `update_available` (boolean) - Whether an update is available
- `update_version` (string) - Version number of available update
- `last_update_check` (datetime) - Timestamp of last update check

#### Helper Functions

Global helpers for checking update status:

```php
hasUpdateAvailable(): bool        // Check if update is available
getUpdateVersion(): ?string       // Get update version
getAlertsList(): array           // Get all active alerts including updates
```

#### UI Components

**Settings Page** (`resources/views/livewire/settings/other.blade.php`)

Displays update card with:
- Current version information
- Available update version (if any)
- "Check for Updates" or "Install Update" button
- Last check timestamp

**Settings Icon** (`resources/views/components/ui/app-header.blade.php`)

Shows alert badge when updates are available with tooltip listing all alerts.

### Event Listeners

The system listens to NativePHP updater events:

```php
// Update is available
'Native\Desktop\Events\Updater\UpdateAvailable'

// Update has been downloaded
'Native\Desktop\Events\Updater\UpdateDownloaded'

// No update available
'Native\Desktop\Events\Updater\UpdateNotAvailable'
```

## Configuration

### NativePHP Configuration

Update settings are configured in `config/nativephp.php`:

```php
'updater' => [
    'enabled' => env('NATIVEPHP_UPDATER_ENABLED', true),
    'default' => env('NATIVEPHP_UPDATER_PROVIDER', 'spaces'),
    'providers' => [
        'github' => [...],
        's3' => [...],
        'spaces' => [...],
    ],
],
```

### Application Version

Set your app version in `config/nativephp.php`:

```php
'version' => env('NATIVEPHP_APP_VERSION', '1.0.0'),
```

Increment this version with each release.

## User Experience

There are some features designed to make the user's life easier.

### When No Update Available

- Green checkmark icon
- "You are using the latest version" message
- Current version displayed
- "Check for Updates" button available

### When Update Available

- Blue download icon
- "Update available" message
- Current version → New version displayed
- "Install Update" button (replaces check button)
- Alert badge on settings icon in header

### Installing Updates

1. User clicks "Install Update" button
2. Application calls `AutoUpdater::quitAndInstall()`
3. Application closes gracefully
4. Update is applied
5. Application restarts with new version

## Testing

Run update service tests:

```bash
php artisan test --filter=UpdateServiceTest
```

Tests cover:
- Update check timing logic
- Marking updates as available/unavailable
- Version retrieval
- Last check timestamp handling

## Manual Update Check

Users can manually check for updates at any time by clicking the "Check for Updates" button in Settings → Other tab, regardless of the 6-hour interval.

## Troubleshooting

### Updates Not Detected

1. Check NativePHP updater configuration in `config/nativephp.php`
2. Verify updater provider credentials (GitHub token, S3 keys, etc.)
3. Ensure `NATIVEPHP_UPDATER_ENABLED=true` in `.env`
4. Check application logs for updater events

### Update Check Not Running

1. Verify `UpdateService` is registered in `NativeAppServiceProvider`
2. Check `last_update_check` value in settings table
3. Clear cache: `php artisan cache:clear`

### Alert Badge Not Showing

1. Check `hasUpdateAvailable()` helper returns true
2. Verify `update_available` setting is set to true
3. Clear view cache: `php artisan view:clear`
