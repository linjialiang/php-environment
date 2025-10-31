## 用户及用户组

### 1. 设置站点基目录权限

::: code-group

```bash [部署]
chown root:root /www
chmod 755 /www
```

```bash [开发]
chown emad:emad /www
chmod 750 -R /www
```

:::

### 2. tp 站点权限案例

::: code-group

```bash [权限分析]
- 对于php文件，php-fpm 需要读取权限
- 对于页面文件，nginx 需要读取权限
- 对于上传文件，php-fpm 需要读写权限，nginx需要读取权限
- 对于缓存目录，php-fpm 需要读写权限
- 对于入口文件，php-fpm 需要读取权限, nginx不需要任何权限（直接走代理转发）
- 如果是开发环境，开发用户对所有文件都应该拥有读写权限
```

```bash [部署]
chown php-fpm:php-fpm -R /www/tp
find /www/tp -type f -exec chmod 440 {} \;
find /www/tp -type d -exec chmod 550 {} \;
# 部分目录需确保nginx可以访问和进入
chmod php-fpm:nginx -R /www/tp /www/tp/public /www/tp/public/static \
/www/tp/public/static/upload
# 部分文件需确保nginx可以访问
chmod 440 /www/tp/public/{favicon.ico,robots.txt}
# 缓存和上传目录需要写入权限
chmod 750 /www/tp/public/static/upload /www/tp/runtime
```

```bash [开发]
chown emad:emad -R /www/tp
find /www/tp -type f -exec chmod 640 {} \;
find /www/tp -type d -exec chmod 750 {} \;
chmod 770 /www/tp/public/static/upload
chmod 770 /www/tp/runtime
```

:::

### 3. laravel 站点权限案例

::: code-group

```bash [权限分析]
- 对于php文件，php-fpm 需要读取权限
- 对于页面文件，nginx 需要读取权限
- 对于上传文件，php-fpm需要读写权限，nginx需要读取权限
- 对于缓存目录，php-fpm需要读写权限
- 对于入口文件，php-fpm需要读取权限, nginx不需要任何权限（直接走代理转发）
- 如果是开发环境，开发用户对所有文件都应该拥有读写权限
```

```bash [部署]
chown php-fpm:php-fpm -R /www/laravel
find /www/laravel -type f -exec chmod 440 {} \;
find /www/laravel -type d -exec chmod 550 {} \;
# 部分目录需确保nginx可以访问和进入
chmod php-fpm:nginx -R /www/laravel /www/laravel/public \
/www/laravel/public/static /www/laravel/public/static/upload
# 部分文件需确保nginx可以访问
chmod 440 /www/laravel/public/{favicon.ico,robots.txt}
# 缓存和上传目录需要写入权限
chmod 750 /www/laravel/public/static/upload
find /www/laravel/storage/ -type d -exec chmod 750 {} \;
```

```bash [开发]
chown emad:emad -R /www/laravel
find /www/laravel -type f -exec chmod 640 {} \;
find /www/laravel -type d -exec chmod 750 {} \;
# php读写 nginx读
chmod 770 /www/laravel/public/static/upload
# php读写
find /www/laravel/storage/* -type d -exec chmod 770 {} \;
```

:::
