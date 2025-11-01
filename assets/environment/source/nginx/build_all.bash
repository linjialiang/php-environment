# 在存在 clang 和 gcc 的情况，因为GCC优化更好，所以需专门指定 gcc 和 g++ 作为解释器
export CC=gcc
export CXX=g++
# 编译选项
./configure --prefix=/server/nginx \
--builddir=/home/nginx/nginx-1.28.0/build_nginx \

# 核心功能模块
--with-threads \
--with-file-aio \

# 自带http功能模块全部启用
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

# prel 是试验性的
--with-http_perl_module \

# 启用邮箱服务
--with-mail \
--with-mail_ssl_module \

# 启用负载均衡服务
--with-stream \
--with-stream_ssl_module \
--with-stream_realip_module \
--with-stream_geoip_module \
--with-stream_ssl_preread_module \

# 外库路径
--with-pcre=/home/nginx/pcre2-10.47 \
--with-pcre-jit \
--with-zlib=/home/nginx/zlib-1.3.1 \
--with-openssl=/home/nginx/openssl-3.5.4

# 开启调试，生产环境下建议禁用
--with-debug
# 由于nginx错误和警告均使用正常输出结果，所以 nginx 不建议使用 > stdout.log