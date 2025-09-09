---
title: 安装 PHP
titleTemplate: 环境搭建教程
---

# 安装 PHP

PHP（`PHP: Hypertext Preprocessor`，超文本预处理器的字母缩写）是一种被广泛应用的开放源代码的多用途脚本语言，它可嵌入到 HTML 中，尤其适合 web 开发。

构建 PHP 时可以直接指定 MySQL 的 socket 路径，所以建议在 MySQL 之后安装

::: tip 提示
也可以在 php.ini 配置文件里指定 MySQL 的 socket 路径
:::

## 扩展库

PHP 扩展库按加载时间可分为：`动态库(共享扩展)` 和 `静态库`

::: details 1. 动态库

动态库是在程序运行时链接的，动态库在编译时不会放到连接的目标程序中，即：可执行文件无法单独运行，动态库扩展名一般为：

| win     | unix   |
| ------- | ------ |
| `*.dll` | `*.so` |

:::

::: details 2. 静态库

静态库是在程序编译时链接的，静态库在编译时会直接整合到目标程序中，编译成功的可执行文件可独立运行，静态库扩展名一般为：

| win     | unix  |
| ------- | ----- |
| `*.lib` | `*.a` |

:::

::: details 3. 动态库和静态库区别

静态编译：静态库将需要的依赖在构建时，已经从系统 `拷贝` 到程序中，执行时就不需要调用系统的库，系统包移除时也不至于会让扩展失效，但是它会额外占用磁盘资源

动态编译：动态库构建了 1 个连接文件(`*.so`) ，用于连接程序和系统对应库的文件，执行时需要调用系统的库以及该库对应的依赖项

-   性能：静态库优于动态库，服务器硬件配置较差的，应该倾向于静态库
-   体积：静态库体积大于动态库
-   升级：静态库升级需要重新构建 PHP，动态库升级只需要重新生成连接文件(`*.so`)即可
-   稳定：静态库更加稳定，对于动态库来讲，如果系统对应库升级，与动态库对应的构建版本不一致，可能会遇到兼容性问题

:::

::: details 4. 扩展库构建类型选择

-   使用频繁的扩展库，推荐使用静态编译
-   更新频繁的扩展库，建议使用动态编译
-   非官方认可扩展库，建议使用动态编译

[官方认可扩展库列表](https://www.php.net/manual/zh/extensions.membership.php) 里的扩展，如有必要均可用静态库的方式构建，安全性和稳定性都由官方验证过

:::

## 准备工作

开始之前我们需要先使用预先准备好的 bash 脚本，解压文件和授权目录，具体参考 [脚本文件](../index#脚本文件)

本次计划构建 2 个 php 版本：

1. `php 7.4.x`
2. `php 8.3.x`

## 静态编译 PECL 扩展 {#pecl-static-bulid}

本次静态编译下面这些 PECL 扩展：

1. apcu
2. redis
3. yaml
4. rdkafka

### 1. 拷贝扩展源码

将 PECL 扩展源码拷贝到 php 的 ext 目录下

::: code-group

```bash [进扩展目录]
cd /home/php-fpm/php_ext
```

```bash [拷贝到8.3]
cp -p -r apcu-5.1.23 /home/php-fpm/php-8.3.12/ext/apcu
cp -p -r redis-6.0.2 /home/php-fpm/php-8.3.12/ext/redis
cp -p -r yaml-2.2.3 /home/php-fpm/php-8.3.12/ext/yaml
```

```bash [拷贝到7.4]
cp -p -r apcu-5.1.23 /home/php-fpm/php-7.4.33/ext/apcu
cp -p -r redis-6.0.2 /home/php-fpm/php-7.4.33/ext/redis
cp -p -r yaml-2.2.3 /home/php-fpm/php-7.4.33/ext/yaml
```

:::

### 2. 重新生成 php 配置

PHP 增加新扩展后，需要使用 `autoconf` 工具重新生成 `配置脚本-configure`

::: details 安装 autoconf 工具

```bash
apt install autoconf -y
```

:::

::: details 重新生成 configure 配置脚本

::: code-group

```bash [8.3重新生成]
cd /home/php-fpm/php-8.3.12/
mv configure{,.original}
./buildconf --force
```

```bash [7.4重新生成]
cd /home/php-fpm/php-7.4.33/
mv configure{,.original}
./buildconf --force
```

:::

## pkg-config

构建 php 时，自己编译的依赖包需要手动加入到 pkg-config 中

使用 export 是临时加入环境变量中，但会永久记录在编译后的可执行文件信息里

### 1. 查看临时环境变量

查看当前终端临时的环境变量，避免多次引入路径，造成混淆

```bash
echo $PKG_CONFIG_PATH
```

### 2. 加入临时环境变量

将所需路径加入到当前终端临时的环境变量中

::: code-group

```bash [加入单个路径]
export PKG_CONFIG_PATH=/path/to/pkgConfig_1:$PKG_CONFIG_PATH
```

```bash [加入多个路径]
export PKG_CONFIG_PATH=/path/to/pkgConfig_1:/path/to/pkgConfig_2:$PKG_CONFIG_PATH
```

:::

### 3. 查看环境变量列表

使用下面指令，可以查看当前终端的全部环境变量列表

```bash
pkg-config --list-all
```

## 构建 PHP

### 1. 安装依赖

本次 PHP 编译，系统还需要如下依赖项：

```bash
apt install g++ libsystemd-dev libsqlite3-dev libcurl4-openssl-dev libffi-dev libgmp-dev libonig-dev libsodium-dev libyaml-dev libzip-dev -y

# php 8.3.0 开始如果要启用 capstone
apt install libcapstone-dev -y

# libpq-dev 包含 libpq库(是 PostgreSQL 官方的客户端库)，用于与 PostgreSQL 服务器进行通信
apt install libpq-dev -y

# rdkafka
apt install librdkafka-dev -y
```

::: tip

-   不同版本所需依赖项可能不同
-   使用更多外部扩展，所需依赖项也会更多
-   php 较低版本如果要在新版的 linux 系统上安装，很多依赖都需要自己去重新构建

所以很多时候你要学会去阅读 `configure` 的报错提，以及掌握 linux 软件包的编译安装
:::

### 2. 创建构建目录

```bash
mkdir /home/php-fpm/php-7.4.33/build_php
mkdir /home/php-fpm/php-8.3.12/build_php
```

### 3. 环境变量

构建 PHP 需要额外将 `sqlite3` 的 `pkgconfig` 目录加入到临时环境变量里

```bash
export PKG_CONFIG_PATH=/server/sqlite3/lib/pkgconfig:$PKG_CONFIG_PATH

# 使用下面指令检查，sqlite3 是否正确加入
pkg-config --list-all | grep sqlite3

# 加入成功显示：
sqlite3          SQLite - SQL database engine
```

::: tip 如果服务器上未安装 sqlite3，则需要安装 `libsqlite3-dev` 依赖库，这样也不用将 `pkgconfig` 加入到 PKG_CONFIG_PATH 环境变量中
:::

### 4. 查看构建选项

```bash
# 全部构建选项
./configure -h

# 扩展构建选项
./configure -h | grep redis
./configure -h | grep yaml

# 导出构建选项
./configure -h > configure.txt
```

::: details 构建选项预览
::: code-group
<<<@/assets/environment-old/source/php/configure/74.ini [7.4]
<<<@/assets/environment-old/source/php/configure/83.ini [8.3]
:::

### 5. 进入构建目录

```bash
# php7.4 构建目录
cd /home/php-fpm/php-7.4.33/build_php/
# php8.3 构建目录
cd /home/php-fpm/php-8.3.12/build_php/
```

### 6. 安装指令

::: details 构建指令参考
::: code-group
<<<@/assets/environment-old/source/php/build/83.bash [8.3]
<<<@/assets/environment-old/source/php/build/74.bash [7.4]
:::

::: tip 构建指令区别：
php8.1 默认已经对 openssl 启用 `pcre-jit` 实现正则即时编译

php8.1 的 gd 扩展增加的 `--with-avif` 选项

php8.3 增加 `--with-capstone` 选项
:::

### 7. 编译并安装

```bash
# nohup make -j2 &
make -j2
make test
make install
```

## PHP 配置

`php.ini` 是 PHP 的配置文件，具体选项可以阅读 [官方手册](https://www.php.net/manual/zh/ini.php)

### 1. 配置文件模板

php 编译完成后，在源码包根目录下会自动生成两个推荐的配置文件模版

-   开发环境推荐： `php.ini-development`
-   部署环境推荐： `php.ini-production`

### 2. 配置文件路径

通过下面的指令可以快速获取到 PHP 配置文件存放路径

::: code-group

```bash [使用 php 程序]
# php7.4
/server/php/74/bin/php --ini
# php8.3
/server/php/83/bin/php --ini
```

```bash [使用 php-config 程序]
# php7.4
/server/php/74/bin/php-config --ini-path
# php8.3
/server/php/83/bin/php-config --ini-path
```

:::

### 3. 拷贝配置文件 {#copy-config-file}

::: code-group

```bash [部署环境]
# php7.4
cp /home/php-fpm/php-7.4.33/php.ini-production /server/php/74/lib/php.ini
# php8.3
cp /home/php-fpm/php-8.3.12/php.ini-production /server/php/83/lib/php.ini
```

```bash [开发环境]
# php7.4
cp /home/php-fpm/php-7.4.33/php.ini-development /server/php/74/lib/php.ini
# php8.3
cp /home/php-fpm/php-8.3.12/php.ini-development /server/php/83/lib/php.ini
```

:::

### 4. 检测配置文件

使用 php 程序，快速检测配置文件使用加载成功

```bash
# php7.4
/server/php/74/bin/php --ini
# php8.3
/server/php/83/bin/php --ini
```

### 5. 开启 OPcache

PHP 官方明确说明 OPcache 只允许编译为共享扩展，并且默认会构建它

使用 `--disable-opcache` 选项可以禁止构建

-   开启方式

    在 `php.ini` 第 953 行，将 `;` 去掉

    ```ini
    zend_extension=opcache
    ```

-   性能配置

    在 `php.ini` 第 1796 行，加入以下内容，可获得较好性能

    ```ini
    # 检查脚本时间戳是否有更新的周期，以秒为单位
    opcache.revalidate_freq=60
    ```

::: danger 警告
开启 `opcache` 扩展，会导致数据被缓存，可能无法获取到最新数据，所以线上环境必须经过严格测试

如果你不明白自己在做什么，最好不要开启它
:::

## PHP-FPM 配置

Nginx 只能处理静态页面，如果要处理 php 脚本，就必须借助 PHP-FPM 这些 FastCGI 进程管理器

1. nginx 将内容转发给 PHP-FPM
2. PHP-FPM 接管 php 统一处理
3. 处理后产生的返回值再返回给 nginx
4. 最终由 nginx 输出

### 1. 配置文件分类

PHP-FPM 配置文件可分成两种

1. 主进程配置文件：管理整个 PHP-FPM 服务的
2. 工作池进程配置文件：管理单个工作池进程的

::: details 主进程：

主进程(master)配置文件，是针对整个 PHP-FPM 的通用配置

-   路径： `/server/php/etc/php-fpm.conf`
-   数量： 有且仅有，1 个
-   需求： 主进程配置文件必须存在
-   默认： 默认未创建
-   模板： `/server/php/etc/php-fpm.conf.default`

:::

::: details 工作池进程：

工作池进程(pool)配置文件，是针对单个工作进程的配置文件

-   路径： `/server/php/etc/php-fpm.d/*.conf`
-   数量： 允许多个
-   需求： 至少需要 1 个工作吃进程配置文件
-   默认： 默认未创建
-   模板： `/server/php/etc/php-fpm.d/www.conf.default`

:::

### 2. 主进程配置文件

PHP-FPM 的主配置文件选项基本上都是使用默认，所以案例选项很少

::: details php 主配置文件案例
::: code-group
<<<@/assets/environment-old/source/php/83/php-fpm.conf{ini} [8.3]
<<<@/assets/environment-old/source/php/74/php-fpm.conf{ini} [7.4]
:::

### 3. 工作池配置文件

PHP-FPM 工作池进程配置文件有多个，并且支持随意命名，但为了更好的管理，我们最好遵循一些规则：

1. 针对单独站点 : 跟 nginx 站点配置文件命名一致
2. 根据工作池性质 :
    - 接收 tp6 站点，命名 `tp.conf`；
    - 接收其它站点，命名 `default.conf`

::: details 通用工作池案例
::: code-group
<<<@/assets/environment-old/source/php/83/php-fpm.d/default.conf{ini} [8.3]
<<<@/assets/environment-old/source/php/74/php-fpm.d/default.conf{ini} [7.4]
:::

::: details ThinkPHP 项目专用工作池案例
<<<@/assets/environment-old/source/php/83/php-fpm.d/tp.conf{ini}
:::

::: tip
更多参数说明，请阅读 [PHP 手册](https://www.php.net/manual/zh/install.fpm.configuration.php)
:::

## Systemd 管理

PHP-FPM 自带了一套比较完善的进程管理指令，编译完成后还会在构建目录下生成 Systemd Unit 文件

::: details 默认模板

::: code-group
<<<@/assets/environment-old/source/service/php/source/83/php-fpm.service{ini} [php8.3]
<<<@/assets/environment-old/source/service/php/source/74/php-fpm.service{ini} [php7.4]
:::

::: details 案例参考

::: code-group
<<<@/assets/environment-old/source/service/php/php83-fpm.service{bash} [php8.3]
<<<@/assets/environment-old/source/service/php/php74-fpm.service{ini} [php7.4]
:::

```bash
# 创建单元文件
mv /path/php*-fpm.service /usr/lib/systemd/system/
# 重载Systemd
systemctl daemon-reload
# 加入systemctl服务，并立即开启
systemctl enable --now php83-fpm
systemctl enable --now php74-fpm
```

::: tip 注意事项：

1. 1 个 `unix-socket`，对应 1 个 `php-fpm` 工作进程
2. 1 个 php-fpm 工作进程配置文件对应 1 个 unix-socket
3. 多个配置文件，不允许指向同一个 unix-socket，会出现冲突
4. 每个配置文件：
    - 必须设置单独的 `socket` 文件路径，如：tp6.sock、default.sock
    - 可以设置自己的用户，如：www、nginx、php-fpm、nobody

:::

## Composer

Composer 是一个 PHP 依赖管理工具，开发环境必备

::: danger 警告
不建议在部署环境安装 `git` `composer` `npm` 等开发环境工具

请全部在开发环境处理好，然后拷贝进服务器即可
:::

### 1. 安装

推荐直接使用腾讯云镜像 [下载 composer](https://mirrors.tencent.com/composer/composer.phar)

```bash
su - php-fpm -s /bin/zsh
cd /server/php/83/bin
curl -O https://mirrors.tencent.com/composer/composer.phar
chmod 770 composer.phar

# 软链接到 /usr/local/bin
ln -s /server/php/83/bin/composer.phar /usr/local/bin/composer
```

::: tip 更多国内镜像

> 当前腾讯云是最正常的

-   腾讯云：`composer config -g repos.packagist composer https://mirrors.cloud.tencent.com/composer/`
-   阿里云：`composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/`
-   华为云：`composer config -g repo.packagist composer https://repo.huaweicloud.com/repository/php/`

:::

### 2. 全量镜像

推荐全局配置阿里云的 Composer 全量镜像

```bash
# 切换到开发者用户
su emad
# 使用国内 Composer 全量镜像
/server/php/83/bin/php /usr/local/bin/composer config -g repo.packagist composer https://mirrors.cloud.tencent.com/composer/

# 取消使用国内 Composer 全量镜像
/server/php/83/bin/php /usr/local/bin/composer config -g --unset repos.packagist
```

### 3. 升级

升级 composer 也非常简单，建议使用国内全量镜像后再升级

```bash
# 切换到php-fpm用户，只能从root进入
su - php-fpm -s /bin/zsh
/server/php/83/bin/php /usr/local/bin/composer self-update
```

## 网页版数据库管理工具

将 adminer、phpMyAdmin、phpRedisAdmin 加入到默认站点

```bash
mv adminer-xxx.php /server/default/adminer.php
mv phpMyAdmin-xxx/ /server/default/pma
mv phpRedisAdmin-xxx/ /server/default/pra
```

### 1. adminer

adminer 使用 pdo 链接数据库，支持管理多种数据库，不需要额外配置

::: tip
adminer 无需配置
:::

::: details 将服务器上 sql 文件导入到数据库

```
- sql文件路径：文件必须和 adminer.php 在同级目录下
- sql文件名称：`adminer.sql` 或者 `adminer.sql.gz`
```

:::

### 2. phpMyAdmin

phpMyAdmin 使用 mysqli 链接，支持管理 MariaDB、MySQL

::: tip
phpMyAdmin 需要配置
:::

1. details 新建配置文件

    在 pma 根目录下新建 config.inc.php 文件

    ```bash
    cd /server/default/pma/
    vim config.inc.php
    ```

    ::: details 配置文件内容
    <<<@/assets/environment-old/source/php/config.inc.php
    :::

    ::: tip
    pma 的密文 `$cfg['blowfish_secret']` 参数需要重新设置
    :::

2. pma 密文参数

    `$cfg['blowfish_secret']` 参数用于设置 pma 密文，支持如下字符类型组合：

    - 数值: `0-9`
    - 大写字母: `A-Z`
    - 小写字母: `a-z`
    - ascii 特殊字符: `\~!@#$%^&*()_+-=[]{}\|;:'"/?.>,<`

### 3. phpRedisAdmin

phpRedisAdmin 需要使用 composer 安装依赖后，才能正常使用

```bash
su emad
cd /server/default/pra/
composer install
```

::: danger 警告
通常不使用 `composer update` 指令，它有可能导致程序依赖被破坏

对于任何线上项目，都应该尽可能减少使用 `composer update` 的次数
:::

## 升级 PHP

升级 PHP 跟正常编译几乎一样，下面是注意事项：

1. 安装依赖

    - PHP 跨主版本更新，必须重新编译安装动态扩展；
    - PHP 跨次版本更新，建议重新编译安装动态扩展；
    - PHP 小版本更新，如果 PHP 并未修改动态扩展，就不用重新编译安装动态扩展。

2. 重命名执行程序
   执行 `make install` 之前，先将 `sbin/php-fpm` 文件重命名，实现平滑升级

    ```bash
    # php8.3
    mv /server/php/83/sbin/php-fpm{,.bak}
    ```

3. 配置文件 `php.ini`

    - 小版本升级，需要修改配置文件，除非遇到 PHP 非常特殊的情况，
    - 其他版本升级，就直接替换掉配置文件，然后将需要的动态扩展重新加上去即可

::: danger 警告
服务器升级 PHP 乃至任何软件升级前，都应该先存快照，备份一份
:::

## 动态安装 PECL 扩展

在为项目增加功能的时候，可能需要额外的扩展支持，这时候我们不可避免要去安装动态扩展

而 PHP 官方显然也是考虑到这一点，所以动态安装这块也非常轻松

本次计划提供下面几个 PECL 扩展的动态安装案例：

1. [imagick](https://pecl.php.net/package/imagick)
2. [xdebug](https://xdebug.org/download)
3. [swoole](https://www.swoole.com/download)
4. [rdkafka](https://pecl.php.net/package/rdkafka)

### 1. imagick

imagick 需要先安装依赖库 [ImageMagick](https://download.imagemagick.org/ImageMagick/download/)

::: details 安装 ImageMagick

```bash
apt install libtool -y
# 如果 make check 没有报错，下面这些依赖可以不用安装
apt install libheif-dev liblcms2-dev libopenjp2-7-dev liblqr-1-0-dev libopenexr-dev libwmf-dev libpango1.0-dev libraw-dev libraqm-dev libdjvulibre-dev libzstd-dev -y
mkdir /server/ImageMagick
cd /home/php-fpm/ImageMagick-7.1.0-51/
./configure --prefix=/server/ImageMagick/
make
make check
make install
```

:::

::: details 安装 Imagick 扩展

```bash
export PKG_CONFIG_PATH=/server/ImageMagick/lib/pkgconfig
cd /home/php-fpm/php_ext/imagick-3.7.0
phpize
# 构建指令
./configure \
--with-php-config=/server/php/83/bin/php-config \
# --with-php-config=/server/php/74/bin/php-config \
--with-imagick=/server/ImageMagick/
# 编译并安装
make
make test
make install
```

> `./configure` 指令检查有报错

:::

### 2. xdebug

xdebug 是 php 的断点调试工具

::: code-group

```bash [编译]
cd /home/php-fpm/php_ext/xdebug-3.3.2
phpize
./configure --enable-xdebug --with-php-config=/server/php/83/bin/php-config
# ./configure --enable-xdebug --with-php-config=/server/php/74/bin/php-config
make -j4 > make.log
make install
```

```ini [配置]
# /server/php/83/lib/php.ini

[xdebug]
zend_extension=xdebug
xdebug.mode=develop,trace,debug
xdebug.client_host=127.0.0.1
; xdebug.client_host=192.168.6.254
xdebug.client_port=9083
; xdebug.client_port=9074
```

:::

::: tip 同时使用 Xdebug 和 OPCache 时，`zend_extension=xdebug` 须在 `zend_extension=opcache` 下面：

```ini
zend_extension=opcache
zend_extension=xdebug
```

:::

### 3. swoole

Swoole 是一个使用 C++ 语言编写的基于异步事件驱动和协程的并行网络通信引擎，为 PHP 提供协程、高性能网络编程支持

### 4. rdkafka

::: code-group

```bash [安装]
# 安装依赖库 librdkafka
apt install librdkafka-dev -y

# 安装 php-rdkafka 扩展
cd /home/php-fpm/php_ext/rdkafka-6.0.3
phpize
./configure --with-php-config=/server/php/83/bin/php-config
make -j2
make install
```

```ini [配置]
# /server/php/83/lib/php.ini
extension=rdkafka
```

:::

### 5. MongoDB

在实际工作中 PostgreSQL 通常可以取代 MySQL 和 MongoDB

::: code-group

```bash [安装]
cd /home/php-fpm/php_ext/mongodb-1.19.2
phpize
./configure --enable-mongodb --with-php-config=/server/php/83/bin/php-config
make -j2
make test
make install
```

```ini [配置]
# /server/php/83/lib/php.ini
extension=mongodb
```

:::

::: tip 开启动态扩展

```ini
extension=imagick
extension=swoole
extension=mongodb
extension=rdkafka
```

:::

### 即将移除的扩展

lnpp 里即将移除的扩展

1. yaml 可使用 `symfony/yaml` 包替代
2. rdkafka 有需求再安装动态扩展
3. apcu 有需求再安装动态扩展
4. redis 移除，考虑使用 Postgres 替代
5. mongodb 移除，完全转到 Postgres
6. mysql 移除，完全转到 Postgres

## 权限

::: code-group

```bash [部署]
chown php-fpm:php-fpm -R /server/php /server/logs/php
find /server/php /server/logs/php -type f -exec chmod 640 {} \;
find /server/php /server/logs/php -type d -exec chmod 750 {} \;
# 可执行文件需要执行权限
chmod 750 -R /server/php/83/bin /server/php/83/sbin
```

```bash [开发]
# 权限同部署环境
# 开发用户 emad 加入 lnpp包用户组
usermod -a -G sqlite,redis,postgres,mysql,php-fpm,nginx emad
```

:::
