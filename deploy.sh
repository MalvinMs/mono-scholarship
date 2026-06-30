#!/bin/bash
# ============================================================
# Zero-downtime deployment script — Platform Beasiswa
# ============================================================
# Usage:
#   ./deploy.sh <deploy-path> <git-branch>
#
# Example:
#   ./deploy.sh /var/www/beasiswa main
# ============================================================

set -euo pipefail

DEPLOY_PATH="${1:-/var/www/beasiswa}"
GIT_BRANCH="${2:-main}"
RELEASE_DIR="${DEPLOY_PATH}/releases/$(date +'%Y%m%d_%H%M%S')"
SHARED_DIR="${DEPLOY_PATH}/shared"
RELEASES_DIR="${DEPLOY_PATH}/releases"
CURRENT_DIR="${DEPLOY_PATH}/current"
KEEP_RELEASES=5

info()  { echo -e "\033[1;34m==>\033[0;36m $1\033[0m"; }
ok()    { echo -e "\033[1;32m  -> $1\033[0m"; }
err()   { echo -e "\033[1;31m  -> $1\033[0m" >&2; }

for cmd in git php npm composer; do
    if ! command -v "$cmd" &>/dev/null; then
        err "Required command not found: $cmd"
        exit 1
    fi
done

info "Deploying branch '${GIT_BRANCH}' to '${DEPLOY_PATH}'"

# --- Step 1: Checkout ---
info "1. Cloning release"
mkdir -p "${RELEASE_DIR}"
git archive "${GIT_BRANCH}" 2>/dev/null | tar -x -C "${RELEASE_DIR}" || \
    git clone --depth=1 --branch="${GIT_BRANCH}" "${DEPLOY_PATH}/.git" "${RELEASE_DIR}" 2>/dev/null || {
        err "Failed to checkout branch ${GIT_BRANCH}"
        exit 1
    }
ok "Release created: $(basename "${RELEASE_DIR}")"

# --- Step 2: Link shared resources ---
info "2. Linking shared resources"
ln -sfn "${SHARED_DIR}/.env" "${RELEASE_DIR}/.env"
rm -rf "${RELEASE_DIR}/storage"
ln -sfn "${SHARED_DIR}/storage" "${RELEASE_DIR}/storage"
ok "Shared resources linked"

# --- Step 3: Install dependencies ---
info "3. Installing PHP dependencies"
cd "${RELEASE_DIR}"
composer install --no-dev --optimize-autoloader --no-interaction 2>&1
ok "Composer dependencies installed"

# --- Step 4: Build frontend ---
info "4. Building frontend"
npm ci --no-audit --no-fund 2>&1
npm run build 2>&1
ok "Frontend built"

# --- Step 5: Database migration ---
info "5. Running database migrations"
php artisan migrate --force 2>&1
ok "Migrations complete"

# --- Step 6: Cache ---
info "6. Caching configuration"
php artisan config:cache 2>&1
php artisan route:cache 2>&1
php artisan event:cache 2>&1
php artisan view:cache 2>&1
php artisan optimize 2>&1
ok "Cache built"

# --- Step 7: Atomic symlink switch ---
info "7. Switching symlink (atomic)"
ln -sfn "${RELEASE_DIR}" "${CURRENT_DIR}-tmp"
mv -Tf "${CURRENT_DIR}-tmp" "${CURRENT_DIR}"
ok "Current release switched to: $(basename "${RELEASE_DIR}")"

# --- Step 8: Graceful Octane reload ---
info "8. Reloading Octane"
cd "${CURRENT_DIR}"
php artisan octane:reload 2>&1 || true
ok "Octane reloaded"

# --- Step 9: Restart queue ---
info "9. Restarting queue workers"
php artisan queue:restart 2>&1 || true
ok "Queue restart signaled"

# --- Step 10: Cleanup old releases ---
info "10. Cleaning up old releases"
cd "${RELEASES_DIR}"
ls -t | tail -n +$((KEEP_RELEASES + 1)) | while read -r old_release; do
    rm -rf "${RELEASES_DIR}/${old_release}"
    ok "Removed old release: ${old_release}"
done
ok "Cleanup complete"

info "Deployment successful: $(basename "${RELEASE_DIR}")"
