---
title: 安装 PHP
titleTemplate: 环境搭建教程
---

# 安装 PHP

PHP（`PHP: Hypertext Preprocessor`，超文本预处理器的字母缩写）是一种被广泛应用的开放源代码的多用途脚本语言，它可嵌入到 HTML 中，尤其适合 web 开发。

## 准备工作

开始之前我们需要先使用预先准备好的 bash 脚本，解压文件和授权目录，具体参考 [脚本文件](./index#脚本文件)

编译安装 PHP 的方式，允许同时构建多个 php 版本，如: `php 8.5` + `php 8.5`

::: tip 变更说明

-   `2025/09/09` : 正式移除 `php 7.4` 支持，因 `debian13` 编译安装该版本需要解决非常多的兼容性问题，其相关内容请阅读[[旧版]](../environment-old/php)；
-   `2025/01/22` : 正式移除 `php 8.3` 支持，由 `php 8.4` 取代，其相关内容请阅读[[旧版]](../environment-old/archive/php_old)；
-   `2025/12/13` : 正式移除 `php 8.4` 支持，由 `php 8.5` 取代；

:::

## 构建 PHP

### 1. 解压源码包

::: code-group

```bash [85]
su - php-fpm -s /bin/zsh
tar -xJf php-8.5.0.tar.xz
mkdir /home/php-fpm/php-8.5.0/build_php
cd /home/php-fpm/php-8.5.0/build_php/
```

:::

### 2. 安装依赖项

本次 PHP 编译过程中，在系统原有扩展存在下，还需安装如下依赖项：

#### apt 自带依赖库安装

::: code-group

```bash [common]
# 编译基础
apt install -y gcc g++

# 安装PECL扩展需要
apt install -y autoconf
```

```bash [85]
apt install -y libcurl4-openssl-dev libpng-dev libavif-dev libwebp-dev \
libjpeg-dev libxpm-dev libfreetype-dev libonig-dev libzip-dev
```

:::

#### ~~SQLite3 依赖~~ <Badge type="info" text="已禁用" />

想使用最新或指定版 sqlite3 ，需自己编译好 sqlite3 后，在 `PKG_CONFIG_PATH` 环境变量中追加 sqlite3 的 `pkgconfig` 配置文件路径

::: code-group

```bash {4} [编译安装sqlite3]
usermod -a -G sqlite php-fpm

# 构建 PHP 需将 sqlite3 的 pkgconfig 目录加入到临时环境变量里
export PKG_CONFIG_PATH=/server/sqlite/lib/pkgconfig:$PKG_CONFIG_PATH

# 使用下面指令检查 sqlite3 是否正确
pkg-config --path sqlite3

# 成功展示：
/server/sqlite/lib/pkgconfig/sqlite3.pc
```

```bash [使用依赖库]
# 未安装 sqlite3，则需安装 libsqlite3-dev 依赖库
# 这中方式不用将 pkgconfig 加入到 PKG_CONFIG_PATH 环境变量中
apt install libsqlite3-dev -y
```

:::

#### pgsql 依赖

想使用最新或指定版 pgsql，需自己编译好 libpq 库后，在 php 构建选项里指定目录路径

::: code-group

```bash [编译安装Postgres]
usermod -a -G postgres php-fpm

# PHP 的构建选项需指定Postgres安装目录或其pkgconfig路径
../configure --prefix=/server/php/85/ \
# [!code error:2]
# [!code focus:2]
--with-pgsql=/server/postgres \
--with-pdo-pgsql=/server/postgres \
# --with-pdo-pgsql=/server/postgres/lib/pkgconfig \
...
```

```bash [使用依赖库]
# 未安装 PostgreSQL 则需安装 libpq-dev 依赖库
# 该方式不需要指定postgres安装目录
apt install libpq-dev -y
```

:::

::: tip 提示

1. 本次已编译 SQLite3，无需额外使用依赖库
    - 确保 php 用户对 SQLite3 的 pkgconfig 目录有 `读取` 和 `执行` 权限
2. 本次已编译 Postgres，无需额外使用依赖库
    - 确保 php 用户对 Postgres 安装目录要有 `读取` 和 `执行` 权限
3. 不同版本所需依赖项可能不同
4. 使用更多外部扩展，所需依赖项也会更多
5. php 较低版本如果要在新版的 linux 系统上安装，很多依赖可能都需要自己重新

通常你需要自己去阅读 `configure` 的错误提示，以及掌握 linux 软件包的编译安装。
:::

### 3. 构建选项

::: code-group
<<< @/assets/environment/source/php/build/85.bash [85]

```bash [编译&安装]
# nohup make -j4 &
make -j4 > make.log
make test > makeTest.log
make install
```

```bash [查看帮助]
# 全部构建选项
./configure -h

# 扩展构建选项
./configure -h | grep pgsql
./configure -h | grep sqlite

# 导出构建选项
./configure -h > configure.txt
```

<<< @/assets/environment/source/php/configure/85.ini [8.5 选项]
:::

::: tip 构建指令区别：

1. `>=8.1.0` 默认已经对 OpenSSL 启用 `pcre-jit` 实现正则即时编译

2. `>=8.1.0` 的 gd2 扩展增加的 `--with-avif` 选项

3. `>=8.1.0` 对 `--with-mhash` 选项标记为已弃用，如果没有旧项目需要向后兼容，不要添加此选项

4. `>=8.5.0` 增加 `--with-capstone` 选项(生产环境不建议启用)，主要用于调试分析，对性能没有影响但增加依赖

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
# php8.5
/server/php/85/bin/php --ini
```

```bash [使用 php-config 程序]
# php8.5
/server/php/85/bin/php-config --ini-path
```

:::

### 3. 拷贝配置文件 {#copy-config-file}

::: code-group

```bash [85]
cp /home/php-fpm/php-8.5.0/php.ini-* /server/php/85/lib/
# 开发环境
cp /server/php/85/lib/php.ini{-development,}
# 部署环境
# cp /server/php/85/lib/php.ini{-production,}
```

:::

### 4. 检测配置文件

使用 php 程序，快速检测配置文件使用加载成功

```bash
# php8.5
/server/php/85/bin/php --ini
```

### 5. 开启 OPcache

PHP 官方明确说明 OPcache 只允许编译为共享扩展，并默认构建

使用 `--disable-opcache` 选项可以禁止构建

::: code-group

```ini [开启方式]
; 新版PHP默认启用，不需要额外开启
[opcache]
;opcache.enable=1
; cli 不用开启，因为都是临时使用，用不到 opcache
;opcache.enable_cli=0
...
```

```ini [性能配置]
; 通常生产和测试环境开启，开发环境不开启
[opcache]
...
; 默认jit是未配置的，启用jit来提升性能
opcache.jit=tracing
opcache.jit_buffer_size=100M
...
; 设 256M（适用于 90% 的 Web 应用）
opcache.memory_consumption=256
...
; 关闭文件时间戳检查以提升性能（部署新代码后必须触发缓存更新！）
opcache.validate_timestamps=0
...
```

:::

::: danger 警告
官网强烈推荐，所有现代 PHP 生产环境都必须启用 `OPcache`，
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
<<< @/assets/environment/source/php/85/php-fpm.conf{ini} [8.5]
:::

### 3. 工作池配置文件

PHP-FPM 工作池进程配置文件有多个，并且支持随意命名，但为了更好的管理，我们最好遵循一些规则：

1. 针对单独站点 : 跟 nginx 站点配置文件命名一致
2. 根据工作池性质 :
    - 接收通用站点，命名 `default.conf`
    - 单独接收 ThinkPHP 站点，命名 `tp.conf`；

::: code-group
<<< @/assets/environment/source/php/85/php-fpm.d/default.conf{ini} [85-default]
<<< @/assets/environment/source/php/85/php-fpm.d/tp.conf{ini} [tp]
<<< @/assets/environment/source/php/85/php-fpm.d/example/default.conf{ini} [案例说明]
:::

::: tip 更多参数说明，请阅读 [PHP 手册](https://www.php.net/manual/zh/install.fpm.configuration.php)
:::

::: warning :warning: 下面是 FastCGI 工作进程池的资源竞争导致请求死锁的案例以及具体解决方案：

::: code-group

```md [发生异常场景]
1. `接口提供者-站点A` 跟 `当前项目-站点B` 在同台服务器；
2. IIS 手动增加的 FastCGi 最大实例数默认为 4，维护者未做修改；
3. 2 个站点共用同一个 `程序应用池`；
4. 由于 2 个站点需要的 PHP 版本号一样，共用同一个 FastCGI 程序（php-cgi.exe 文件）；
5. `站点A` 提供接口给 `站点B`；
6. `站点B-前端` 有个页面向 `站点B-PHP后端` 同时发起了 `5个请求`；
7. `这5个请求` 又是全部需要向 `站点A` 发起 CURL 请求来获得数据的；
8. 到此当前 `程序应用池` 对 FasCGI 模块最大可生成的 4 个工作进程已被 `站点B前端->站点B后端` 的 5 个并发请求全部占用（有 1 个请求在排队中）；
9. 所以 `站点B->站点A` 的 CURL 请求也全部在排队中，发生了堵塞事件；
10. 这个情况，只有到请求超时返回失败 FastCGI 子进程才能被释放；
11. 由于 `这5个请求` 同时发出，失败几乎也是同时发生，所以这个页面的 5 请求会陷入请求失败的死循环。
```

```md [IIS 解决办法]
1. `PHP当前项目站点` 不要跟 `PHP接口站点` 处于同台服务器；
    - 完美解决
2. 为每个站点设置不同的 `应用程序池`，不同应用程序池之间工作进程是独立；
    - 在服务器资源有限的情况，这是完美解决方案
3. 在性能允许的情况下增大 `FastCGI设置 > 最大实例数` 的值；
    - 临时应急，不能从根本上解决问题
4. 每个站点单独使用一个 `php-cgi.exe` 程序(复制 `php-cgi.exe` 重命名)。
    - 可行但不建议，仅在 web 服务器不支持 `方式2` 的情况下使用
```

```md [PHP-FPM 解决办法]
1. `PHP当前项目站点` 不要跟 `PHP接口站点` 处于同台服务器；
    - 完美解决
2. 为每个站点设置单独的 `工作池`，不同工作池产生 `worker进程` 是独立的；
    - 在服务器资源有限的情况，这是完美解决方案
    - 在 `<phpRootPath>/etc/php-fpm.d` 目录下增加工作池配置文件
3. 在性能允许的情况下增大 `max_children` 的值；
    - 临时应急，不能从根本上解决问题
```

:::

## Systemd 管理

PHP-FPM 自带了一套比较完善的进程管理指令，编译完成后还会在构建目录下生成 Systemd Unit 文件

::: code-group
<<< @/assets/environment/source/service/php/php85-fpm.service{bash} [85]

```bash [重载]
# 重载Systemd
systemctl daemon-reload
# 加入systemctl服务，并立即开启
systemctl enable --now php85-fpm
```

<<< @/assets/environment/source/service/php/source/85/php-fpm.service{ini} [85 默认]

:::

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
/server/php/85/bin/php /usr/local/bin/composer self-update
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
    # php8.5
    mv /server/php/85/sbin/php-fpm{,.bak}
    ```

3. 配置文件 `php.ini`

    - 小版本升级，需要修改配置文件，除非遇到 PHP 非常特殊的情况，
    - 其他版本升级，就直接替换掉配置文件，然后将需要的动态扩展重新加上去即可

::: danger 警告
服务器升级 PHP 乃至任何软件升级前，都应该先存快照，备份一份
:::

## 动态安装 PECL 扩展

<!--@include: ./trait/php_ext.md-->

## 权限

::: code-group

```bash [部署]
chown php-fpm:php-fpm -R /server/php /server/logs/php
find /server/php /server/logs/php -type f -exec chmod 640 {} \;
find /server/php /server/logs/php -type d -exec chmod 750 {} \;
chmod 640 /server/php/85/lib/php/extensions/no-debug-non-zts-*/*
# 可执行文件需要执行权限
chmod 750 -R /server/php/85/{bin,sbin}
# composer,phpCsFixer等工具包，在独立调用时也需要执行权限
chmod 750 /server/php/tools/{composer,php-cs-fixer-v3}.phar

# 创建快捷方式
ln -s /server/php/tools/composer.phar /usr/local/bin/composer
ln -s /server/php/tools/php-cs-fixer-v3.phar /usr/local/bin/php-cs-fixer
```

```bash [开发]
# 权限同部署环境
# 开发用户 emad 加入 lnpp包用户组
usermod -a -G redis,postgres,php-fpm,nginx emad
```

:::

::: details 什么是独立调用

像 `{composer,php-cs-fixer}.phar` 等 phar 工具包本质上都是 php 脚本文件，
只要终端支持 php 脚本，`php 脚本文件` 就可以象 `sh 脚本文件` 一样独立调用

::: code-group

```bash {1} [代码开头]
#!/usr/bin/env php
<?php
/*
 * This file is part of Composer.
 *
 * (c) Nils Adermann <naderman@naderman.de>
 *     Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view
 * the license that is located at the bottom of this file.
 */
 ...
```

```bash [使用php调用]
php composer [options]
php php-cs-fixer [options]
/server/php/85/bin/php /usr/local/bin/composer [options]
/server/php/85/bin/php /usr/local/bin/php-cs-fixer [options]
```

```bash [独立调用]
composer [options]
php-cs-fixer [options]
/usr/local/bin/composer [options]
/usr/local/bin/php-cs-fixer [options]
```

::: danger 独立调用的条件
php 可执行程序必须加入到对应终端的环境变量中，终端才能通过代码开头的 `#!/usr/bin/env php` 识别到。
:::
