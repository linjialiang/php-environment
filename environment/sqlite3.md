---
title: 安装 SQLite3
titleTemplate: 环境搭建教程
---

# 安装 SQLite3

[SQLite3](https://www.sqlite.org) 是一个轻量级的嵌入式关系数据库管理系统

::: tip sqlite3 的一些特点

1. SQLite3 不需要配置，不需要安装，也不需要管理员，最牛的是它没有服务器，仅是一个文件；

2. 数据库以文件形式保存在磁盘上，可以自由 COPY 使用；

3. 因为 SQLite3 没有服务器监听端口，所以不能像 mysql 一样通过 ip 和端口远程连接数据库。如果想远程访问数据库，只能通过数据库文件共享方式。

:::

## 编译安装

::: code-group

```bash [编译套件]
# 推荐安装通用的编译套件
apt install -y build-essential autoconf, libtool, pkg-config
```

```bash [依赖项]
apt install -y tcl
```

```bash [构建选项]
su - sqlite -s /bin/zsh
tar -xzf sqlite-autoconf-3500400.tar.gz
cd ~/sqlite-autoconf-3500400/
./configure --prefix=/server/sqlite > stdout.log
```

```bash [编译安装]
make -j4 > make.log
make install
```

:::

## 权限

::: code-group

```bash [部署]
chown sqlite:sqlite -R /server/sqlite
find /server/sqlite -type f -exec chmod 640 {} \;
find /server/sqlite -type d -exec chmod 750 {} \;
# 可执行文件需要执行权限
chmod 750 -R /server/sqlite/bin
```

```bash [开发]
# 权限同部署环境
# 开发用户 emad 加入 lnpp包用户组
usermod -a -G sqlite,redis,postgres,mysql,php-fpm,nginx emad
```

:::

到此，SQLite3 简单构建安装就完成了，不需要配置就可以使用
