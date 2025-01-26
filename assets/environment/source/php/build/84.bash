../configure --prefix=/server/php/84/ \
--enable-fpm \
--with-fpm-user=php-fpm \
--with-fpm-group=php-fpm \
--with-fpm-systemd \
--with-openssl \
--with-zlib \
--with-zip \
--enable-bcmath \
--enable-calendar \
--enable-intl \
--enable-exif \
--with-gettext \
--with-gmp \
--with-mhash \
--with-sodium \
--with-curl \
--with-ffi \
--enable-gd \
--with-avif \
--with-webp \
--with-jpeg \
--with-xpm \
--with-freetype \
--enable-mbstring \
--with-capstone \
--enable-mysqlnd \
--with-mysqli=mysqlnd \
--with-pdo-mysql=mysqlnd \
# postgres 根目录或 pg_config 路径
--with-pgsql=/server/postgres \
--with-pdo-pgsql=/server/postgres \
--enable-sockets \
--enable-sysvmsg \
--enable-sysvsem \
--enable-sysvshm
