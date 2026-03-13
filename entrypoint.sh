#!/bin/bash
set -e

# Normalize Railway env var names to standard ones
export MYSQL_HOST="${MYSQLHOST:-${MYSQL_HOST:-localhost}}"
export MYSQL_USER="${MYSQLUSER:-${MYSQL_USER:-wordpress}}"
export MYSQL_PASSWORD="${MYSQLPASSWORD:-${MYSQL_PASSWORD:-}}"
export MYSQL_DATABASE="${MYSQLDATABASE:-${MYSQL_DATABASE:-wordpress}}"
export MYSQL_PORT="${MYSQLPORT:-${MYSQL_PORT:-3306}}"

# Configure PHP-FPM to use a unix socket
cat > /usr/local/etc/php-fpm.d/zz-custom.conf <<'FPMCONF'
[www]
listen = /var/run/php-fpm.sock
listen.owner = www-data
listen.group = www-data
listen.mode = 0660
FPMCONF

# Remove default nginx config that conflicts
rm -f /etc/nginx/sites-enabled/default
ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default

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

    if [ -z "$R2_DB_URL" ]; then
        echo "Error: R2_DB_URL not set. Set it to the public or presigned URL of the SQL dump."
        exit 1
    fi

    echo "Downloading database dump..."
    curl -L -o /tmp/db.sql.gz "$R2_DB_URL"

    echo "Importing..."
    gunzip -c /tmp/db.sql.gz | mysql -h"${MYSQL_HOST}" -u"${MYSQL_USER}" -p"${MYSQL_PASSWORD}" "${MYSQL_DATABASE}"

    rm /tmp/db.sql.gz

    # Update site URL to match current deployment
    if [ -n "$WP_HOME" ]; then
        echo "Updating site URL to $WP_HOME..."
        wp option update siteurl "$WP_HOME" --allow-root --path=/var/www/html
        wp option update home "$WP_HOME" --allow-root --path=/var/www/html
    fi

    echo "Database import complete."
else
    echo "Database already populated ($TABLE_COUNT tables), skipping import."
fi

exec "$@"
