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

::: details 截图
![Windows上启用IIS功能](/assets/iis/enable-iis.png)
:::

::: details 文字

```
====================================================
启用或关闭 Windows 功能启用
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

1. 详情请查看 [[官方介绍链接]](https://www.iis.net/downloads/microsoft/url-rewrite)
2. 简体中文版下载位置

    ::: details 截图
    点击进入 `[官方介绍链接]` ，滚动到最底部，点击 `Chinese Simplified:` 含的 `x64 installer` 下载

    ![下载url重写模块](/assets/iis/url-rewrite.png)
    :::
