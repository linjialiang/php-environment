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

<!-- 引入用户权限 -->
<!-- @include: ./trait/user_power.md -->

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

::: warning :warning:注意

bash/zsh 配置文件开头需要载入 `~/.profile`：

```bash
# ~/.(bashrc|zshrc)
source ~/.profile
```

:::

## 编译器选择

::: warning PostgreSQL 推荐使用 `CLANG+LLVM` 编译套件，`--with-llvm` 启用 JIT 支持，能提升查询性能；其余软件优先使用 `GCC` 编译套件。
:::

<!-- 引入系统优化 -->
<!-- @include: ./trait/kernel.md -->

<!-- 引入日志管理 -->
<!-- @include: ./trait/log_management.md -->

<!-- 引入手动安装依赖 -->
<!-- @include: ./trait/package_install.md -->

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

<!-- 引入输出重定向 -->
<!-- @include: ./trait/output.md -->
