#!/bin/bash

# Fix Electron sandbox permissions for NativePHP
# This script sets the correct permissions for the chrome-sandbox binary

SANDBOX_PATH="vendor/nativephp/desktop/resources/electron/node_modules/electron/dist/chrome-sandbox"

if [ ! -f "$SANDBOX_PATH" ]; then
    echo "❌ Error: chrome-sandbox not found at $SANDBOX_PATH"
    echo "   Run 'composer install' first"
    exit 1
fi

echo "🔧 Fixing Electron sandbox permissions..."

sudo chown root:root "$SANDBOX_PATH"
sudo chmod 4755 "$SANDBOX_PATH"

if [ $? -eq 0 ]; then
    echo "✓ Sandbox permissions fixed successfully!"
    echo ""
    echo "You can now run: php artisan native:run"
else
    echo "❌ Failed to fix permissions. Make sure you have sudo access."
    exit 1
fi
