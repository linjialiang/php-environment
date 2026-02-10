---
title: 安装 SQLite3 库
titleTemplate: 环境搭建教程
---

# 安装 SQLite3 库

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

```bash [编译安装]
su - sqlite3 -s /bin/zsh

tar -xzf sqlite-autoconf-3510200.tar.gz
cd ~/sqlite-autoconf-3510200/

# 清理 make
make distclean

./configure --prefix=/server/sqlite3 > stdout.log

make -j4 > make.log
make install
```

## 权限

```bash
chown sqlite3:sqlite3 -R /server/sqlite3
find /server/sqlite3 -type f -exec chmod 640 {} \;
find /server/sqlite3 -type d -exec chmod 750 {} \;
chmod 750 -R /server/sqlite3/bin
```

到此，SQLite3 简单构建安装就完成了!
