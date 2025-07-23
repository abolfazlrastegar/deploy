#!/bin/bash
set -e
projectPath=$1
branch=$2

SUPERVISOR_CONF="/etc/supervisor/supervisord.conf"
SUPERVISOR_CONF_DIR="/etc/supervisor/conf.d"
DEPLOY_CONF_DIR="$projectPath/supervisor"

if [ -z "$projectPath" ]; then
  echo "❌ Project path is required. Usage: ./deploy-get/deploy.sh $projectPath [$branch]"
  exit 1
fi

if [ -z "$branch" ]; then
  branch="main"
fi

logFile="$projectPath/storage/logs/deploy.log"

log() {
  now=$(date +"%Y-%m-%d %H:%M:%S")
  echo "[$now] $1" | tee -a "$logFile"
}

log "🚀 Starting deployment in $projectPath on branch $branch"

cd "$projectPath" || { log "❌ Project path not found!"; exit 1; }

log "📥 Fetching and resetting to origin/$branch..."

git fetch origin
if ! git reset --hard origin/"$branch"; then
    log "⚠️ Git reset failed. Trying to clean broken ref and re-fetch..."

    rm -f .git/refs/remotes/origin/"$branch" 2>/dev/null || true
    git remote prune origin

    git fetch origin || { log "❌ Git fetch failed after cleanup .."; exit 1; }

    git reset --hard origin/"$branch" || { log "❌ Git reset still failed after cleanup .."; exit 1; }
fi


log "📦 Installing dependencies..."
composer install --no-dev --optimize-autoloader || { log "❌ Composer install failed .."; exit 1; }

log "⏳ Putting app into maintenance mode..."
php artisan down

log "🛠 Running migrations..."
php artisan migrate --force || { log "❌ Migration failed"; php artisan up; exit 1; }

log "⚙️ Clearing and rebuilding cache..."
php artisan config:clear
php artisan cache:clear
php artisan config:cache
composer dump-autoload

log "✅ Bringing app back up..."
php artisan up

log "🔁 Discovering worker configs..."
workers_to_restart=()

for conf_file in "$DEPLOY_CONF_DIR"/*.conf; do
    conf_filename=$(basename "$conf_file")
    worker_name="${conf_filename%.conf}"

    log "📄 Registering $worker_name ..."
    sudo cp "$conf_file" "$SUPERVISOR_CONF_DIR/$conf_filename"
    workers_to_restart+=("$worker_name:*")
done


if [ ! -f "$SUPERVISOR_CONF" ]; then
  log "❌ Supervisor config not found at $SUPERVISOR_CONF"
  exit 1
fi

log "🔄 Reloading Supervisor..."
sudo supervisorctl -c "$SUPERVISOR_CONF" reread
sudo supervisorctl -c "$SUPERVISOR_CONF" update

log "♻️ Restarting all discovered workers..."
for worker in "${workers_to_restart[@]}"; do
    log "🔁 Restarting $worker"
    sudo supervisorctl -c "$SUPERVISOR_CONF" restart "$worker"
done

log "🔌 Restarting socket with PM2...."
pm2 restart socket-server

log "✅ Deployment completed successfully...."
exit 0
