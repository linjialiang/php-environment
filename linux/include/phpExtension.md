---
title: 安装和配置共享扩展
titleTemplate: Linux 下纯手工搭建 PHP 环境
---

# 安装和配置共享扩展

项目运行过程中，可能需要额外的扩展支持，这时我们不可避免要去安装共享扩展

::: code-group

```bash [解压扩展包]
cd /home/php/php_ext
echo apcu-5.1.28.tgz xdebug-3.5.0.tgz redis-6.3.0.tgz mongodb-2.1.8.tgz | xargs -n1 tar -xzf
```

```ini [85配置扩展]
; path /server/php/85/lib/php.ini
extension=apcu

;zend_extension=opcache
;部署环境请关闭 xdebug
zend_extension=xdebug

[xdebug]
;部署环境请关闭 xdebug
; xdebug.mode=off
;xdebug.mode=develop,coverage,debug,gcstats,profile,trace
xdebug.mode=develop,debug,trace
xdebug.client_host=127.0.0.1
; xdebug.client_host=192.168.66.254
xdebug.client_port=9085

[apcu]
; 手册里写的默认开启，实测情况如下：
;   apcu-5.1.27 版本默认禁用
;   apcu-5.1.28 版本默认启用
apcu.enabled=1
; 为 CLI 版本的PHP启用APC,主要用于测试和调试
apc.enable_cli=1

; 开发环境配置
apcu.shm_size=64M
apcu.stat=1        ; 开发时开启文件检查
apcu.ttl=300       ; 短时间缓存，便于调试

; 生产环境配置
; apcu.shm_size=256M
; apcu.stat=0         ; 关闭状态检查提升性能
; apcu.ttl=7200       ; 长时间缓存
; apcu.slam_defense=1 ; 防止缓存击穿
```

```bash [测试扩展]
# 测试加入环境变量的 PHP 版本的扩展
php --ri xdebug
php --ri apcu

# 测试指定PHP版本的扩展
/server/php/85/bin/php --ri xdebug
/server/php/85/bin/php --ri apcu
```

:::

## 1. xdebug 扩展

::: code-group

```bash [85编译]
cd /home/php/php_ext/xdebug-3.5.0
/server/php/85/bin/phpize
./configure --with-php-config=/server/php/85/bin/php-config
make -j4
make install
```

:::

## 2. apcu 扩展

::: code-group

```bash [85]
cd /home/php/php_ext/apcu-5.1.28
/server/php/85/bin/phpize
./configure --with-php-config=/server/php/85/bin/php-config
make -j4
make test
make install
```

```ini [配置参考]
[apcu]
; 手册里写的默认开启，实测情况如下：
;   apcu-5.1.27 版本默认禁用
;   apcu-5.1.28 版本默认启用
apcu.enabled=1
; 为 CLI 版本的PHP启用APC,主要用于测试和调试
apc.enable_cli=1

; 开发环境配置
apcu.shm_size=64M
apcu.stat=1        ; 开发时开启文件检查
apcu.ttl=300       ; 短时间缓存，便于调试

; 生产环境配置
; apcu.shm_size=256M
; apcu.stat=0         ; 关闭状态检查提升性能
; apcu.ttl=7200       ; 长时间缓存
; apcu.slam_defense=1 ; 防止缓存击穿
```

:::

## 3. redis 扩展

::: code-group

```bash [85]
cd /home/php/php_ext/redis-6.3.0
/server/php/85/bin/phpize
./configure --with-php-config=/server/php/85/bin/php-config
make -j4
make test
make install
```

:::

## 4. mongodb 扩展

::: code-group

```bash [85]
cd /home/php/php_ext/mongodb-2.1.8
/server/php/85/bin/phpize
./configure --with-php-config=/server/php/85/bin/php-config
make -j4
make test
make install
```

:::

::: warning 说明
yaml 格式的配置文件性能并没有 php 格式的配置文件好，所以这里就不考虑安装 yaml 库了
:::
