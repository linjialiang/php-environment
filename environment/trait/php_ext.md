项目运行过程中，可能需要额外的扩展支持，这时我们不可避免要去安装动态扩展

::: code-group

```bash [依赖库]
# 使用 phpize 初始化 configure 配置文件时，需要 autoconf 依赖库
apt install autoconf -y
```

```ini [配置文件-默认]
# /server/php/84/lib/php.ini
extension=redis
extension=mongodb
extension=yaml
extension=apcu

;zend_extension=opcache
zend_extension=xdebug

[xdebug]
# xdebug.mode=off
xdebug.mode=develop,coverage,debug,gcstats,profile,trace
xdebug.client_host=127.0.0.1
# xdebug.client_host=192.168.66.254
xdebug.client_port=9084
```

```bash [测试扩展-默认]
# 加入环境变量的php版本
php --ri xdebug
php --ri redis
php --ri mongodb
php --ri yaml
php --ri apcu
```

```bash [测试8.4扩展]
/server/php/84/bin/php --ri xdebug
/server/php/84/bin/php --ri redis
/server/php/84/bin/php --ri mongodb
/server/php/84/bin/php --ri yaml
/server/php/84/bin/php --ri apcu
```

:::

### 1. xdebug 扩展

[xdebug](https://xdebug.org/download) 扩展安装案例：

::: code-group

```bash [84编译]
cd /home/php-fpm/php_ext/xdebug-3.4.5
phpize
./configure --with-php-config=/server/php/84/bin/php-config
make -j4 > make.log
make install
```

```ini [84配置]
# 如果 Xdebug 和 OPCache 同时使用，xdebug 必须在 opcache 之后：
zend_extension=opcache
zend_extension=xdebug

[xdebug]
# xdebug.mode=off
xdebug.mode=develop,coverage,debug,gcstats,profile,trace
xdebug.client_host=127.0.0.1
# xdebug.client_host=192.168.66.254
xdebug.client_port=9084
```

:::

### 2. redis 扩展

::: code-group

```bash [84]
cd /home/php-fpm/php_ext/redis-6.2.0
phpize
./configure --with-php-config=/server/php/84/bin/php-config
make -j4 > make.log
make test
make install
```

:::

### 3. MongoDB 扩展 {#ext-mongodb}

::: code-group

```bash [84]
cd /home/php-fpm/php_ext/mongodb-2.1.1
phpize
./configure --with-php-config=/server/php/84/bin/php-config
make -j4 > make.log
make test
make install
```

:::

### 4. yaml 扩展

::: code-group

```bash [84]
# 安装依赖库
apt install libyaml-dev -y

cd /home/php-fpm/php_ext/yaml-2.2.5
phpize
./configure --with-php-config=/server/php/84/bin/php-config
make -j4 > make.log
make test
make install
```

:::

### 5. apcu 扩展

::: code-group

```bash [84]
cd /home/php-fpm/php_ext/apcu-5.1.27
phpize
./configure --with-php-config=/server/php/84/bin/php-config
make -j4 > make.log
make test
make install
```

:::
