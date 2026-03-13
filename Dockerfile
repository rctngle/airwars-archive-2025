FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    nginx \
    supervisor \
    less \
    mariadb-client \
    curl \
    unzip \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        gd \
        mysqli \
        pdo_mysql \
        zip \
        intl \
        mbstring \
        exif \
        opcache \
    && rm -rf /var/lib/apt/lists/*

# Install WP-CLI
RUN curl -o /usr/local/bin/wp https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
    && chmod +x /usr/local/bin/wp

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set up WordPress
WORKDIR /var/www/html
RUN php -d memory_limit=512M /usr/local/bin/wp core download --version=6.7.2 --allow-root

# Install free plugins (direct download — no DB needed at build time)
RUN cd /var/www/html/wp-content/plugins \
    && curl -sL "https://downloads.wordpress.org/plugin/custom-post-type-ui.latest-stable.zip" -o cptui.zip && unzip -q cptui.zip && rm cptui.zip \
    && curl -sL "https://downloads.wordpress.org/plugin/classic-editor.latest-stable.zip" -o ce.zip && unzip -q ce.zip && rm ce.zip \
    && curl -sL "https://downloads.wordpress.org/plugin/redirection.latest-stable.zip" -o red.zip && unzip -q red.zip && rm red.zip \
    && curl -sL "https://downloads.wordpress.org/plugin/disable-comments.latest-stable.zip" -o dc.zip && unzip -q dc.zip && rm dc.zip \
    && curl -sL "https://downloads.wordpress.org/plugin/seamless-sticky-custom-post-types.latest-stable.zip" -o sscpt.zip && unzip -q sscpt.zip && rm sscpt.zip

# Install ACF Pro (license key provided at build time)
ARG ACF_PRO_KEY
RUN if [ -n "$ACF_PRO_KEY" ]; then \
        cd /var/www/html/wp-content/plugins \
        && curl -sL -o acf-pro.zip "https://connect.advancedcustomfields.com/v2/plugins/download?p=pro&k=${ACF_PRO_KEY}&t=6.3" \
        && unzip -q acf-pro.zip && rm acf-pro.zip; \
    fi

# Copy theme
COPY theme/ /var/www/html/wp-content/themes/airwars-new/

# Install theme Composer dependencies
RUN cd /var/www/html/wp-content/themes/airwars-new && composer install --no-dev --no-interaction

# Copy config files
COPY nginx.conf /etc/nginx/sites-available/default
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Fix permissions
RUN chown -R www-data:www-data /var/www/html

EXPOSE 8080

ENTRYPOINT ["/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
