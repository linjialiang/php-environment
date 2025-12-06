---
title: 概述
titleTemplate: 环境搭建教程
---

# 环境搭建概述

此版本代号为 `x.5.x`，简称 `LNPP`，主要涉及内容有：

| package    | 说明                       |
| ---------- | -------------------------- |
| Debian13   | 系统发行版                 |
| PostgreSQL | 关系型数据库               |
| PHP        | 后端脚本语言               |
| Nginx      | Web 服务器和反向代理服务器 |

::: info 增加安装文档，但不推荐使用的数据库：

| package | 说明               |
| ------- | ------------------ |
| Redis   | 键值存储数据库     |
| MongoDB | 文档数据库         |
| Sqlite3 | 轻量级关系型数据库 |
| MySQL   | 关系型数据库       |

:::

此版本的宗旨是后端数据统一使用 PostgreSQL 数据库。

下面开始手工搭建 `LNPP` 环境：

## 测试环境

::: code-group

```[debian13]
系统 : Debian GNU/Linux 13 (trixie)
内核 : Linux debian 6.12.48+deb13-amd64
```

<<< @/assets/environment/lnpp-toc.txt [环境目录结构]
:::

## 脚本文件

我们准备了几个 bash 脚本文件：

::: code-group
<<< @/assets/environment/source/bash/user.bash [创建用户]
<<< @/assets/environment/source/bash/mkdir.bash [创建目录]
<<< @/assets/environment/source/bash/chown.bash [目录授权]
<<< @/assets/environment/source/bash/tar.bash [解压参考]
<<< @/assets/environment/source/bash/cleanLog.sh [一键清理]
<<< @/assets/environment/source/bash/clean.sh [简单清理]
:::

## 包列表

::: code-group

```md [包列表]
1. postgresql-18.1.tar.bz2
2. php-8.4.14.tar.xz
    - 动态扩展
        - xdebug-3.4.7.tgz
        - apcu-5.1.27.tgz
3. nginx-1.28.0.tar.gz
    - openssl-3.5.4.tar.gz
    - pcre2-10.47.tar.bz2
    - zlib-1.3.1.tar.xz
```

```md [包下载]
| package    | url                                   |
| ---------- | ------------------------------------- |
| PostgreSQL | https://www.postgresql.org/           |
| PHP        | https://www.php.net/                  |
| PHP extend | http://pecl.php.net/                  |
| Nginx      | http://nginx.org/                     |
| zlib       | http://www.zlib.net/                  |
| openssl    | https://openssl-library.org/          |
| pcre2      | https://github.com/PCRE2Project/pcre2 |
```

:::

<!-- 引入请求生命周期 -->
<!--@include: ./trait/lifecycle.md-->

<!-- 引入用户说明 -->
<!--@include: ./trait/userinfo.md-->

<!-- 引入用户权限 -->
<!--@include: ./trait/user_power.md-->

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
<!--@include: ./trait/kernel.md-->

<!-- 引入日志管理 -->
<!--@include: ./trait/log_management.md-->

<!-- 引入手动安装依赖 -->
<!--@include: ./trait/package_install.md-->

<!-- 引入输出重定向 -->
<!--@include: ./trait/output.md-->

## 附录二、预构建包一键安装脚本

::: code-group

```md [说明]
-   Postgres 默认有个超级管理员用户 `admin` 密码 `1`
```

<<< @/assets/environment/lnpp-setup.sh [lnpp]
:::

## 附录三、PHP 探针

<<< @/assets/environment/source/php/tz.php
