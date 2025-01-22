../configure --prefix=/server/php/74/ \
--enable-fpm \
--with-fpm-user=php-fpm \
--with-fpm-group=php-fpm \
--with-fpm-systemd \
--with-openssl \
OPENSSL_LIBS=/server/openssl-1.1.1w/lib \
--with-pcre-jit \
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
--with-webp \
--with-jpeg \
--with-xpm \
--with-freetype \
--enable-mbstring \
--with-pgsql=/server/postgres \
--with-pdo-pgsql=/server/postgres \
--enable-sockets \
--enable-sysvmsg \
--enable-sysvsem \
--enable-sysvshm
