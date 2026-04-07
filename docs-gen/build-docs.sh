#!/bin/bash
# Copies generated screenshots from docs-gen/screenshots/ into docs/fields/screenshots/
# Run after screenshot.js and screenshot-native.js

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
SRC="$SCRIPT_DIR/screenshots"
DEST="$SCRIPT_DIR/../docs/fields/screenshots"

# Clean and copy
rm -rf "$DEST"
cp -r "$SRC" "$DEST"

echo "Screenshots copied to docs/fields/screenshots/"
echo "$(find "$DEST" -name '*.png' | wc -l) images"
