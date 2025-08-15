#!/bin/bash

cd /var/www/html

# .envファイルが存在しない場合のみ作成
if [ ! -f .env ]; then
    cp .env.example .env
    sed -i -e 's/DB_HOST=127.0.0.1/DB_HOST=db/g' .env
    sed -i -e 's/DB_PASSWORD=/DB_PASSWORD=root/g' .env
fi

# composer installは既にDockerfileで実行済みの場合はスキップ
if [ ! -d "vendor" ]; then
    composer install --no-dev --optimize-autoloader
fi

# アプリケーションキーが設定されていない場合のみ生成
if ! grep -q "APP_KEY=base64:" .env; then
    php artisan key:generate
fi

# ストレージリンクが存在しない場合のみ作成
if [ ! -L public/storage ]; then
    php artisan storage:link
fi

# 権限設定
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# キャッシュクリア（必要な場合のみ）
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Startup completed successfully!"