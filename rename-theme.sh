#!/usr/bin/env bash
# rename-theme.sh — rename this theme to a new project slug
#
# Usage:
#   ./rename-theme.sh <slug> "<Theme Name>"
#
# Example:
#   ./rename-theme.sh martinez2026 "Martinez 2026"
#
# What it replaces:
#   geller2026   → <slug>          (function prefixes, text domain, block namespaces, CSS classes)
#   Geller 2026  → <Theme Name>    (human-readable name in style.css, block descriptions, patterns)
#   Estudio Geller → <Theme Name>  (placeholder copy in footer block)

set -euo pipefail

OLD_SLUG="geller2026"
OLD_NAME="Geller 2026"
OLD_LONG="Estudio Geller"

NEW_SLUG="${1:-}"
NEW_NAME="${2:-}"

# ── Validate args ──────────────────────────────────────────────────────────────

if [[ -z "$NEW_SLUG" || -z "$NEW_NAME" ]]; then
  echo "Usage: ./rename-theme.sh <slug> \"<Theme Name>\""
  echo "  slug       lowercase, no spaces (e.g. martinez2026)"
  echo "  Theme Name human-readable name  (e.g. \"Martinez 2026\")"
  exit 1
fi

if [[ "$NEW_SLUG" =~ [^a-z0-9_-] ]]; then
  echo "Error: slug must be lowercase letters, numbers, hyphens, or underscores only."
  exit 1
fi

if [[ "$NEW_SLUG" == "$OLD_SLUG" ]]; then
  echo "Error: new slug is the same as the current slug."
  exit 1
fi

echo ""
echo "Renaming theme:"
echo "  Slug: $OLD_SLUG → $NEW_SLUG"
echo "  Name: $OLD_NAME → $NEW_NAME"
echo ""

# ── Files to process ──────────────────────────────────────────────────────────

# All source files (excluding build/, node_modules/, and this script itself)
FILES=$(find . \
  -type f \
  \( -name "*.php" -o -name "*.json" -o -name "*.js" -o -name "*.scss" \
     -o -name "*.css" -o -name "*.html" -o -name "*.md" \) \
  ! -path "./build/*" \
  ! -path "./node_modules/*" \
  ! -path "./package-lock.json" \
  ! -name "rename-theme.sh" \
)

# ── Replace in files ───────────────────────────────────────────────────────────

echo "Replacing strings in files..."

for FILE in $FILES; do
  # Order matters: replace longer/more-specific strings first
  sed -i '' \
    -e "s/${OLD_LONG}/${NEW_NAME}/g" \
    -e "s/${OLD_NAME}/${NEW_NAME}/g" \
    -e "s/${OLD_SLUG}/${NEW_SLUG}/g" \
    "$FILE"
done

echo "Done."
echo ""

# ── Remind about build/ ────────────────────────────────────────────────────────

echo "Next steps:"
echo "  1. Run: npm run build"
echo "     (build/ still references the old slug — rebuild to regenerate it)"
echo ""
echo "  2. Rename the theme folder itself:"
echo "     cd .. && mv $OLD_SLUG $NEW_SLUG"
echo ""
echo "  3. Update style.css if needed (Author, Description, Theme URI)."
echo ""
echo "  4. Commit: git add -A && git commit -m \"Rename theme to $NEW_SLUG\""
echo ""
