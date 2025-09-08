---
title: 概述
titleTemplate: 环境搭建教程
---

# 环境搭建概述

下面开始手工搭建 PHP 的开发环境、部署环境

## 测试环境

::: code-group

```[debian13]
系统 : `Debian GNU/Linux 13 (trixie)`
内核 : `linux-image-6.12.41+deb13-amd64`
```

<<<@/assets/environment/lnmpp-toc.txt [环境目录结构]
:::

## 脚本文件

我们准备了几个 bash 脚本文件：

::: code-group
<<<@/assets/environment/source/bash/user.bash [创建用户]
<<<@/assets/environment/source/bash/mkdir.bash [创建目录]
<<<@/assets/environment/source/bash/chown.bash [目录授权]
<<<@/assets/environment/source/bash/tar.bash [解压参考]
:::

## 包列表

::: code-group

```md [包列表]
1. sqlite-autoconf-3500400.tar.gz
2. redis-8.2.1.tar.gz
3. mysql-8.4.6.tar.gz
4. postgresql-17.6.tar.bz2
5. php-8.4.12.tar.xz
    - 动态扩展
        - xdebug-3.4.5.tgz
        - apcu-5.1.27.tgz
        - mongodb-2.1.1.tgz
        - redis-6.2.0.tgz
        - yaml-2.2.5.tgz
6. nginx-1.28.0.tar.gz
    - openssl-3.5.2.tar.gz
    - pcre2-10.45.tar.bz2
    - zlib-1.3.1.tar.xz
```

```md [包下载]
| package        | url                                                  |
| -------------- | ---------------------------------------------------- |
| SQLite3        | https://www.sqlite.org/                              |
| Redis          | https://download.redis.io/redis-stable.tar.gz        |
| PostgreSQL     | https://www.postgresql.org/                          |
| MySQL          | https://www.mysql.com/                               |
| PHP            | https://www.php.net/                                 |
| PHP extend     | http://pecl.php.net/                                 |
| Nginx          | http://nginx.org/                                    |
| zlib           | http://www.zlib.net/                                 |
| openssl        | https://openssl-library.org/                         |
| openssl-1.1.1w | https://www.openssl.org/source/openssl-1.1.1w.tar.gz |
| pcre2          | https://github.com/PCRE2Project/pcre2                |
| icu4c          | https://github.com/unicode-org/icu/releases/         |
```

:::

<!-- 引入请求生命周期 -->
<!-- @include: ./trait/lifecycle.md -->

<!-- 引入用户说明 -->
<!-- @include: ./trait/userinfo.md -->

## 用户及用户组

下面这是开发环境的案例

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
chmod php-fpm:nginx -R /www/tp /www/tp/public /www/tp/public/static /www/tp/public/static/upload
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
chmod php-fpm:nginx -R /www/laravel /www/laravel/public /www/laravel/public/static /www/laravel/public/static/upload
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

## umask 权限

::: code-group

```bash [开发用户]
# ~/.profile

# 第9行 umask 022处新建一行
umask 027 # 创建的文件权限是 640 目录权限是 750
```

```bash [php-fpm 用户]
# /etc/profile

# 第9行 umask 022 处新建一行
# 即使客户端上传了木马上来，也没得执行
umask 022 # 创建的文件权限是 644 目录权限是 755

# 提示：
# - php-fpm 用户的进程通常不会进入终端，所以只能在系统级别的初始化文件里设置
# - 但是这样一来其它用户的权限也会跟着改变，需要慎重处理
# - 建议使用php自身来限制上传文件的权限
```

:::

::: warning 注意

bash/zsh 配置文件开头需要增加一行：

```bash
# ~/.(bashrc|zshrc)

source ~/.profile
```

:::

## 编译器选择

::: warning PostgreSQL 推荐使用 `CLANG+LLVM` 编译套件，`--with-llvm` 启用 JIT 支持，能提升查询性能；其余软件优先使用 `GCC` 编译套件。
:::

## 内核调优

服务器中如果存在多个主要的服务，内核调优就需要兼顾到各种服务的特性，需要理解的是：
内核的配置仅代表这台服务器最大允许的值，软件包通常也会提供对应值。

这里通过修改 sysctl 配置文件（`/etc/sysctl.d/*.conf`）来实现：

1. 控制进程是否允许使用虚拟内存

    - 0：进程只能使用物理内存(默认值)
    - 1：进程可以使用比物理内存更多的虚拟内存

    ```bash
    echo "vm.overcommit_memory = 1" > /etc/sysctl.d/overcommit_memory.conf
    ```

2. TCP 全连接队列(Accept 队列)最大长度，即已完成三次握手但未被应用层 accept()的连接数

    - debian13 默认为 4096，通常足够
    - 超过 65535 需确认内核是否支持

    ```bash
    echo "net.core.somaxconn = 4096" > /etc/sysctl.d/somaxconn.conf
    ```

3. TCP 半连接队列(SYN 队列)最大长度，即处于 SYN_RECV 状态的未完成握手连接数

    - debian13 默认为 512，存在高并发服务建议设为 4096
    - 增大值会占用更多内存

    ```bash
    echo "net.ipv4.tcp_max_syn_backlog = 4096" > /etc/sysctl.d/tcp_max_syn_backlog.conf
    ```

::: warning :warning: 注意：
在 Debian 13 中，sysctl 的配置文件路径发生了显著变化，从传统的单一文件 `/etc/sysctl.conf` 转向了模块化的分散配置目录。

以下是 Debian13 具体的配置文件路径及其优先级规则：

| 路径                             | 优先级 | 说明           |
| -------------------------------- | ------ | -------------- |
| `/etc/sysctl.d/*.conf`           | 1      | 优先级最高     |
| `/run/sysctl.d/*.conf`           | 2      | 重启失效       |
| `/usr/local/lib/sysctl.d/*.conf` | 3      | 第三方软件配置 |
| `/usr/lib/sysctl.d/*.conf`       | 4      | 系统默认配置   |
| `/lib/sysctl.d/*.conf`           | 5      | 兼容旧版       |

```bash
sysctl --system             # 加载所有配置文件
sysctl -a                   # 检查所有生效参数
sysctl vm.overcommit_memory # 查看特定参数
```

:::

## 资源限制

`/etc/security/limits.conf` 用于设置用户或用户组在系统上的资源限制，目的是防止单个用户或进程过度消耗系统资源（如 CPU、内存、文件打开数等），从而保障系统的稳定性和安全性

| 配置文件                        | 说明               |
| ------------------------------- | ------------------ |
| `/etc/security/limits.conf`     | 资源限制主配置文件 |
| `/etc/security/limits.d/*.conf` | 模块化管理配置文件 |

::: details 案例
::: code-group

```bash [postgres]
echo "postgres  soft  nofile  65535
postgres  hard  nofile  65535" > /etc/security/limits.d/postgres.conf
```

```bash [redis]
echo "redis soft nofile 65535
redis hard nofile 65535" > /etc/security/limits.d/redis.conf
```

:::

## 日志管理

Linux/Unix 系统使用 `Logrotate` 来管理日志文件。更多说明请参考 [[Tutorial]](http://tutorial.e8so.com/) 项目

::: code-group
<<<@/assets/environment/source/logrotate.d/redis{bash} [redis]
<<<@/assets/environment/source/logrotate.d/nginx{bash} [nginx]
<<<@/assets/environment/source/logrotate.d/example{bash} [带备注]
:::

{#Logrotate-common}

| 常用指令                                     | 描述                                              |
| -------------------------------------------- | ------------------------------------------------- |
| `logrotate -vf /etc/logrotate.conf`          | 强制立即轮转所有配置文件（-v 显示详细过程）       |
| `logrotate -vf /etc/logrotate.d/your_config` | 强制立即轮转指定的配置文件（-v 显示详细过程）     |
| `logrotate -d /etc/logrotate.d/your_config`  | 模拟轮转过程并显示详细信息                        |
| `grep logrotate /var/log/syslog`             | 查看 logrotate 自身的执行记录和可能出现的错误信息 |

::: warning :warning: `copytruncate/create` 两者区别

-   copytruncate :
    1. 复制当前日志文件后清空原文件，避免需重启 Redis 或发送信号
    2. 对于不支持主动重新打开日志的程序非常有用
    3. 但理论上在复制和清空之间可能有极小量的日志丢失
-   create :
    1. 确保日志不丢失，
    2. 需要配置 `postrotate` 脚本，在轮转后对程序发送信号，重新打开日志

:::

## 编译 OpenSSL 特定版本 {#assign-openssl-version}

如果对 openssl 依赖库有特殊版本需求，需要自行编译安装

::: code-group

```bash{5-11} [1.1.1w编译选项]
# 作为公共依赖库，推荐以root用户安装它
mkdir /server/openssl-1.1.1w
cd /root/openssl-1.1.1w/

./config --prefix=/server/openssl-1.1.1w \
--openssldir=/server/openssl-1.1.1w \
no-shared \
zlib
```

```bash{5-11} [3.0.17编译选项]
# 作为公共依赖库，推荐以root用户安装它
mkdir /server/openssl-3.0.17
cd /root/openssl-3.0.17/

./config --prefix=/server/openssl-3.0.17 \
--openssldir=/server/openssl-3.0.17 \
no-shared \
zlib
```

```bash [编译安装]
make -j4 > make.log
make test > make-test.log
make install
```

```bash{2,8-10} [1.1.1w配置]
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

```bash{2,8-10} [3.0.17配置]
# 设置新的 PKG_CONFIG_PATH，排除系统默认的 OpenSSL 库路径
export PKG_CONFIG_PATH=/server/openssl-3.0.17/lib64/pkgconfig:$PKG_CONFIG_PATH

# 使用下面指令检查，是否正确替换
pkg-config --path openssl,libssl,libcrypto

# 成功替换展示：
/server/openssl-3.0.17/lib/pkgconfig/openssl.pc
/server/openssl-3.0.17/lib/pkgconfig/libssl.pc
/server/openssl-3.0.17/lib/pkgconfig/libcrypto.pc

# 未成功替换展示：
/usr/lib/x86_64-linux-gnu/pkgconfig/openssl.pc
/usr/lib/x86_64-linux-gnu/pkgconfig/libssl.pc
/usr/lib/x86_64-linux-gnu/pkgconfig/libcrypto.pc
```

:::

## 编译 `ICU4C` 特定版本 {#assign-icu-version}

Debian/Ubuntu 系统仓库中对 `ICU4C` 库的命名约定为 `libicu`。

系统自带的 libicu 版本如果不能满足编译环境需求，就需要通过手动编译特定版本来满足需求。

比如：debian13 自带的是 libicu-76.x，而 php7.4.33 需要 libicu-72.1 版本，此时我们就必须手动编译 `libicu-72.1` 。

下面以 Debin13 编译安装 `ICU4C-72.1` 为例：

::: code-group

```bash [编译安装]
mkdir /server/icu4c-72_1
wget https://github.com/unicode-org/icu/releases/download/release-72-1/icu4c-72_1-src.tgz
tar - xzf icu4c-72_1-src.tgz
cd icu/source/
./configure --prefix=/server/icu4c-72.1 \
--enable-static
make -j4 > make.log
make check > make-check.log
make install
```

```bash [配置]
# 设置新的 PKG_CONFIG_PATH，排除系统默认的 icu 库路径
export PKG_CONFIG_PATH=/server/icu4c-72.1/lib/pkgconfig:$PKG_CONFIG_PATH

# 使用下面指令检查，是否正确替换
pkg-config --path icu-i18n, icu-io, icu-uc

# 成功替换展示：
/server/icu4c-72.1/lib/pkgconfig/icu-i18n.pc
/server/icu4c-72.1/lib/pkgconfig/icu-io.pc
/server/icu4c-72.1/lib/pkgconfig/icu-uc.pc

# 未成功替换展示：
/usr/lib/x86_64-linux-gnu/pkgconfig/icu-i18n.pc
/usr/lib/x86_64-linux-gnu/pkgconfig/icu-io.pc
/usr/lib/x86_64-linux-gnu/pkgconfig/icu-uc.pc
```

::: danger :warning:警告
debian13 编译 libicu-72.1 后，使用 `make check` 检测是存在报错情况的，这说明依赖环境并不能完全满足，正确的部署环境应该是，使用尽可能符合要求的系统发行版安装环境，像 debian13 发行版对 php7.4 的环境支持度已经很低，如果 php5.x 还要安装到 debian13 上，可能支持度会更差，你需要解决大量的依赖问题，这完全不可取的。从下个发行版开始我们将移除对 `php7.4` 的支持工作
:::

## 附录：

### 一、预构建包一键安装脚本

::: code-group

```md [说明]
-   Postgres 默认有个超级管理员用户 `admin` 密码 `1`
-   MySQL 默认有个本地用户 `admin@localhost` 密码 `1`
-   MySQL 默认有个局域网用户 `admin@'192.168.%.%'` 密码 `1`
-   Redis 默认设置了全局密码 `1`
```

<<<@/assets/environment/lnmpp-setup.sh [lnmpp]
:::

### 二、输出重定向说明

在 Linux 系统中，输出重定向是一个非常重要的功能，它允许你将命令的输出内容（包括标准输出和标准错误输出）重定向到文件、其他命令或丢弃，而不是默认显示在终端上。掌握输出重定向可以帮助你更好地控制命令的输出，记录日志，调试程序等。

#### 基本概念

在 Linux 中，每个命令通常有以下三种输出流：

| 流类型       | 文件描述符 | 含义           | 默认输出位置 | 英文别名                  |
| ------------ | :--------: | -------------- | ------------ | ------------------------- |
| 标准输出     |     1      | 正常输出结果   | 终端         | Standard Output，`stdout` |
| 标准错误输出 |     2      | 错误或警告信息 | 终端         | Standard Error，`stderr`  |
| 标准输入     |     0      | 输入来源       | 键盘输入     | Standard Input，`stdin`   |

输出重定向主要涉及 `stdout`（1）和 `stderr`（2），通过重定向符号可以将这些输出流重定向到文件或其他地方。

#### 常用输出重定向符号

|  符号  | 含义                                     | 示例                       |
| :----: | ---------------------------------------- | -------------------------- |
|  `>`   | 将 stdout 重定向到文件（覆盖）           | command `>` file           |
|  `>>`  | 将 stdout 重定向到文件（追加）           | command `>>` file          |
|  `2>`  | 将 stderr 重定向到文件（覆盖）           | command `2>` file          |
| `2>>`  | 将 stderr 重定向到文件（追加）           | command `2>>` file         |
|  `&>`  | 将 stdout 和 stderr 重定向到文件（覆盖） | command `&>` file          |
| `&>>`  | 将 stdout 和 stderr 重定向到文件（追加） | command `&>>` file         |
| `2>&1` | 将 stderr 重定向到 stdout（覆盖）        | command `2>&1` file        |
|  `\|`  | 管道符号，传递信息给另 1 个命令 ​​       | `ls -l / \| grep 'server'` |
| `tee`  | 将数据同时写入到文件和终端               | command `\|` `tee` file    |

#### 案例展示

::: code-group

```bash [仅显示stderr]
# 终端输出错误和警告，文件记录正常信息
cmake \
-DWITH_DEBUG=ON \
-DCMAKE_INSTALL_PREFIX=/server/mysql \
-DWITH_SYSTEMD=ON \
-DFORCE_COLORED_OUTPUT=ON \
-DWITH_MYSQLX=OFF \
-DWITH_UNIT_TESTS=OFF \
-DINSTALL_MYSQLTESTDIR= \
.. > stdout.log
```

```bash [仅记录stderr]
# 终端输出正常信息，文件记录错误和警告
cmake \
-DWITH_DEBUG=ON \
-DCMAKE_INSTALL_PREFIX=/server/mysql \
-DWITH_SYSTEMD=ON \
-DFORCE_COLORED_OUTPUT=ON \
-DWITH_MYSQLX=OFF \
-DWITH_UNIT_TESTS=OFF \
-DINSTALL_MYSQLTESTDIR= \
.. 2> stdout.log
```

:::
