#!/bin/bash
set -e

APP_DIR="/opt/PurrAI"
EXECUTABLE="$APP_DIR/purrai"
CACHE_DIR="$APP_DIR/resources/build/app/bootstrap/cache"
STORAGE_DIR="$APP_DIR/resources/build/app/storage"
SANDBOX_PATH="$APP_DIR/chrome-sandbox"
TARGET_USER="${SUDO_USER:-$(logname 2>/dev/null || echo '')}"
LOG_FILE="/tmp/purrai-install.log"

# Log function
log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" | tee -a "$LOG_FILE"
}

log "========================================="
log "Running after-install script for PurrAI"
log "APP_DIR: $APP_DIR"
log "TARGET_USER: $TARGET_USER"
log "========================================="

# Check if directories exist
if [[ ! -d "$APP_DIR" ]]; then
    log "ERROR: Application directory not found: $APP_DIR"
    exit 1
fi
log "✓ Application directory found"

# Create symlink in /usr/bin
if [[ -f "$EXECUTABLE" ]]; then
    log "→ Creating symlink /usr/bin/purrai"
    ln -sf "$EXECUTABLE" /usr/bin/purrai
    log "✓ Symlink created"
else
    log "WARNING: Executable not found: $EXECUTABLE"
fi

# Fix cache and storage permissions
if [[ -d "$CACHE_DIR" ]]; then
    if [[ -n "$TARGET_USER" && "$TARGET_USER" != "root" ]]; then
        TARGET_GROUP="$(id -gn "$TARGET_USER")"
        log "→ Granting ownership of cache to $TARGET_USER:$TARGET_GROUP"
        chown -R "$TARGET_USER:$TARGET_GROUP" "$CACHE_DIR"
        chmod -R 775 "$CACHE_DIR"
    else
        log "→ Unable to detect installing user, relaxing permissions for cache"
        chmod -R 777 "$CACHE_DIR"
    fi
    log "✓ Cache permissions fixed"
else
    log "WARNING: Cache directory not found: $CACHE_DIR"
fi

if [[ -d "$STORAGE_DIR" ]]; then
    if [[ -n "$TARGET_USER" && "$TARGET_USER" != "root" ]]; then
        TARGET_GROUP="$(id -gn "$TARGET_USER")"
        log "→ Granting ownership of storage to $TARGET_USER:$TARGET_GROUP"
        chown -R "$TARGET_USER:$TARGET_GROUP" "$STORAGE_DIR"
        chmod -R 775 "$STORAGE_DIR"
    else
        log "→ Unable to detect installing user, relaxing permissions for storage"
        chmod -R 777 "$STORAGE_DIR"
    fi
    log "✓ Storage permissions fixed"
else
    log "WARNING: Storage directory not found: $STORAGE_DIR"
fi

# Fix chrome-sandbox permissions
if [[ -f "$SANDBOX_PATH" ]]; then
    log "→ Fixing chrome-sandbox permissions"
    log "  Before: $(ls -la "$SANDBOX_PATH")"
    chown root:root "$SANDBOX_PATH"
    chmod 4755 "$SANDBOX_PATH"
    log "  After: $(ls -la "$SANDBOX_PATH")"
    log "✓ Chrome sandbox configured successfully"
else
    log "ERROR: Chrome sandbox not found: $SANDBOX_PATH"
    log "Contents of $APP_DIR:"
    ls -la "$APP_DIR" | tee -a "$LOG_FILE"
    exit 1
fi

log "✓ After-install script completed successfully"
log "========================================="
log "Installation log saved to: $LOG_FILE"
