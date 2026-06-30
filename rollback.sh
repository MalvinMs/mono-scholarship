#!/bin/bash
# ============================================================
# Rollback script — Platform Beasiswa
# ============================================================
# Switches current symlink to previous release + Octane reload
#
# Usage:
#   ./rollback.sh <deploy-path>
# ============================================================

set -euo pipefail

DEPLOY_PATH="${1:-/var/www/beasiswa}"
RELEASES_DIR="${DEPLOY_PATH}/releases"
CURRENT_DIR="${DEPLOY_PATH}/current"

PREVIOUS=$(ls -t "${RELEASES_DIR}" 2>/dev/null | sed -n '2p')

if [ -z "${PREVIOUS}" ]; then
    echo "Error: Tidak ada release sebelumnya untuk rollback."
    exit 1
fi

echo "==> Rolling back to: ${PREVIOUS}"

ln -sfn "${RELEASES_DIR}/${PREVIOUS}" "${CURRENT_DIR}-tmp"
mv -Tf "${CURRENT_DIR}-tmp" "${CURRENT_DIR}"
echo "  -> Symlink switched"

cd "${CURRENT_DIR}"
php artisan octane:reload || true
echo "  -> Octane reloaded"

php artisan queue:restart || true
echo "  -> Queue restart signaled"

echo "==> Rollback selesai: ${PREVIOUS}"
