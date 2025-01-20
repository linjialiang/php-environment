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

## 三、权限说明

### 1. Windows 与 Linux 权限上的区别

-   Linux 想要获得 `指定路径文件(或目录)` 的权限，必须确保用户对该文件的所有上级目录具备 `执行(x)` 权限；
-   Windows 想要获得 `指定路径文件(或目录)` 的权限，只需用户对当前文件(或目录)具备对应权限即可，无需考虑上级目录权限问题。

### 2. 创建系统用户/用户组

::: code-group

```md [IIS站点用户]
-   `www` : IIS 站点用户[通用]

> 通常你可以为每个站点创建 1 个独立的用户，案例创建了 1 个统一的站点用户：
```

```md [IIS应用池用户]
1. `iis` : IIS 应用池用户[默认]
    - 归属用户组 `IIS_AppPool_Users`
2. `iis_php74` : IIS 应用池用户[支持 PHP7.4 的 CGI]
    - 归属用户组 `PHP_CGI_Users` `IIS_AppPool_Users`
3. `iis_php84` : IIS 应用池用户[支持 PHP8.4 的 CGI]
    - 归属用户组 `PHP_CGI_Users` `IIS_AppPool_Users`
```

```md [用户组]
1. `IIS_AppPool_Users` : IIS 应用池用户组
2. `PHP_CGI_Users` : 多版本 PHP CGI 通用用户组
```

:::

::: details 截图

:::

## 四、IIS 应用池

1. 添加 1 个默认应用池，应用于静态站点

    - 指定标示为 `iis` 用户(需指定密码)

    ::: details 截图

    :::

## 五、站点应用案例

### 1. 静态站点

::: details 1. 添加一个默认应用池用于

:::

::: code-group

```[项目目录结构]
├─ D:\www\doc.php.io ========================================== [项目根目录]
|   ├─ php-chunked-xhtml ------------------- 主要子目录
|   ├─ web.config -------------------------- 站点伪静态
|   └─ ...
└─
```

```md [权限说明]
用户 `www` 需对项目根目录及子目录拥有 `基本权限-读取`
```

:::

### 2. ThinkPHP 项目

::: code-group

```md [ThinkPHP 项目]
用户 `iis_php84` 需对项目根目录及子目录拥有 `基本权限-读取`
用户 `iis_php84` 需对上传目录 `/public/static/uploads` 拥有 `基本权限-读取+写入+修改`
用户 `www` 需对站点根目录 `/public` 及子目录拥有 `基本权限-读取`
```

:::
