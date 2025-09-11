项目运行过程中，可能需要额外的扩展支持，这时我们不可避免要去安装动态扩展

::: code-group

```bash [解压扩展包]
tar -xzf apcu-5.1.27.tgz
tar -xzf mongodb-2.1.1.tgz
tar -xzf redis-6.2.0.tgz
tar -xzf xdebug-3.4.5.tgz
tar -xzf yaml-2.2.5.tgz
```

```ini [84配置扩展]
; path /server/php/84/lib/php.ini
extension=redis
extension=mongodb
extension=yaml
extension=apcu

;zend_extension=opcache
;部署环境请注释掉 xdebug
zend_extension=xdebug

[xdebug]
;部署环境请关闭 xdebug
; xdebug.mode=off
xdebug.mode=develop,coverage,debug,gcstats,profile,trace
xdebug.client_host=127.0.0.1
; xdebug.client_host=192.168.66.254
xdebug.client_port=9084

[apcu]
; 虽然手册里写的默认开启，但实测时 apcu-5.1.27 版本默认是禁用的
apc.enabled=1
; 为 CLI 版本的PHP启用APC,主要用于测试和调试
apc.enable_cli=1
```

```bash [测试扩展]
# 测试加入环境变量的 PHP 版本的扩展
php --ri xdebug
php --ri redis
php --ri mongodb
php --ri yaml
php --ri apcu

# 测试指定PHP版本的扩展
/server/php/84/bin/php --ri xdebug
/server/php/84/bin/php --ri redis
/server/php/84/bin/php --ri mongodb
/server/php/84/bin/php --ri yaml
/server/php/84/bin/php --ri apcu
```

:::

### 1. xdebug 扩展

::: code-group

```bash [84编译]
cd /home/php-fpm/php_ext/xdebug-3.4.5
/server/php/84/bin/phpize
./configure --with-php-config=/server/php/84/bin/php-config
make -j4 > make.log
make install
```

:::

### 2. redis 扩展

::: code-group

```bash [84]
cd /home/php-fpm/php_ext/redis-6.2.0
/server/php/84/bin/phpize
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
/server/php/84/bin/phpize
./configure --with-php-config=/server/php/84/bin/php-config
make -j4 > make.log
make test > makeTest.log
make install
```

:::

### 4. yaml 扩展

::: code-group

```bash [84]
cd /home/php-fpm/php_ext/yaml-2.2.5
/server/php/84/bin/phpize
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
/server/php/84/bin/phpize
./configure --with-php-config=/server/php/84/bin/php-config
make -j4 > make.log
make test
make install
```

:::
