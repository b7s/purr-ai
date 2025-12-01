#!/bin/bash
set -e

APP_DIR="/opt/PurrAI"
CACHE_DIR="$APP_DIR/resources/build/app/bootstrap/cache"
STORAGE_DIR="$APP_DIR/resources/build/app/storage"
SANDBOX_PATH="$APP_DIR/chrome-sandbox"
TARGET_USER="${SUDO_USER:-$(logname 2>/dev/null || echo '')}"

echo "Running after-install script for PurrAI"

if [[ -n "$TARGET_USER" && "$TARGET_USER" != "root" ]]; then
    TARGET_GROUP="$(id -gn "$TARGET_USER")"
    echo "→ Granting ownership of cache/storage to $TARGET_USER"
    chown -R "$TARGET_USER:$TARGET_GROUP" "$CACHE_DIR" "$STORAGE_DIR"
    chmod -R 775 "$CACHE_DIR" "$STORAGE_DIR"
else
    echo "→ Unable to detect installing user, relaxing permissions for cache/storage"
    chmod -R 777 "$CACHE_DIR" "$STORAGE_DIR"
fi

chown root:root "$SANDBOX_PATH"
chmod 4755 "$SANDBOX_PATH"
