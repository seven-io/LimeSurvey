#!/bin/bash

# Build script for seven LimeSurvey plugin
# Creates a properly structured ZIP file for LimeSurvey plugin installation

set -e

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
BUILD_DIR="$SCRIPT_DIR/dist"
PLUGIN_NAME="seven"
VERSION=$(sed -n 's/^[[:space:]]*<version>\([0-9.]*\)<\/version>.*/\1/p' "$SCRIPT_DIR/config.xml" | head -1)
ZIP_NAME="seven-limesurvey-${VERSION}.zip"

echo "Building $PLUGIN_NAME plugin v$VERSION..."

# Clean up previous build
rm -rf "$BUILD_DIR"
mkdir -p "$BUILD_DIR/$PLUGIN_NAME"

# Copy only required files
cp "$SCRIPT_DIR/config.xml" "$BUILD_DIR/$PLUGIN_NAME/"
cp "$SCRIPT_DIR/seven.php" "$BUILD_DIR/$PLUGIN_NAME/"
cp "$SCRIPT_DIR/README.md" "$BUILD_DIR/$PLUGIN_NAME/"
cp "$SCRIPT_DIR/LICENSE" "$BUILD_DIR/$PLUGIN_NAME/"

# Create ZIP without macOS metadata
cd "$BUILD_DIR"
zip -r "$ZIP_NAME" "$PLUGIN_NAME" -x "*.DS_Store" -x "__MACOSX/*"

# Clean up temp folder
rm -rf "$BUILD_DIR/$PLUGIN_NAME"

echo ""
echo "✓ Build complete: dist/$ZIP_NAME"
echo ""
ls -lh "$BUILD_DIR/$ZIP_NAME"
