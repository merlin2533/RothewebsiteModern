#!/usr/bin/env bash
# =============================================================================
# scripts/fetch_originals.sh
# Holt alle Original-Bilder von rothe-transporte.de.
#
# Aufruf:
#   bash scripts/fetch_originals.sh
#   SOURCE_HOST=https://rothe-transporte.de bash scripts/fetch_originals.sh
#
# Voraussetzung: PHP 8.2+ mit curl-Extension, Internet-Zugriff vom Host.
# =============================================================================
set -euo pipefail

cd "$(dirname "$0")/.."

if ! command -v php >/dev/null 2>&1; then
    echo "Fehler: PHP CLI nicht gefunden." >&2
    exit 1
fi

php scripts/fetch_originals.php "$@"
