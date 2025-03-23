## 八、PostgreSQL

::: details 下载 zip 存档步骤
下载页面 https://www.postgresql.org/download/windows/

![zip存档列表](/assets/iis/pgsql/1.jpg)
![选择版本](/assets/iis/pgsql/2.jpg)
:::

### 1. 目录结构

::: details 安装目录如下：

```
├─ C:\mysql ==================================================== [MySQL 安装根目录]
|   ├─ data -------------------------------------------- 数据基目录
|   |  ├─ 17 --------------------- postgresql 17 数据
|   |  |  ├─
|   |  |  ├─
|   |  |  └─ ...
|   |  |
|   |  └─ ...
|   |
|   ├─ product ------------------------------------------ 产品源码基目录
|   |  ├─ 17 --------------------- postgresql-17.4-1
|   |  |  ├─ bin
|   |  |  ├─
|   |  |  └─ ...
|   |  |
|   |  └─ ...
|   |
|   ├─ tls ----------------------------------------------- 产品源码基目录
|   |  ├─ root.{crt,key} --------- CA证书用于签署其他证书，以建立信任链
|   |  ├─ pgsql.{crt,key} -------- 这个证书可用于SSL/TLS连接中的任何用途
|   |  ├─ server.{crt,key} ------- 只能用于SSL/TLS连接中的服务器身份验证
|   |  ├─ client.{crt,key} ------- 只能用于SSL/TLS连接中的客户端身份验证
|   |  ├─ client-emad.{crt,key} -- 允许用户emad使用verify-full验证类型
|   |  ├─ client-admin.{crt,key} - 允许用户admin使用verify-full验证类型
|   |  ├─ pgsql.dh --------------- 在SSL/TLS握手过程中协商临时密钥，以确保通信的安全性
|   |  |
|   |  └─ ...
|   |
|   └─
└─
```

:::

## 2. 初始化数据库

::: code-group

```ps1 [17]
C:
cd C:\pgsql\product\17\bin
.\initdb.exe -D "C:\pgsql\data\17" -E UTF8 --locale=Chinese_China.936 -U postgres -W
```

:::

::: tip data 目录权限说明

`C:\pgsql\data` 需要拥有当前打开的终端窗口所属用户的权限全部权限，因为打开终端的用户为服务器进程的宿主：

-   终端以超级管理员身份打开：需要 `Administrator` 用户 `读+写` 权限;
-   终端以用户 emad 身份打开：需要 `emad` 用户 `读+写` 权限.

:::
