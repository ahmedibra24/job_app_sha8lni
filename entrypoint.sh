#!/bin/sh
set -e

echo "Caching config..."
php artisan config:clear
php artisan package:discover --ansi
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Waiting for TiDB (SSL connection)..."

MAX_TRIES=10
COUNTER=0

until php -r "
try {
    new PDO(
        'mysql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_DATABASE'),
        getenv('DB_USERNAME'),
        getenv('DB_PASSWORD'),
        [
            PDO::MYSQL_ATTR_SSL_CA => getenv('MYSQL_ATTR_SSL_CA')
        ]
    );
    echo 'DB Connected';
} catch (Exception \$e) {
    echo \$e->getMessage();
    exit(1);
}
"; do
  COUNTER=$((COUNTER+1))
  echo "Database not ready... attempt $COUNTER/$MAX_TRIES"
  sleep 3

  if [ $COUNTER -ge $MAX_TRIES ]; then
    echo "Skipping DB wait..."
    break
  fi
done

echo "Starting Laravel server..."
exec php -S 0.0.0.0:80 -t public