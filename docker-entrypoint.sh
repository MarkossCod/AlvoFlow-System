#!/bin/sh
set -e

# Se a Aiven exigir TLS, cole o conteúdo do ca.pem na env var DB_SSL_CA (no Render) —
# escrevemos para um arquivo aqui e apontamos MYSQL_ATTR_SSL_CA para ele.
if [ -n "$DB_SSL_CA" ]; then
    echo "$DB_SSL_CA" > /var/www/html/storage/aiven-ca.pem
    export MYSQL_ATTR_SSL_CA="/var/www/html/storage/aiven-ca.pem"
fi

php artisan config:clear
php artisan migrate --force

# ponytail: usa o servidor embutido do Laravel (single-process) em vez de php-fpm+nginx —
# suficiente para o volume de um balcão de publicações; trocar por php-fpm+nginx se o
# tráfego crescer e precisar de mais concorrência.
exec php artisan serve --host 0.0.0.0 --port "${PORT:-10000}"
