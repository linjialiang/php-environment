# 同时存在 clang 和 gcc 时，需指定 gcc 和 g++  作为编译器，PHP与GCC更友好
export CC=/usr/bin/gcc
export CXX=/usr/bin/g++
# 编译选项
../configure --prefix=/server/php/85/ \
--enable-fpm \
--with-fpm-user=php-fpm \
--with-fpm-group=php-fpm \
--with-fpm-systemd \
--with-openssl \
--with-zlib \
--with-curl \
--with-ffi \
--enable-gd \
--with-avif \
--with-webp \
--with-jpeg \
--with-xpm \
--with-freetype \
--enable-intl \
--enable-mbstring \
--with-pgsql=/server/postgres \
--with-pdo-pgsql=/server/postgres \
--enable-sockets \
--with-zip \
--without-sqlite3 \
--without-pdo-sqlite > stdout.log
