#!/bin/bash
set -e

echo "Running after-install script for PurrAI"

chown -R root:root /opt/PurrAI/resources/build/app/bootstrap/cache
chown -R root:root /opt/PurrAI/resources/build/app/storage
chmod -R 775 /opt/PurrAI/resources/build/app/bootstrap/cache
chmod -R 775 /opt/PurrAI/resources/build/app/storage
chown root:root /opt/PurrAI/chrome-sandbox
chmod 4755 /opt/PurrAI/chrome-sandbox
