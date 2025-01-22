---
title: 安装 PHP
titleTemplate: 环境搭建教程
---

# 安装 PHP

PHP（`PHP: Hypertext Preprocessor`，超文本预处理器的字母缩写）是一种被广泛应用的开放源代码的多用途脚本语言，它可嵌入到 HTML 中，尤其适合 web 开发。

## 准备工作

开始之前我们需要先使用预先准备好的 bash 脚本，解压文件和授权目录，具体参考 [脚本文件](./index#脚本文件)

本次编译安装 PHP 的方式，允许同时构建多个 php 版本，如: `php-7.4.33` + `php-8.4.x`

::: tip 变更说明

-   从 2025/01/22 开始重新启用 `php-7.4.33` 以支撑旧项目；
-   从 2025/01/22 开始正式移除 `php-8.3.x` 版本，其相关内容请阅读[[PHP旧版]](./archive/php_old)；

:::

## 构建 PHP

### 1. 安装依赖

本次 PHP 编译过程中，在系统原有扩展存在下，还需安装如下依赖项：

::: code-group

```bash [84]
apt install libcurl4-openssl-dev libpng-dev libavif-dev libwebp-dev \
libjpeg-dev libxpm-dev libfreetype-dev libgmp-dev libonig-dev libcapstone-dev \
libsodium-dev libzip-dev -y

# php 扩展所需额外依赖
apt install autoconf libyaml-dev -y
```

```bash [74]
# 在安装完 新版PHP所需依赖后，PHP 7.4 还需依赖如下：
# 未发现需要其他依赖项
```

:::

::: details debian12 纯净版所需完整依赖

```bash
apt install -y xz-utils
apt install -y make gcc g++ libcurl4-openssl-dev libpng-dev pkg-config \
libavif-dev libwebp-dev libjpeg-dev libxpm-dev libfreetype-dev libgmp-dev \
libonig-dev libcapstone-dev libsodium-dev libzip-dev libsystemd-dev libxml2-dev \
libffi-dev

# sqlite3 扩展所需依赖，已安装sqlite3则不需要
apt install libsqlite3-dev -y
# pgsql 扩展所需依赖，已安装PostgreSQL则不需要
apt install libpq-dev -y
# php 安装动态扩展所需依赖
apt install autoconf libyaml-dev -y
```

:::

::: details sqlite3 依赖

想使用最新或指定版 sqlite3 ，需自己编译好 sqlite3 后，在 `PKG_CONFIG_PATH` 环境变量中追加 sqlite3 的 `pkgconfig` 配置文件路径

::: code-group

```bash{4} [编译安装sqlite3]
usermod -a -G postgres php-fpm

# 构建 PHP 需将 sqlite3 的 pkgconfig 目录加入到临时环境变量里
export PKG_CONFIG_PATH=/server/sqlite/lib/pkgconfig:$PKG_CONFIG_PATH

# 使用下面指令检查，sqlite3 是否正确加入
pkg-config --list-all | grep sqlite3

# 加入成功显示：
sqlite3          SQLite - SQL database engine
```

```bash [使用依赖库]
# 未安装 sqlite3，则需安装 libsqlite3-dev 依赖库
# 这中方式不用将 pkgconfig 加入到 PKG_CONFIG_PATH 环境变量中
apt install libsqlite3-dev -y
```

:::

::: details pgsql 依赖

想使用最新或指定版 pgsql，需自己编译好 libpq 库后，在 php 构建选项里指定目录路径

::: code-group

```bash{5,6} [编译安装Postgres]
usermod -a -G postgres php-fpm

# PHP 的构建选项需指定Postgres安装目录
../configure --prefix=/server/php/84/ \
--with-pgsql=/server/postgres \
--with-pdo-pgsql=/server/postgres \
...
```

```bash [使用依赖库]
# 未安装 PostgreSQL 则需安装 libpq-dev 依赖库
# 该方式不需要指定postgres安装目录
apt install libpq-dev -y
```

:::

::: details 依赖 openssl 特殊版本

如果对 openssl 依赖库有特殊版本需求，需要自行编译安装

::: code-group

```bash{5-8} [编译安装openssl]
# 作为公共依赖库，推荐以root用户安装它
mkdir /server/openssl-1.1.1w
cd /root/openssl-1.1.1w/

./config --prefix=/server/openssl-1.1.1w \
--openssldir=/server/openssl-1.1.1w \
no-shared \
zlib

make -j4
make test
make install
...
```

```bash{2,8-10} [php编译选项]
# 设置新的 PKG_CONFIG_PATH，排除系统默认的 OpenSSL 库路径
export PKG_CONFIG_PATH=/server/openssl-1.1.1w/lib/pkgconfig:$PKG_CONFIG_PATH

# 使用下面指令检查，是否正确替换
pkg-config --path openssl,libssl,libcrypto

# 成功替换展示：
/server/openssl-1.1.1w/lib/pkgconfig/openssl.pc
/server/openssl-1.1.1w/lib/pkgconfig/libssl.pc
/server/openssl-1.1.1w/lib/pkgconfig/libcrypto.pc

# 未成功替换展示：
/usr/lib/x86_64-linux-gnu/pkgconfig/openssl.pc
/usr/lib/x86_64-linux-gnu/pkgconfig/libssl.pc
/usr/lib/x86_64-linux-gnu/pkgconfig/libcrypto.pc
```

::: danger 重要说明
php-7.4 不支持 Debian12 自带的 openssl-3.0.x 版本，
因此这里自行编译安装了 [[openssl-1.1.1w]](https://openssl-library.org/source/old/1.1.1/index.html)

:::

::: tip 提示

1. 本次已编译 SQLite3，无需额外使用依赖库
    - 确保 php 用户对 SQLite3 的 pkgconfig 目录有 `读取` 和 `执行` 权限
2. 本次已编译 Postgres，无需额外使用依赖库
    - 确保 php 用户对 Postgres 安装目录要有 `读取` 和 `执行` 权限
3. 不同版本所需依赖项可能不同
4. 使用更多外部扩展，所需依赖项也会更多
5. php 较低版本如果要在新版的 linux 系统上安装，很多依赖可能都需要自己重新

通常你需要自己去阅读 `configure` 的错误提示，以及掌握 linux 软件包的编译安装
:::

### 2. 创建并进入构建目录

::: code-group

```bash [84]
mkdir /home/php-fpm/php-8.4.3/build_php
cd /home/php-fpm/php-8.4.3/build_php/
```

```bash [74]
mkdir /home/php-fpm/php-7.4.33/build_php
cd /home/php-fpm/php-7.4.33/build_php/
```

:::

### 3. 查看构建选项

::: code-group

```bash [common]
# 全部构建选项
./configure -h

# 扩展构建选项
./configure -h | grep pgsql
./configure -h | grep sqlite

# 导出构建选项
./configure -h > configure.txt
```

<<<@/assets/environment/source/php/configure/84.ini [8.4 选项]
<<<@/assets/environment/source/php/configure/74.ini [7.4 选项]
:::

### 4. 构建指令参考

::: details 构建指令参考
::: code-group
<<<@/assets/environment/source/php/build/84.bash [84]
<<<@/assets/environment/source/php/build/74.bash [74]

```bash [编译&安装]
# nohup make -j4 &
make -j4
make test
make install
```

<<<@/assets/environment/source/php/build/84_pgsql.bash [84[pgsql]]
<<<@/assets/environment/source/php/build/84_mysql.bash [84[mysql]]
:::

::: tip 构建指令区别：
php8.1 默认已经对 openssl 启用 `pcre-jit` 实现正则即时编译

php8.1 的 gd 扩展增加的 `--with-avif` 选项

php8.4 增加 `--with-capstone` 选项
:::

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
# php8.4
/server/php/84/bin/php --ini
```

```bash [使用 php-config 程序]
# php7.4
/server/php/74/bin/php-config --ini-path
# php8.4
/server/php/84/bin/php-config --ini-path
```

:::

### 3. 拷贝配置文件 {#copy-config-file}

::: code-group

```bash [84]
cp /home/php-fpm/php-8.4.3/php.ini-* /server/php/84/lib/
# 开发环境
cp /server/php/84/lib/php.ini{-development,}
# 部署环境
# cp /server/php/84/lib/php.ini{-production,}
```

```bash [74]
cp /home/php-fpm/php-7.4.33/php.ini-* /server/php/84/lib/
# 开发环境
cp /server/php/74/lib/php.ini{-development,}
# 部署环境
# cp /server/php/74/lib/php.ini{-production,}
```

:::

### 4. 检测配置文件

使用 php 程序，快速检测配置文件使用加载成功

```bash
# php7.4
/server/php/74/bin/php --ini
# php8.4
/server/php/84/bin/php --ini
```

### 5. 开启 OPcache

PHP 官方明确说明 OPcache 只允许编译为共享扩展，并默认构建

使用 `--disable-opcache` 选项可以禁止构建

::: code-group

```ini [开启方式]
# 在 `php.ini` 第 953 行，将 `;` 去掉
zend_extension=opcache
```

```ini [性能配置]
# 在 `php.ini` 第 1796 行，加入以下内容，可获得较好性能
# 检查脚本时间戳是否有更新的周期，以秒为单位
opcache.revalidate_freq=60
```

:::

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
<<<@/assets/environment/source/php/84/php-fpm.conf{ini} [8.4]
<<<@/assets/environment/source/php/74/php-fpm.conf{ini} [7.4]
:::

### 3. 工作池配置文件

PHP-FPM 工作池进程配置文件有多个，并且支持随意命名，但为了更好的管理，我们最好遵循一些规则：

1. 针对单独站点 : 跟 nginx 站点配置文件命名一致
2. 根据工作池性质 :
    - 接收 tp6 站点，命名 `tp.conf`；
    - 接收其它站点，命名 `default.conf`

::: details 通用工作池案例
::: code-group
<<<@/assets/environment/source/php/84/php-fpm.d/default.conf{ini} [8.4]
<<<@/assets/environment/source/php/74/php-fpm.d/default.conf{ini} [7.4]
:::

::: details ThinkPHP 项目专用工作池案例
<<<@/assets/environment/source/php/84/php-fpm.d/tp.conf{ini}
:::

::: tip
更多参数说明，请阅读 [PHP 手册](https://www.php.net/manual/zh/install.fpm.configuration.php)
:::

## Systemd 管理

PHP-FPM 自带了一套比较完善的进程管理指令，编译完成后还会在构建目录下生成 Systemd Unit 文件

::: details 默认模板

::: code-group
<<<@/assets/environment/source/service/php/source/84/php-fpm.service{ini} [php8.4]
<<<@/assets/environment/source/service/php/source/74/php-fpm.service{ini} [php7.4]
:::

::: details 案例参考

::: code-group
<<<@/assets/environment/source/service/php/php84-fpm.service{bash} [php8.4]
<<<@/assets/environment/source/service/php/php74-fpm.service{ini} [php7.4]
:::

```bash
# 创建单元文件
mv /path/php*-fpm.service /usr/lib/systemd/system/
# 重载Systemd
systemctl daemon-reload
# 加入systemctl服务，并立即开启
systemctl enable --now php84-fpm
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

推荐直接使用阿里云镜像下载 [composer](https://mirrors.aliyun.com/composer/composer.phar)

```bash
su - php-fpm -s /bin/zsh
cd /server/php/tools
./php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
./php composer-setup.php
./php -r "unlink('composer-setup.php');"
chmod 750 composer.phar

# 软链接到可用的环境变量路径，如: /usr/local/bin/ 路径下
ln -s /server/php/tools/composer.phar /usr/local/bin/composer
```

### 2. 全量镜像

Composer 国内全量镜像推荐 `华为云>腾讯云>阿里云`

```bash
# 切换到开发用户或php-fpm用户
su - php-fpm -s /bin/zsh
# 使用国内 Composer 全量镜像
composer config -g repo.packagist composer https://mirrors.huaweicloud.com/repository/php/
# 取消使用国内 Composer 全量镜像
composer config -g --unset repos.packagist
```

::: tip 国内镜像推荐

```bash
# 华为云
composer config -g repo.packagist composer https://mirrors.huaweicloud.com/repository/php/
# 腾讯云
composer config -g repos.packagist composer https://mirrors.cloud.tencent.com/composer/
# 阿里云 [不能实时同步，部分扩展缺失]
composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/
```

:::

### 3. 升级

升级 composer 也非常简单，建议使用国内全量镜像后再升级

```bash
# 切换到php-fpm用户，只能从root进入
su - php-fpm -s /bin/zsh
/server/php/84/bin/php /usr/local/bin/composer self-update
```

## 升级 PHP

升级 PHP 跟正常编译几乎一样，下面是注意事项：

1. 安装依赖

    - PHP 跨主版本更新，必须重新编译安装动态扩展；
    - PHP 跨次版本更新，建议重新编译安装动态扩展；
    - PHP 小版本更新，如果 PHP 并未修改动态扩展，就不用重新编译安装动态扩展。

2. 重命名执行程序
   执行 `make install` 之前，先将 `sbin/php-fpm` 文件重命名，实现平滑升级

    ```bash
    # php8.4
    mv /server/php/84/sbin/php-fpm{,.bak}
    ```

3. 配置文件 `php.ini`

    - 小版本升级，需要修改配置文件，除非遇到 PHP 非常特殊的情况，
    - 其他版本升级，就直接替换掉配置文件，然后将需要的动态扩展重新加上去即可

::: danger 警告
服务器升级 PHP 乃至任何软件升级前，都应该先存快照，备份一份
:::

## 动态安装 PECL 扩展

<!-- @include: ./trait/php_ext.md -->

## 权限

::: code-group

```bash [部署]
chown php-fpm:php-fpm -R /server/php /server/logs/php
find /server/php /server/logs/php /server/php/tools/ -type f -exec chmod 640 {} \;
find /server/php /server/logs/php /server/php/tools/ -type d -exec chmod 750 {} \;
# 可执行文件需要执行权限
chmod 750 -R /server/php/84/bin /server/php/84/sbin
# 动态扩展库文件: 运行时需要读取权限，升级时需要写入权限，单独调用时需要执行权限
chmod 750 /server/php/84/lib/php/extensions/no-debug-non-zts-*/*
# composer 单独调用是也需要执行权限
# php composer -V php开头的形式调用就不需要执行权限
# composer -V composer开的的形式，就必须添加执行权限
chmod 750 /server/php/tools/composer.phar
```

```bash [开发]
# 权限同部署环境
# 开发用户 emad 加入 lnpp包用户组
usermod -G sqlite,redis,postgres,mysql,php,nginx emad
```

:::
