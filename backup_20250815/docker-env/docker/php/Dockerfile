# 使用イメージの指定（より安定なバージョン指定）
FROM php:7.4-fpm

# composer イメージ内から所定のファイルをコピー
COPY --from=composer:2.4 /usr/bin/composer /usr/bin/composer

# 必要なファイルのみを先にコピー（キャッシュ効率化）
COPY app/composer.json app/composer.lock* /var/www/html/

# 一つのRUNコマンドでまとめて実行（レイヤー数削減）
RUN apt-get update && apt-get install -y \
    git \
    zip unzip \
    curl \
    gnupg \
    --no-install-recommends \
    && docker-php-ext-install pdo_mysql \
    && curl -sL https://deb.nodesource.com/setup_16.x | bash - \
    && apt-get install -y nodejs --no-install-recommends \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# composerの依存関係を先にインストール（キャッシュ活用）
WORKDIR /var/www/html
RUN if [ -f composer.json ]; then composer install --no-dev --optimize-autoloader --no-scripts; fi

# アプリケーションファイルをコピー
COPY app/ /var/www/html/

# 最終設定
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage