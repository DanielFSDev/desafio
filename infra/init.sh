#!/bin/sh

until mysql -h app_db -u app -papp -e 'select 1'; do
  >&2 echo "Aguardando o banco de dados..."
  sleep 5
done
>&2 echo "Banco de dados está pronto!"

path_dir=$( cd /var/www/html/ && pwd )
apache_user='www-data:www-data'
control_file="$path_dir/storage/logs/migrate_installed.flag"

cd "$path_dir" \
    && mkdir -p "$path_dir"/storage/logs \
    && touch "$path_dir"/storage/logs/laravel.log \
    && composer install -d "$path_dir" \
    && mkdir -p "$path_dir"/storage/framework/sessions/ \
    && mkdir -p "$path_dir"/storage/framework/cache/data \
    && mkdir -p "$path_dir"/storage/framework/views/ \
    && mkdir -p "$path_dir"/public \
    && mkdir -p "$path_dir"/bootstrap/cache/ \
    && mkdir -p "$path_dir"/storage/proxies/ \
    && mkdir -p "$path_dir"/storage/app/ \
    && chown -R $apache_user "$path_dir"/storage/app \
    && chown -R $apache_user "$path_dir"/storage/framework \
    && chown -R $apache_user "$path_dir"/storage/logs \
    && chown -R $apache_user "$path_dir"/storage/proxies \
    && chown -R $apache_user "$path_dir"/bootstrap/cache/ \
    && rm -rf bootstrap/cache/*.php \
    && chown -R $apache_user "$path_dir"/vendor/bin/* \

if [ ! -f "$control_file" ]; then
    php artisan migrate:install \
        && php artisan migrate \
        && touch "$control_file"
fi
