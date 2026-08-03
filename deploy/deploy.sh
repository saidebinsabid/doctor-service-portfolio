#!/bin/bash
# ============================================================
#  cPanel ডেপ্লয় স্ক্রিপ্ট — প্রফেসর ডা. আবু সুফিয়ান
#  cPanel Git Version Control এই স্ক্রিপ্ট bash দিয়ে চালায় (SSH নেই)।
#  আসল কাজ এখানে; .cpanel.yml শুধু এটাকে ডাকে।
# ============================================================
set -euo pipefail

REPO=/home/drabusuf/doctor-service-portfolio
APPDIR="$REPO/website"
DOCROOT=/home/drabusuf/public_html
PHP=/opt/alt/php82/usr/bin/php

echo "==> Deploy shuru | APPDIR=$APPDIR | PHP=$PHP"
cd "$APPDIR"

# ---- 1) composer.phar (server-e composer nei) ----
if [ ! -f composer.phar ]; then
  echo "==> composer.phar download hocche"
  curl -fsSL https://getcomposer.org/download/latest-stable/composer.phar -o composer.phar
fi

# ---- 2) directory skeleton (storage/bootstrap-cache repo-te track kora nei) ----
mkdir -p bootstrap/cache
mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views
mkdir -p storage/logs storage/app/public
chmod -R 775 storage bootstrap/cache || true

# ---- 3) PHP dependencies ----
echo "==> composer install --no-dev"
$PHP -d memory_limit=-1 composer.phar install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# ---- 4) .env thakte hobe (File Manager/API die aage bosano) ----
if [ ! -f .env ]; then
  echo "ERROR: .env nei ($APPDIR/.env). Deploy bondho."
  exit 1
fi

# ---- 5) filesystem cache clear (DB-nirbhor cache:clear noy — table na thakle bhange) ----
$PHP artisan config:clear
$PHP artisan route:clear
$PHP artisan view:clear

# ---- 6) prothombar provisioning: migrate + seed (marker die ekbar) ----
if [ ! -f storage/app/.provisioned ]; then
  echo "==> prothom provisioning: migrate --force --seed"
  $PHP artisan migrate --force --seed
  touch storage/app/.provisioned
fi

# ---- 7) niyontrito migration: storage/app/.run-migrations rakhle porer deploy-e cholbe ----
if [ -f storage/app/.run-migrations ]; then
  echo "==> niyontrito migrate"
  rm -f storage/app/.run-migrations
  $PHP artisan migrate --force
fi

# ---- 8) cache rebuild (ekhon table ache) ----
echo "==> artisan caches rebuild"
$PHP artisan config:cache
$PHP artisan route:cache
$PHP artisan view:cache

# ---- 9) built public/ -> docroot e copy (LiteSpeed symlink serve kore na) ----
echo "==> public/ -> $DOCROOT copy"
mkdir -p "$DOCROOT"
cp -a public/. "$DOCROOT"/
# custom booter (app ke ~/public_html theke boot kore, app folder web theke ogommo)
cp -f "$REPO/deploy/public_html-index.php" "$DOCROOT/index.php"
# uploads er jonno real storage dir (symlink noy)
mkdir -p "$DOCROOT/storage"

echo "==> Deploy SESH. https://drabusufian.com"
