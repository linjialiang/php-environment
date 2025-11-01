# 同时存在 clang 和 gcc 时，需指定 gcc 和 g++  作为编译器，nginx与GCC更友好
# nginx 可能用不到 c++ 编译器，这个未测试
export CC=gcc
export CXX=g++
# 编译选项
./configure --prefix=/server/nginx \
--builddir=/home/nginx/nginx-1.28.0/build_nginx \
--without-select_module \
--without-poll_module \
--with-threads \
--with-file-aio \
--with-http_ssl_module \
--with-http_v2_module \
--with-http_v3_module \
--with-http_realip_module \
--with-http_addition_module \
--with-http_xslt_module \
--with-http_image_filter_module \
--with-http_geoip_module \
--with-http_sub_module \
--with-http_dav_module \
--with-http_flv_module \
--with-http_mp4_module \
--with-http_gunzip_module \
--with-http_gzip_static_module \
--with-http_auth_request_module \
--with-http_random_index_module \
--with-http_secure_link_module \
--with-http_degradation_module \
--with-http_slice_module \
--with-http_stub_status_module \
--with-pcre=/home/nginx/pcre2-10.47 \
--with-pcre-jit \
--with-zlib=/home/nginx/zlib-1.3.1 \
--with-openssl=/home/nginx/openssl-3.5.4
# 由于nginx错误和警告均使用正常输出结果，所以 nginx 不建议使用 > stdout.log
