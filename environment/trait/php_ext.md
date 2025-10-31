项目运行过程中，可能需要额外的扩展支持，这时我们不可避免要去安装动态扩展

::: code-group

```bash [解压扩展包]
tar -xzf apcu-5.1.27.tgz
tar -xzf xdebug-3.4.5.tgz
```

```ini [84配置扩展]
; path /server/php/84/lib/php.ini
extension=apcu

;zend_extension=opcache
;部署环境请关闭 xdebug
zend_extension=xdebug

[xdebug]
;部署环境请关闭 xdebug
; xdebug.mode=off
xdebug.mode=develop,coverage,debug,gcstats,profile,trace
xdebug.client_host=127.0.0.1
; xdebug.client_host=192.168.66.254
xdebug.client_port=9084

[apcu]
; 虽然手册里写的默认开启[2025-09-10]，但实测时 apcu-5.1.27 版本默认是禁用的
apc.enabled=1
; 为 CLI 版本的PHP启用APC,主要用于测试和调试
apc.enable_cli=1
```

```bash [测试扩展]
# 测试加入环境变量的 PHP 版本的扩展
php --ri xdebug
php --ri apcu

# 测试指定PHP版本的扩展
/server/php/84/bin/php --ri xdebug
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

:::

### 2. apcu 扩展

::: code-group

```bash [84]
cd /home/php-fpm/php_ext/apcu-5.1.27
/server/php/84/bin/phpize
./configure --with-php-config=/server/php/84/bin/php-config
make -j4 > make.log
make test
make install
```

```ini [配置参考]
; 开发环境配置
apcu.enabled=1
apcu.shm_size=64M
apcu.stat=1        ; 开发时开启文件检查
apcu.ttl=300       ; 短时间缓存，便于调试

; 生产环境配置
apcu.enabled=1
apcu.shm_size=256M
apcu.stat=0         ; 关闭状态检查提升性能
apcu.ttl=7200       ; 长时间缓存
apcu.slam_defense=1 ; 防止缓存击穿
```

:::

::: warning 说明
yaml 格式的配置文件性能并没有 php 格式的配置文件好，所以这里就不考虑安装 yaml 库了
:::
