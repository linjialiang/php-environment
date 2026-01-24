---
title: 安装 SQLite3
titleTemplate: 环境搭建教程
---

# 安装 SQLite3

[SQLite3](https://www.sqlite.org) 是一个轻量级的嵌入式关系数据库管理系统

::: info SQLite3 特色

1. SQLite3 是一个库，不是服务 - 编译得到的是 `.so/.a 库文件` 和 `命令行工具`
2. 无需守护进程 - 应用程序直接链接 SQLite 库操作数据库文件
3. 命令行工具是管理工具 ​- SQLite3 命令用于交互式管理和调试
4. 适合嵌入式场景 ​- 移动应用、桌面软件、小型网站
5. 不适合高并发写入 - 全局写入锁限制了并发写入性能

`sqlite-autoconf-x.tar.gz` 编译后提供的是完整的嵌入式数据库引擎和管理工具
:::

## 编译安装

::: code-group

```bash [依赖项]
apt install tcl gcc make -y
```

```bash [构建选项]
su - sqlite -s /bin/zsh
tar -xzf sqlite-autoconf-3510200.tar.gz
cd ~/sqlite-autoconf-3510200/

./configure --prefix=/server/sqlite3 > stdout.log

make -j4 > make.log
make install
```

:::

## 权限

::: code-group

```bash [部署]
chown sqlite:sqlite -R /server/sqlite3
find /server/sqlite3 -type f -exec chmod 640 {} \;
find /server/sqlite3 -type d -exec chmod 750 {} \;
# 可执行文件需要执行权限
chmod 750 -R /server/sqlite3/bin
```

```bash [开发]
# 权限同部署环境
usermod -a -G sqlite,redis,postgres,php-fpm,nginx,mysql emad
```

:::

到此，SQLite3 简单构建安装就完成了，不需要配置就可以使用
