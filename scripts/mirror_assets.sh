#!/usr/bin/env bash
# =============================================================================
# Mirror images from rothe-transporte.de into uploads/originals/
# Falls back gracefully if the site is unreachable.
# =============================================================================
set -uo pipefail

REPO_ROOT="$(cd "$(dirname "$0")/.." && pwd)"
TARGET="${REPO_ROOT}/uploads/originals"
LOG="${REPO_ROOT}/data/mirror.log"

mkdir -p "${TARGET}" "$(dirname "${LOG}")"
echo "─── Mirror started $(date -u +%Y-%m-%dT%H:%M:%SZ) ───" >> "${LOG}"

# 1. Probe the homepage
HTTP=$(curl -sL -o /tmp/_rothe_probe.html -w "%{http_code}" \
    -A "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36" \
    --max-time 15 https://rothe-transporte.de/ || echo "000")

echo "Homepage probe: HTTP ${HTTP}" | tee -a "${LOG}"

if [[ "${HTTP}" != "200" ]]; then
    echo "Site unreachable (HTTP ${HTTP}). Skipping mirror." | tee -a "${LOG}"
    echo "Run this script again from a network where rothe-transporte.de is reachable."
    exit 0
fi

# 2. Mirror images via wget if available, else curl-based fallback
if command -v wget >/dev/null 2>&1; then
    cd "${TARGET}" && wget --quiet --no-check-certificate \
        --recursive --level=2 --no-parent --no-host-directories \
        --accept jpg,jpeg,png,webp,svg,gif --no-clobber \
        --user-agent="Mozilla/5.0" \
        https://rothe-transporte.de/ 2>>"${LOG}" || true
    echo "Mirror complete via wget." | tee -a "${LOG}"
else
    echo "wget not found – using curl fallback for known image paths." | tee -a "${LOG}"
    grep -oE 'https://rothe-transporte\.de/[^"]+\.(jpg|jpeg|png|webp|svg|gif)' /tmp/_rothe_probe.html \
        | sort -u | while read -r URL; do
        FN="${TARGET}/$(basename "${URL}")"
        [[ -f "${FN}" ]] && continue
        curl -sL -A "Mozilla/5.0" -o "${FN}" "${URL}" && \
            echo "  ✓ $(basename "${URL}")" | tee -a "${LOG}" || \
            echo "  ✗ $(basename "${URL}")" | tee -a "${LOG}"
    done
fi

echo "─── Mirror finished $(date -u +%Y-%m-%dT%H:%M:%SZ) ───" >> "${LOG}"
echo "See ${LOG} for details. Files in: ${TARGET}"
