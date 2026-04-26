#!/usr/bin/env bash
# =============================================================================
# Backup script for rothe-transporte.de
# Creates a single timestamped tar.gz containing:
#   - data/database.sqlite (consistent .backup snapshot)
#   - uploads/ (all admin-uploaded media)
# Usage:
#   bash scripts/backup.sh                         # → backups/<timestamp>.tar.gz
#   BACKUP_DIR=/var/backups bash scripts/backup.sh # custom destination
#
# Cron example (daily 03:15, keep 14 days):
#   15 3 * * * cd /var/www/rothe && bash scripts/backup.sh && \
#              find backups -name '*.tar.gz' -mtime +14 -delete
# =============================================================================
set -euo pipefail

REPO_ROOT="$(cd "$(dirname "$0")/.." && pwd)"
BACKUP_DIR="${BACKUP_DIR:-${REPO_ROOT}/backups}"
TS="$(date -u +%Y%m%dT%H%M%SZ)"

DB_FILE="${REPO_ROOT}/data/database.sqlite"
UPLOADS_DIR="${REPO_ROOT}/uploads"

mkdir -p "${BACKUP_DIR}"
WORK="$(mktemp -d)"
trap 'rm -rf "${WORK}"' EXIT

# 1. SQLite snapshot via .backup (safe even with active writers / WAL)
if [[ -f "${DB_FILE}" ]]; then
    if command -v sqlite3 >/dev/null 2>&1; then
        sqlite3 "${DB_FILE}" ".backup '${WORK}/database.sqlite'"
    else
        # Fallback: best-effort cp (acceptable when site is idle)
        cp -p "${DB_FILE}" "${WORK}/database.sqlite"
    fi
else
    echo "WARN: ${DB_FILE} not found – skipping DB snapshot." >&2
fi

# 2. Stage uploads
if [[ -d "${UPLOADS_DIR}" ]]; then
    cp -a "${UPLOADS_DIR}" "${WORK}/uploads"
fi

# 3. Pack
ARCHIVE="${BACKUP_DIR}/rothe-${TS}.tar.gz"
( cd "${WORK}" && tar czf "${ARCHIVE}" . )

# 4. Report
SIZE="$(du -h "${ARCHIVE}" | cut -f1)"
echo "✓ ${ARCHIVE} (${SIZE})"
