---
title: 安装 PHP 解释器
titleTemplate: 环境搭建教程
---

# 安装 PHP 解释器

PHP（`PHP: Hypertext Preprocessor`，超文本预处理器的字母缩写）是一种被广泛应用的开放源代码的多用途脚本语言，它可嵌入到 HTML 中，尤其适合 web 开发。

## 变更说明

::: tip 变更说明：

-   `2026.02.10` : 文档重构，移除旧版内容，需要旧版的请阅读源码 `v1` 分支；
-   `2025/12/13` : 移除 `php 8.4` 支持，由 `php 8.5` 取代；
-   `2025/01/22` : 移除 `php 8.3` 支持，由 `php 8.4` 取代；
-   `2025/09/09` : 移除 `php 7.4` 支持，因 `debian13` 编译安装该版本需要解决非常多的兼容性问题；

:::

## 依赖项说明

::: details SQLite3 开发库依赖

::: code-group

```bash {2} [使用最新开发库]
# 需要将 sqlite3 的 pkgconfig 路径手动加入到临时环境变量里
export PKG_CONFIG_PATH=/server/sqlite3/lib/pkgconfig:$PKG_CONFIG_PATH

# 使用下面指令检查 sqlite3 是否正确
pkg-config --path sqlite3

# 成功展示：
/server/sqlite3/lib/pkgconfig/sqlite3.pc
```

```bash [使用系统依赖]
# 此方式系统可以自动获取 pkgconfig 路径，无需额外操作
apt install --no-install-recommends libsqlite3-dev -y
```

::: warning :warning: 要想使用最新版的 SQLite3 库编译

1. 需要自己先安装 `最新的 SQLite3 开发库`，环境中编译安装最新的[[SQLite3 库和工具]](./sqlite3)，已经自带最新开发库。
2. 确保 php 用户对 SQLite3 开发库有 `读取+执行` 权限。

:::

::: details PostgreSQL 客户端编程接口依赖

::: code-group

```bash [使用最新开发库]
# PHP 的构建选项需指定 Postgres 安装根目录 或者 pkgconfig 路径
../configure --prefix=/server/php/85/ \
# [!code error:2]
# [!code focus:2]
--with-pgsql=/server/postgres \
--with-pdo-pgsql=/server/postgres \
# --with-pgsql=/server/postgres/lib/pkgconfig \
# --with-pdo-pgsql=/server/postgres/lib/pkgconfig \
...
```

```bash [使用系统依赖]
# 该方式不需要指定postgres安装目录
apt install --no-install-recommends libpq-dev -y
```

::: warning :warning: 要想使用最新的 PostgreSQL 客户端编程接口

1. 需要先安装 `最新的 PostgreSQL 客户端开发库`，项目中编译安装最新的[[PostgreSQL 数据库管理系统]](./pgsql)，已经自带最新的客户端开发库。
2. 确保 php 用户对 PostgreSQL 客户端开发库有 `读取+执行` 权限。

:::

## 编译流程

这里以 PHP 8.5 为例

::: code-group

```bash [进入编译目录]
su - php-fpm -s /bin/zsh

tar -xJf php-8.5.2.tar.xz
mkdir /home/php/php-8.5.2/build_php
cd /home/php/php-8.5.2/build_php/
```

```bash [设置环境变量]
# 同时存在 clang 和 gcc 时，推荐指定 gcc 和 g++ 作为编译器
export CC=/usr/bin/gcc
export CXX=/usr/bin/g++
```

<<< @/assets/linux/script/php/build-85.bash [构建选项]

```bash [编译&安装]
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
./configure -h > php-85.help
```

<<< @/assets/linux/help/php-85.help{ini} [8.5 帮助]
:::

::: details 构建选项说明

| 选项                                | 作用                                        |
| ----------------------------------- | ------------------------------------------- |
| `--prefix=/server/php/85/`          | 设置安装目录                                |
| `--enable-fpm`                      | 启用 PHP-FPM 服务                           |
| `--with-fpm-user=php`               | 设置运行用户（降权运行）                    |
| `--with-fpm-group=php`              | 设置运行组（降权运行）                      |
| `--with-fpm-systemd`                | 支持 Systemd 管理                           |
| `--with-openssl`                    | 支持 HTTPS/SSL                              |
| `--with-openssl-legacy-provider`    | 为 OpenSSL 3.0+ 启用旧版算法提供者          |
| `--with-openssl-argon2`             | 启用 OpenSSL 内置的 Argon2 密码哈希算法支持 |
| `--with-zlib`                       | 启用 zlib 压缩                              |
| `--with-curl`                       | 启用 cURL 支持                              |
| `--enable-exif`                     | 启用图像元数据                              |
| `--enable-gd`                       | 启用 GD 图像库                              |
| `--with-jpeg`                       | GD 图像库启用 JPEG 支持                     |
| `--with-freetype`                   | GD 图像库启用字体渲染                       |
| `--enable-intl`                     | 启用国际化支持                              |
| `--enable-mbstring`                 | 启用多字节字符串                            |
| `--with-mysqli=mysqlnd`             | MySQLi 扩展                                 |
| `--with-pdo-mysql=mysqlnd`          | PDO MySQL 驱动                              |
| `--with-pdo-pgsql=/server/postgres` | PDO PostgreSQL 驱动                         |
| `--with-pgsql=/server/postgres`     | PostgreSQL 扩展                             |
| `--with-sodium`                     | 现代加密算法库                              |
| `--enable-sockets`                  | 启用 Socket 通信                            |
| `--with-password-argon2`            | 密码散列算法启用 Argon2 密码哈希            |
| `--enable-mysqlnd`                  | PHP 针对 MySQL 的原生驱动                   |

:::

::: tip 构建选项区别：

1. `>=8.1.0` 默认已经对 OpenSSL 启用 `pcre-jit` 实现正则即时编译
2. `>=8.1.0` 的 gd2 扩展增加的 `--with-avif` 选项，现实场景用不到
3. `>=8.1.0` 对 `--with-mhash` 选项标记为已弃用，如果没有旧项目需要向后兼容，不要添加此选项
4. `>=8.1.0` 增加 `--with-capstone` 选项(生产环境不建议启用)，主要用于调试分析，对性能没有影响但增加依赖

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
cp /home/php/php-8.5.2/php.ini-* /server/php/85/lib/
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

### 5. 配置 OPcache

PHP 官方明确说明 OPcache 只允许编译为共享扩展，并默认构建，使用 `--disable-opcache` 选项可以禁止构建。

::: tip PHP 官网强烈推荐：
所有现代 PHP 生产环境都必须启用 `OPcache` (默认开启)!

题外话：对于 Windows 环境，`OPcache` 性能优化有限，保持默认配置即可。
:::

:::code-group
<<< @/assets/linux/etc/php/opcache-85-dev.ini [开发环境]
<<< @/assets/linux/etc/php/opcache-85.ini [生产环境]
:::

::: danger :warning: 生产环境配置案例警告

1. `opcache.validate_timestamps=0`

    - 关闭文件时间戳检查，即：操作码缓存文件从不更新

2. `opcache.enable_file_override=1`

    - 启用检查操作码缓存，即：优先读取作码缓存文件，不管文件是否更新或删除

由于生产环境设置了这两个选项，所以每次修改文件后，必须使用下面任意方式清空 OPcache 缓存：

::: code-group

```bash [重启PHP-FPM服务]
# 1. 清空所有OPcache
# 2. 清空所有APCu
# 3. 终止所有用户会话
# 4. 中断正在处理的请求
systemctl restart php85-fpm
```

```bash [Web环境执行清空]
# 只清空OPcache共享内存
# OPcache内存空间与网站完全一致
curl https://site.com/opcache-reset.php
```

```bash [CLI执行清空]
# 只清空OPcache共享内存
# OPcache内存空间与网站通常不一致
# 不建议使用此方式
php -r "opcache_reset();"
```

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
<<< @/assets/linux/etc/php/85/php-fpm.conf{ini} [8.5]
:::

### 3. 工作池配置文件

PHP-FPM 工作池进程配置文件有多个，并且支持随意命名，但为了更好的管理，我们最好遵循一些规则：

1. 针对单独站点 : 跟 nginx 站点配置文件命名一致
2. 根据工作池性质 :
    - 接收通用站点，命名 `default.conf`
    - 单独接收 ThinkPHP 站点，命名 `tp.conf`；

::: code-group
<<< @/assets/linux/etc/php/85/php-fpm.d/default.conf{ini} [85-default]
<<< @/assets/linux/etc/php/85/php-fpm.d/tp.conf{ini} [tp]
<<< @/assets/linux/etc/php/85/php-fpm.d/example/default.conf{ini} [案例说明]
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
<<< @/assets/linux/service/php85-fpm.service{bash} [85]

```bash [重载]
systemctl daemon-reload
systemctl enable --now php85-fpm
```

<<< @/assets/linux/service/default/php85-fpm.service{ini} [85 默认]
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
cd /server/etc/php/tools
./php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
./php composer-setup.php
./php -r "unlink('composer-setup.php');"
chmod 750 composer.phar

# 软链接到可用的环境变量路径，如: /usr/local/bin/ 路径下
ln -s /server/etc/php/tools/composer.phar /usr/local/bin/composer
```

### 2. 全量镜像

Composer 国内全量镜像推荐 `华为云>腾讯云>阿里云`

```bash
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

```bash [部署]
chown php-fpm:php-fpm -R /server/php /server/logs/php
find /server/php /server/logs/php -type f -exec chmod 640 {} \;
find /server/php /server/logs/php -type d -exec chmod 750 {} \;
chmod 640 /server/php/85/lib/php/extensions/no-debug-non-zts-*/*
chmod 750 -R /server/php/85/{bin,sbin}
chmod 750 /server/etc/php/tools/{composer,php-cs-fixer-v3}.phar

ln -s /server/etc/php/tools/composer.phar /usr/local/bin/composer
ln -s /server/etc/php/tools/php-cs-fixer-v3.phar /usr/local/bin/php-cs-fixer
```

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
