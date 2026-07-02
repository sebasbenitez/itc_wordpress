#!/bin/bash

# WoodMart Images Optimizer Release Script
# Usage: ./scripts/release.sh 1.0.1

set -e

# Check if version argument provided
if [ -z "$1" ]; then
    echo "❌ Usage: $0 <version>"
    echo "Example: $0 1.0.1"
    exit 1
fi

VERSION=$1
echo "🚀 Preparing release v$VERSION"

# Validate version format (semantic versioning)
if [[ ! $VERSION =~ ^[0-9]+\.[0-9]+\.[0-9]+$ ]]; then
    echo "❌ Invalid version format. Use semantic versioning (e.g., 1.0.1)"
    exit 1
fi

# Check if we're in the right directory
if [ ! -f "woodmart-images-optimizer.php" ]; then
    echo "❌ Run this script from the plugin root directory"
    exit 1
fi

# Check if git working directory is clean
if [ -n "$(git status --porcelain)" ]; then
    echo "❌ Git working directory is not clean. Commit or stash changes first."
    git status --short
    exit 1
fi

echo "📝 Updating version numbers..."

# Update version in main plugin file
sed -i.bak "s/Version: .*/Version: $VERSION/" woodmart-images-optimizer.php
sed -i.bak "s/WOODMART_IMAGES_OPTIMIZER_VERSION', '[^']*'/WOODMART_IMAGES_OPTIMIZER_VERSION', '$VERSION'/" woodmart-images-optimizer.php

# Update version in readme.txt
sed -i.bak "s/Stable tag: .*/Stable tag: $VERSION/" readme.txt

# Remove backup files
rm -f *.bak

echo "📅 Updating changelog..."

# Get current date
CURRENT_DATE=$(date +%Y-%m-%d)

# Prepare changelog entry
if [ -f "CHANGELOG.md" ]; then
    # Create temporary file with new entry
    {
        echo "# Changelog"
        echo ""
        echo "All notable changes to the WoodMart Images Optimizer plugin will be documented in this file."
        echo ""
        echo "The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),"
        echo "and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html)."
        echo ""
        echo "## [$VERSION] - $CURRENT_DATE"
        echo ""
        echo "### Changed"
        echo "- Version bump to $VERSION"
        echo ""
        # Skip the header lines and append existing content
        tail -n +8 CHANGELOG.md
    } > CHANGELOG.tmp && mv CHANGELOG.tmp CHANGELOG.md
fi

echo "🔍 Validating changes..."

# Quick validation
php -l woodmart-images-optimizer.php || exit 1

echo "📊 Changes summary:"
echo "  • Plugin version: $VERSION"
echo "  • Readme stable tag: $VERSION"
echo "  • Changelog updated: $CURRENT_DATE"

echo ""
read -p "🤔 Do you want to commit and tag this release? (y/N): " -n 1 -r
echo

if [[ $REPLY =~ ^[Yy]$ ]]; then
    echo "📝 Committing changes..."
    git add woodmart-images-optimizer.php readme.txt CHANGELOG.md
    git commit -m "Prepare v$VERSION release

- Update plugin version to $VERSION
- Update readme.txt stable tag
- Update changelog with release date"

    echo "🏷️ Creating git tag..."
    git tag "v$VERSION" -m "Release v$VERSION"

    echo "✅ Release v$VERSION prepared successfully!"
    echo ""
    echo "Next steps:"
    echo "1. Push changes: git push origin main"
    echo "2. Push tag: git push origin v$VERSION"
    echo "3. GitHub Actions will automatically create the release"
    echo ""
    echo "Or push both at once:"
    echo "git push origin main && git push origin v$VERSION"
else
    echo "🚫 Release cancelled. Changes have been made but not committed."
    echo "To revert: git checkout -- woodmart-images-optimizer.php readme.txt CHANGELOG.md"
fi 