---
title: 概述
titleTemplate: 环境搭建教程
---

# 环境搭建概述

下面开始手工搭建 PHP 的开发环境、部署环境

## 测试环境

测试环境 `虚拟机` 的系统参数如下：

-   系统 :` Debian GNU/Linux 12 (Bookworm) x86_64`
-   内核 : `linux-image-6.1.0-26-amd64`

::: details PHP 环境目录结构

::: code-group
<<<@/assets/environment/lnmpp-toc.txt [lnmpp]
<<<@/assets/environment/lnpp-toc.txt [lnpp]
<<<@/assets/environment/lnmp-toc.txt [lnmp]
:::

## 脚本文件

::: details 我们准备了几个 bash 脚本文件：

::: code-group
<<<@/assets/environment/source/bash/user.bash [用户]
<<<@/assets/environment/source/bash/mkdir.bash [创建]
<<<@/assets/environment/source/bash/tar.bash [解压]
<<<@/assets/environment/source/bash/chown.bash [授权]
:::

## 安装包列表

::: code-group

```md [lnmpp]
1. sqlite-autoconf-3490200.tar.gz
2. redis-7.4.3.tar.gz
3. mysql-8.4.5.tar.gz
4. postgresql-17.4.tar.bz2
5. php-7.4.33.tar.xz
    - 动态扩展
        - xdebug-3.1.6.tgz `最后支持版`
        - apcu-5.1.24.tgz
        - mongodb-1.20.1.tgz `最后支持版`
        - redis-6.2.0.tgz
        - yaml-2.2.4.tgz
    - 依赖库
        - openssl-1.1.1w.tar.gz
6. php-8.4.7.tar.xz
    - 动态扩展
        - xdebug-3.4.2.tgz
        - apcu-5.1.24.tgz
        - mongodb-2.0.0.tgz
        - redis-6.2.0.tgz
        - yaml-2.2.4.tgz
7. nginx-1.28.0.tar.gz
    - openssl-3.5.0.tar.gz
    - pcre2-10.45.tar.bz2
    - zlib-1.3.1.tar.xz
```

```md [lnpp]
1. sqlite-autoconf-3490200.tar.gz
2. redis-7.4.3.tar.gz
3. postgresql-17.4.tar.bz2
4. php-7.4.33.tar.xz
    - 动态扩展
        - xdebug-3.1.6.tgz `最后支持版`
        - apcu-5.1.24.tgz
        - mongodb-1.20.1.tgz `最后支持版`
        - redis-6.2.0.tgz
        - yaml-2.2.4.tgz
    - 依赖库
        - openssl-1.1.1w.tar.gz
5. php-8.4.7.tar.xz
    - 动态扩展
        - xdebug-3.4.2.tgz
        - apcu-5.1.24.tgz
        - mongodb-2.0.0.tgz
        - redis-6.2.0.tgz
        - yaml-2.2.4.tgz
6. nginx-1.28.0.tar.gz
    - openssl-3.5.0.tar.gz
    - pcre2-10.45.tar.bz2
    - zlib-1.3.1.tar.xz
```

```md [lnmp]
1. sqlite-autoconf-3490200.tar.gz
2. redis-7.4.3.tar.gz
3. mysql-8.4.5.tar.gz
4. php-7.4.33.tar.xz
    - 动态扩展
        - xdebug-3.1.6.tgz `最后支持版`
        - apcu-5.1.24.tgz
        - mongodb-1.20.1.tgz `最后支持版`
        - redis-6.2.0.tgz
        - yaml-2.2.4.tgz
    - 依赖库
        - openssl-1.1.1w.tar.gz
5. php-8.4.7.tar.xz
    - 动态扩展
        - xdebug-3.4.2.tgz
        - apcu-5.1.24.tgz
        - mongodb-2.0.0.tgz
        - redis-6.2.0.tgz
        - yaml-2.2.4.tgz
6. nginx-1.28.0.tar.gz
    - openssl-3.5.0.tar.gz
    - pcre2-10.45.tar.bz2
    - zlib-1.3.1.tar.xz
```

:::

::: details 源码包下载地址

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

:::

## 请求生命周期

::: details Nginx 处理一次请求的生命周期

在 Nginx 中，处理一次请求的整个生命周期涉及到 master 进程和 worker 进程的协同工作。以下是详细介绍每个阶段中 master 进程和 worker 进程的作用：

1. 初始化阶段：

    - master 进程：负责读取和验证 Nginx 配置文件，初始化工作环境。
    - worker 进程：由 master 进程根据配置文件中定义的 worker_processes 参数创建，数量通常与服务器的 CPU 核数一致，以充分利用多核特性。

2. 监听和接受连接阶段：

    - master 进程：在初始化完成后，master 进程会监听配置中指定的端口。
    - worker 进程：负责接受来自客户端的连接请求。由于 worker 进程与 CPU 核心绑定，可以高效地处理并发连接。

3. 处理请求阶段：

    - master 进程：不直接参与请求的处理，主要负责监控和管理 worker 进程。
    - worker 进程：每个 worker 进程只有一个线程，它们独立处理请求，执行如解析请求头、查找配置、重写 URI、访问权限检查等任务，并将结果返回给客户端。

4. 发送响应阶段：

    - master 进程：继续监控 worker 进程的状态，确保系统稳定运行。
    - worker 进程：负责将响应数据发送回客户端，并记录访问日志。

5. 关闭连接阶段：

    - master 进程：不直接参与连接的关闭。
    - worker 进程：在完成响应后，负责关闭与客户端的连接。

6. 重新加载和退出阶段：

    - master 进程：处理信号如 SIGTERM 来平滑退出或重新加载配置文件，创建新的 worker 进程来替换旧的 worker 进程，从而不影响正在处理的请求。
    - worker 进程：在收到退出信号后，完成当前请求的处理，然后退出。

::: tip 总结：

1. 客户端发起的所有请求均由 worker 进程处理，而 master 进程不直接参与处理这些请求。
2. 也就是说，客户端发起请求只使用了 worker 进程用户权限，不会涉及到 master 进程用户权限

::: warning 问题：浏览器是否具备 Nginx worker 进程用户的权限？

浏览器作为客户端，不具备 nginx worker 进程用户的任何权限，浏览器只是通过建立的 TCP 连接来接收 Nginx worker 进程发送的内容
:::

::: details PHP-FPM 处理一次请求的生命周期

php-fpm 处理一次请求的生命周期包括请求接收、请求处理和请求结束三个阶段:

1. 请求接收阶段：

    当 php-fpm 的 master 进程接收到一个来自 Web 服务器（如 Nginx）的请求时，它会加锁以避免多个 worker 进程同时处理同一个请求，这在 Linux 中称为"惊群问题"。

    Master 进程随后会指派一个可用的 worker 进程来处理这个请求。如果没有可用的 worker 进程，将返回错误，这也是在使用 Nginx 配合 php-fpm 时可能遇到 502 错误的一个原因。

2. 请求处理阶段：

    被指派的 worker 进程开始处理请求。这个过程中，worker 进程会执行 PHP 脚本并生成响应结果。如果处理过程超时，可能会返回 504 错误。

    Worker 进程内部嵌入了 PHP 解释器，是 PHP 代码实际执行的地方。

3. 请求结束阶段：

    一旦 worker 进程完成了请求处理，它会将结果返回给 Web 服务器，从而完成整个请求的处理周期。

:::

## 用户说明

::: details 在用户脚本中我们创建了多个用户：

| 用户名   | 说明            |
| -------- | --------------- |
| emad     | 开发者用户      |
| sqlite   | SQLite3 用户    |
| redis    | Redis 用户      |
| postgres | PostgreSQL 用户 |
| mysql    | MySQL 用户      |
| php-fpm  | PHP-FPM 用户    |
| nginx    | Nginx 用户      |

:::

::: details `Nginx` 和 `PHP-FPM` 进程和用户关系比较复杂：

::: code-group

```md [Nginx 进程]
| process      | user  |
| ------------ | ----- |
| Nginx master | nginx |
| Nginx worker | nginx |

> Nginx 主进程：

-   master 进程用户需要有 worker 进程用户的全部权限，master 进程用户类型：
    1.  特权用户(root)：worker 进程可以指定为其它非特权用户；
    2.  非特权用户：worker 进程跟 master 进程是同一个用户。

> Nginx 工作进程：

-   worker 进程负责处理实际的用户请求
-   代理转发和接收代理响应都是由 worker 进程处理

> Nginx 配置文件 `user` 指令限制说明：

-   主进程是特权用户(root)：`user` 指令是有意义，用于指定工作进程用户和用户组
-   主进程是非特权用户：`user` 指令没有意义，会被 Nginx 程序忽略掉
```

```md [PHP-FPM 进程]
| process           | user    |
| ----------------- | ------- |
| PHP-FPM master    | php-fpm |
| PHP-FPM pool 进程 | php-fpm |

> PHP-FPM 主进程：

-   master 进程负责管理 pool 进程
-   master 进程创建和管理 pool 进程的 sock 文件
-   master 进程需要有 pool 进程用户的全部权限，master 进程用户类型：
    1.  特权用户（root）：pool 进程可以指定为其它非特权用户
    2.  非特权用户：pool 进程用户跟 master 进程用户相同

> PHP-FPM 工作池进程：

-   pool 进程独立地处理请求，执行 PHP 脚本代码
-   pool 进程处理完 PHP 代码后，会直接将结果返回给客户端
```

```md [代理转发]
> 具体流程如下：

1. Nginx master 进程：当有新的请求到来时，master 进程会将其分配给一个 worker 进程来处理。
2. Nginx worker 进程：如果请求是静态资源，则直接返回给客户端；如果请求是 PHP 文件，则通过 `PHP-FPM pool` 进程的 sock 文件将请求转发给 PHP-FPM 进行处理。
3. PHP-FPM master 进程：当收到 `PHP-FPM pool` 进程的 sock 文件传递的请求时，master 进程会将其分配给一个 pool 进程来处理。
4. PHP-FPM pool 进程：pool 进程执行和处理 PHP 代码，并将返回结果发送给对应的 sock 文件
5. 返回结果：Nginx 的 worker 进程，接收 sock 文件的响应内容，再将处理后的动态内容返回给客户端。

> nginx 站点代理转发 php 请求时：

-   Nginx 主进程用户不需要对 PHP-FPM 的 socket 文件拥有任何权限，处理请求的是工作进程
-   Nginx 工作进程用户需要对 PHP-FPM 的 socket 文件具有 `读+写` 权限
    1.  读取权限：Nginx 工作进程需要读取 socket 文件以发送请求到 PHP-FPM
    2.  写入权限：Nginx 工作进程也需要写入权限，以便接收来自 PHP-FPM 的响应
-   PHP-FPM 主进程用户需要对 sock 文件具有全部权限
    1. 创建/删除 pool 进程的 sock 文件
    2. 监听指定的端口或 Unix 套接字文件，以便接收来自 Web 服务器（如 Nginx）的请求。
    3. 由

> PHP-FPM 的套接字文件：

-   pool 进程的 sock 文件监听用户: `php-fpm`
-   pool 进程的 sock 文件监听用户组: `nginx`
-   pool 进程的 sock 文件监听权限: `0660`
-   用户 php-fpm 的附属用户组增加 `nginx`
```

:::

### 用户职责

-   `用户 nginx` 是 Nginx worker 进程的 Unix 用户
-   `用户 php-fpm` 是 PHP-FPM 子进程的 Unix 用户
-   `用户 emad` 是开发者操作项目资源、文件的用户

### 用户权限

::: code-group

```md [nginx]
-   对静态文件需提供 `读` 的权限
-   对 php-fpm 的 `unix socket` 文件提供了读写的权限

浏览器等客户端使用 nginx 用户浏览网站：1)加载静态文件; 2) php-fpm 的 `unix socket` 文件传输

1. 使用 socket 转发，nginx 用户可作为 FPM 的监听用户，如：监听 socket、连接 web 服务器，权限设为 660
2. 使用 IP 转发，FPM 无需监听用户
```

```md [php-fpm]
1. FPM 进程运行的 Unix 用户，对 php 脚本、php 所需的配置文件需要 `读` 的权限；
2. 当 php 需要操作文件或目录时，需要提供 `读+写` 权限：
    - 如：框架中记录运行时日志、缓存的 runtime 目录，就需要 `读+写` 全新
3. 除此以外，php-fpm 用户通常不需要其他权限
```

```md [emad]
-   开发环境：需要对 php 文件、静态文件有 `读+写` 的权限;
-   部署环境：平时可以不提供任何权限，因为该用户与服务没有关联;
-   部署环境：对需要变动的文件，需要具有`读+写`的权限;

> 说明：emad 泛指开发者账户，你可以取其它名字
```

:::

::: tip
新版 lnpp 将采用 tcp 代理转发方式，所以 socket 文件权限不需要过多考虑
:::

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

## 附录：预构建包一键安装脚本

::: code-group

```md [说明]
-   Postgres 默认有个超级管理员用户 `admin` 密码 `1`
-   MySQL 默认有个本地用户 `admin@localhost` 密码 `1`
-   MySQL 默认有个局域网用户 `admin@'192.168.%.%'` 密码 `1`
-   Redis 默认设置了全局密码 `1`
```

<<<@/assets/environment/lnmpp-setup.sh [lnmpp]
<<<@/assets/environment/lnmp-setup.sh [lnmp]
<<<@/assets/environment/lnpp-setup.sh [lnpp]
:::
