#!/bin/bash
set -e

# Normalize Railway env var names to standard ones
export MYSQL_HOST="${MYSQLHOST:-${MARIADB_HOST:-${MYSQL_HOST:-localhost}}}"
export MYSQL_USER="${MYSQLUSER:-${MARIADB_USER:-${MYSQL_USER:-wordpress}}}"
export MYSQL_PASSWORD="${MYSQLPASSWORD:-${MARIADB_PASSWORD:-${MYSQL_PASSWORD:-}}}"
export MYSQL_DATABASE="${MYSQLDATABASE:-${MARIADB_DATABASE:-${MYSQL_DATABASE:-wordpress}}}"
export MYSQL_PORT="${MYSQLPORT:-${MARIADB_PORT:-${MYSQL_PORT:-3306}}}"

# Configure PHP-FPM to use a unix socket
cat > /usr/local/etc/php-fpm.d/zz-custom.conf <<'FPMCONF'
[www]
user = www-data
group = www-data
listen = /var/run/php-fpm.sock
listen.owner = www-data
listen.group = www-data
listen.mode = 0660
pm = dynamic
pm.max_children = 5
pm.start_servers = 2
pm.min_spare_servers = 1
pm.max_spare_servers = 3
FPMCONF

# Remove default nginx config that conflicts
rm -f /etc/nginx/sites-enabled/default
ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default

# Generate wp-config.php
echo "Generating wp-config.php..."
wp config create \
    --dbname="${MYSQL_DATABASE}" \
    --dbuser="${MYSQL_USER}" \
    --dbpass="${MYSQL_PASSWORD}" \
    --dbhost="${MYSQL_HOST}:${MYSQL_PORT}" \
    --allow-root \
    --path=/var/www/html \
    --skip-check \
    --force
wp config set WP_HOME "${WP_HOME:-http://localhost:8080}" --allow-root --path=/var/www/html
wp config set WP_SITEURL "${WP_HOME:-http://localhost:8080}" --allow-root --path=/var/www/html
wp config set FORCE_SSL_ADMIN true --raw --allow-root --path=/var/www/html

# Trust Railway's reverse proxy for HTTPS detection
sed -i "/\/\* That's all, stop editing!/i\\
if (isset(\$_SERVER['HTTP_X_FORWARDED_PROTO']) \&\& \$_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {\\
    \$_SERVER['HTTPS'] = 'on';\\
}" /var/www/html/wp-config.php

# Wait for MySQL to be ready
echo "Waiting for MySQL..."
until mysqladmin ping -h"${MYSQL_HOST}" -u"${MYSQL_USER}" -p"${MYSQL_PASSWORD}" --silent 2>/dev/null; do
    sleep 2
done
echo "MySQL is ready."

# Check if database needs importing
TABLE_COUNT=$(mysql -h"${MYSQL_HOST}" -u"${MYSQL_USER}" -p"${MYSQL_PASSWORD}" "${MYSQL_DATABASE}" \
    -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='${MYSQL_DATABASE}';" -sN 2>/dev/null || echo "0")

if [ "$TABLE_COUNT" -lt 5 ]; then
    echo "Database is empty — importing from R2..."

    if [ -z "$R2_BUCKET" ] || [ -z "$R2_DB_KEY" ]; then
        echo "Error: R2_BUCKET and R2_DB_KEY must be set (e.g. R2_BUCKET=my-bucket, R2_DB_KEY=backups/db.sql.gz)"
        exit 1
    fi

    if [ -z "$R2_ACCESS_KEY_ID" ] || [ -z "$R2_SECRET_ACCESS_KEY" ] || [ -z "$R2_ENDPOINT" ]; then
        echo "Error: R2_ACCESS_KEY_ID, R2_SECRET_ACCESS_KEY, and R2_ENDPOINT must be set"
        exit 1
    fi

    export AWS_ACCESS_KEY_ID="$R2_ACCESS_KEY_ID"
    export AWS_SECRET_ACCESS_KEY="$R2_SECRET_ACCESS_KEY"

    echo "Downloading database dump from R2..."
    aws s3 cp "s3://${R2_BUCKET}/${R2_DB_KEY}" /tmp/db.sql.gz --endpoint-url "$R2_ENDPOINT"

    echo "Importing..."
    gunzip -c /tmp/db.sql.gz | mysql -h"${MYSQL_HOST}" -u"${MYSQL_USER}" -p"${MYSQL_PASSWORD}" "${MYSQL_DATABASE}"

    rm /tmp/db.sql.gz

    echo "Database import complete."
else
    echo "Database already populated ($TABLE_COUNT tables), skipping import."
fi

# Sync static data files from R2
if [ -n "$R2_ACCESS_KEY_ID" ] && [ -n "$R2_SECRET_ACCESS_KEY" ] && [ -n "$R2_ENDPOINT" ] && [ -n "$R2_BUCKET" ]; then
    export AWS_ACCESS_KEY_ID="$R2_ACCESS_KEY_ID"
    export AWS_SECRET_ACCESS_KEY="$R2_SECRET_ACCESS_KEY"

    # Only fetch files needed by client-side JS (served via nginx)
    THEME_DATA="/var/www/html/wp-content/themes/airwars-new/data/conflict-data-static"
    mkdir -p "$THEME_DATA"
    for f in gaza-neighbourhoods.json coalition-ekia.json; do
        if [ ! -f "$THEME_DATA/$f" ]; then
            echo "Fetching $f from R2..."
            aws s3 cp "s3://${R2_BUCKET}/data/$f" "$THEME_DATA/$f" --endpoint-url "$R2_ENDPOINT" || true
        fi
    done
    chown -R www-data:www-data "$THEME_DATA"
fi

exec "$@"
