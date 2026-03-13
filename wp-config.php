<?php

// Support both Railway (MYSQLHOST) and standard (MYSQL_HOST) env var names
define('DB_NAME',     getenv('MYSQLDATABASE') ?: getenv('MYSQL_DATABASE') ?: 'wordpress');
define('DB_USER',     getenv('MYSQLUSER')     ?: getenv('MYSQL_USER')     ?: 'wordpress');
define('DB_PASSWORD', getenv('MYSQLPASSWORD') ?: getenv('MYSQL_PASSWORD') ?: '');
define('DB_HOST',     getenv('MYSQLHOST')     ?: getenv('MYSQL_HOST')     ?: 'localhost');
define('DB_CHARSET',  'utf8mb4');
define('DB_COLLATE',  '');

$table_prefix = getenv('WP_TABLE_PREFIX') ?: 'wp_';

// Auth keys — generate fresh ones for production via https://api.wordpress.org/secret-key/1.1/salt/
define('AUTH_KEY',         getenv('AUTH_KEY')         ?: 'put-unique-phrase-here');
define('SECURE_AUTH_KEY',  getenv('SECURE_AUTH_KEY')  ?: 'put-unique-phrase-here');
define('LOGGED_IN_KEY',    getenv('LOGGED_IN_KEY')    ?: 'put-unique-phrase-here');
define('NONCE_KEY',        getenv('NONCE_KEY')        ?: 'put-unique-phrase-here');
define('AUTH_SALT',        getenv('AUTH_SALT')         ?: 'put-unique-phrase-here');
define('SECURE_AUTH_SALT', getenv('SECURE_AUTH_SALT')  ?: 'put-unique-phrase-here');
define('LOGGED_IN_SALT',   getenv('LOGGED_IN_SALT')    ?: 'put-unique-phrase-here');
define('NONCE_SALT',       getenv('NONCE_SALT')        ?: 'put-unique-phrase-here');

// Site URL — set via env so it matches Railway's assigned domain
if (getenv('WP_HOME')) {
    define('WP_HOME',    getenv('WP_HOME'));
    define('WP_SITEURL', getenv('WP_HOME'));
}

define('WP_DEBUG', getenv('WP_DEBUG') === 'true');
define('WP_DEBUG_LOG', getenv('WP_DEBUG') === 'true');
define('DISALLOW_FILE_EDIT', true);
define('DISALLOW_FILE_MODS', true);

if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}

require_once ABSPATH . 'wp-settings.php';
