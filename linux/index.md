---
title: 准备工作
titleTemplate: Linux 下纯手工搭建 PHP 环境
---

# 准备工作

这是 `《Linux 下纯手工搭建 PHP 环境》` 2026 年重构版本！

这几年我们不断迭代，文档存在遗留代码也存在错误的说明，本次重构主要涉及：

1. 文档内容紧凑，提升阅读体验；
2. 修复已知错误；
3. 优化目录结构；
4. 优化一键脚本；
5. 升级一键构建包；

## 包列表

主要涉及发行版和软件包如下。

::: code-group
<<< @/assets/linux/info/lsb_release.zsh[发行版]
<<< @/assets/linux/info/package-list.md[包列表]
<<< @/assets/linux/info/package-url.md[包下载地址]
<<< @/assets/linux/info/toc.zsh[目录结构]
:::

## 一键脚本

这几个脚本可以快速创建、授权目录，清理系统缓存。

::: code-group
<<< @/assets/linux/script/common/user.zsh [创建用户]
<<< @/assets/linux/script/common/mkdir.zsh [创建目录]
<<< @/assets/linux/script/common/chown.zsh [目录授权]
<<< @/assets/linux/script/common/cleanLog.zsh [一键清理]
<<< @/assets/linux/script/common/clean.zsh [简单清理]
:::

## 编译套件

这些包是编译软件时最常用到的依赖项

::: code-group

```bash [编译套件]
apt install --no-install-recommends build-essential autoconf pkg-config -y
```

```bash [SQLite3]
apt install --no-install-recommends tcl -y
```

```bash [Redis]
apt install --no-install-recommends libsystemd-dev libssl-dev -y
```

```bash [PostgreSQL]
apt install --no-install-recommends llvm-dev libicu-dev liblz4-dev libzstd-dev \
libbison-dev flex libreadline-dev zlib1g-dev libssl-dev uuid-dev -y
```

```bash [PHP8.5]
apt install --no-install-recommends -y
```

```bash [Nginx]
apt install --no-install-recommends -y
```

:::
