---
title: 用户与进程
titleTemplate: Linux 下纯手工搭建 PHP 环境
---

# 用户与进程

这节主要让我们先了解服务进程的工作方式。

为了使用真实案例说明，这节内容放在环境搭建完成后进行写作！

::: tip 为开发用户授权

```bash
usermod -a -G sqlite3,redis,postgres,php,nginx,mysql emad
```

:::
