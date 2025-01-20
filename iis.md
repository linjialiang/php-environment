---
title: IIS 篇
titleTemplate: 环境搭建教程
---

# IIS 下搭建 PHP 环境

IIS 是 Windows 上自带的 Web 服务器，经过多年的打磨(于 1995 年发布)，在功能上做的比较完善。

::: tip
如果环境部署在 Windows 上，并且对权限安全有更高要求，我们推荐使用 IIS ！
:::

## 一、安装 IIS

针对 PHP CGI 我们至少需要启用以下功能：

### 1. 启用功能

-   资源管理器进入 `控制面板\程序\程序和功能`；
-   点击左侧的 `启用或关闭 Windows 功能`；
-   启用的功能请查看下面的截图或文字：

::: details 截图
![Windows上启用IIS功能](/assets/iis/control-panel.png)
![Windows上启用IIS功能](/assets/iis/enable-iis.png)
:::

::: details 文字

```
====================================================
启用或关闭 Windows 功能
====================================================
├─ Internet Information Services
|   ├─ Web 管理工具
|   |  ├─ IIS 管理服务
|   |  ├─ IIS 管理脚本和工具
|   |  ├─ IIS 管理控制台
|   |  └─ ...
|   |
|   ├─ 万维网服务
|   |  ├─ 常见 HTTP 功能
|   |  |  ├─ 静态内容
|   |  |  ├─ 默认文档
|   |  |  └─ ...
|   |
|   ├─ 应用程序开发功能
|   |  ├─ CGI
|   |  └─ ...
|   |
|
```

:::

### 2. 安装 URL 重写模块

-   进入 [`[官方介绍链接]`](https://www.iis.net/downloads/microsoft/url-rewrite) 并滚动到底部；
-   点击 `Chinese Simplified:` 行的 `x64 installer` 进行下载；

    ::: details 截图
    ![下载url重写模块](/assets/iis/url-rewrite.png)
    :::

-   双击 `rewrite_amd64_zh-CN.msi` 安装。

## 二、PHP 解释器

案例使用了 2 个 PHP 版本的解释器：

1. PHP 8.4.3
2. PHP 7.4.33

::: details 安装目录如下：

```
├─ C:\php ==================================================== [PHP 安装根目录]
|   ├─ 74 ------------------------------------------ PHP 7.4.x 解释器根目录
|   |  ├─ ext -------------------- PHP 扩展存放路径
|   |  ├─ composer.bat ----------- Composer 执行脚本
|   |  ├─ php.ini ---------------- php 配置文件
|   |  ├─ php.exe ---------------- 用于命令行的执行文件
|   |  ├─ php-cgi.exe ------------ 用于 CGI 的执行文件
|   |  ├─ phpdbg.exe ------------- PHP的SAPI模块(是个调试工具)
|   |  ├─ php-win.exe ------------ 用于命令行的执行文件(不显示输出内容)
|   |  └─ ...
|   |
|   ├─ 84 ------------------------------------------ PHP 8.4.x 解释器根目录
|   |  ├─ ext -------------------- PHP 扩展存放路径
|   |  ├─ composer.bat ----------- Composer 执行脚本
|   |  ├─ php.ini ---------------- php 配置文件
|   |  ├─ php.exe ---------------- 用于命令行的执行文件
|   |  ├─ php-cgi.exe ------------ 用于 CGI 的执行文件
|   |  ├─ phpdbg.exe ------------- PHP的SAPI模块(是个调试工具)
|   |  ├─ php-win.exe ------------ 用于命令行的执行文件(不显示输出内容)
|   |  └─ ...
|   |
|   ├─ config --------------------------------------- PHP 多版本通用配置存放路径
|   |  ├─ cacert.pem ------------- Curl 和 OpenSSL 扩展的证书文件
|   |  └─ ...
|   |
|   ├─ tools ---------------------------------------- PHP 多版本通用工具存放路径
|   |  ├─ composer.phar ---------- Composer 包
|   |  ├─ phing.phar ------------- phing 包
|   |  ├─ php-cs-fixer.phar ------ PHP CS Fixer 包
|   |  └─ ...
|   └─
└─
```

:::
