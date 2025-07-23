#!/bin/bash
projectPath=$1
branch=$2

now=$(date +"%Y-%m-%d %H:%M:%S")
logFile="$projectPath/storage/logs/front-deploy.log"

log() {
  echo "[$now] $1" | tee -a $logFile
}

log "🚀 Starting deployment in $projectPath on branch $branch"

cd $projectPath || { log "❌ Project path not found!"; exit 1; }

log "📥 Pulling changes from branch $branch..."
git pull origin $branch || { log "❌ Git pull failed"; exit 1; }

log "📦 Installing dependencies..."
npm install || { log "❌ npm install failed"; exit 1; }

log "📦 Building project dasbedas"
npm run build

log "🚀 Restarting app with PM2"
pm2 restart admin

log "✅ Deployment completed successfully."
exit 0
