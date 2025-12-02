#!/bin/bash
set -e

APP_DIR="/opt/PurrAI"
CACHE_DIR="$APP_DIR/resources/build/app/bootstrap/cache"
STORAGE_DIR="$APP_DIR/resources/build/app/storage"
SANDBOX_PATH="$APP_DIR/chrome-sandbox"
TARGET_USER="${SUDO_USER:-$(logname 2>/dev/null || echo '')}"

echo "Running after-install script for PurrAI"

# Check if directories exist
if [[ ! -d "$APP_DIR" ]]; then
    echo "ERROR: Application directory not found: $APP_DIR"
    exit 1
fi

# Fix cache and storage permissions
if [[ -d "$CACHE_DIR" ]]; then
    if [[ -n "$TARGET_USER" && "$TARGET_USER" != "root" ]]; then
        TARGET_GROUP="$(id -gn "$TARGET_USER")"
        echo "→ Granting ownership of cache to $TARGET_USER:$TARGET_GROUP"
        chown -R "$TARGET_USER:$TARGET_GROUP" "$CACHE_DIR"
        chmod -R 775 "$CACHE_DIR"
    else
        echo "→ Unable to detect installing user, relaxing permissions for cache"
        chmod -R 777 "$CACHE_DIR"
    fi
else
    echo "WARNING: Cache directory not found: $CACHE_DIR"
fi

if [[ -d "$STORAGE_DIR" ]]; then
    if [[ -n "$TARGET_USER" && "$TARGET_USER" != "root" ]]; then
        TARGET_GROUP="$(id -gn "$TARGET_USER")"
        echo "→ Granting ownership of storage to $TARGET_USER:$TARGET_GROUP"
        chown -R "$TARGET_USER:$TARGET_GROUP" "$STORAGE_DIR"
        chmod -R 775 "$STORAGE_DIR"
    else
        echo "→ Unable to detect installing user, relaxing permissions for storage"
        chmod -R 777 "$STORAGE_DIR"
    fi
else
    echo "WARNING: Storage directory not found: $STORAGE_DIR"
fi

# Fix chrome-sandbox permissions
if [[ -f "$SANDBOX_PATH" ]]; then
    echo "→ Fixing chrome-sandbox permissions"
    chown root:root "$SANDBOX_PATH"
    chmod 4755 "$SANDBOX_PATH"
    echo "✓ Chrome sandbox configured successfully"
else
    echo "ERROR: Chrome sandbox not found: $SANDBOX_PATH"
    exit 1
fi

echo "✓ After-install script completed successfully"
