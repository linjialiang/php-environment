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

### 2. 初始化数据库

::: code-group

```ps1 [17]
C:
cd C:\pgsql\product\17\bin
.\initdb.exe -D "C:\pgsql\data\17" -E UTF8 --locale=Chinese_China.936 -U postgres -W

# 测试 开启服务
.\pg_ctl.exe -D "C:\pgsql\data\17" -l "C:\pgsql\log\17\logFile" start
# 测试 停止服务
.\pg_ctl.exe -D "C:\pgsql\data\17" -l "C:\pgsql\log\17\logFile" start
```

:::

::: tip data 目录权限说明

权限方面感觉有点奇怪：

1. 我的操作系统登录账户是 `Administrator`；
2. `C:\pgsql\` 目录授权给用户组 `Administrators` 全部权限依然会报错；
3. `C:\pgsql\` 必须授权给用户 `Administrator` 全部权限才可以正常初始化。

:::

### 3. 注册 Windows 服务

Windows 版的 PgSQL 不支持 `Administrator` 账户作为服务所属账户，注册是需要指定服务所属系统账户。

::: details 这里以 postgres 系统账户为例(系统账户名可以随便取，不一定是 postgres)
![创建系统用户](/assets/iis/pgsql/3.jpg)
:::

::: code-group

```ps1 [17]
C:
cd C:\pgsql\product\17\bin
.\pg_ctl.exe register -D "C:\pgsql\data\17" -N "pgsql-17" -U postgres -P "1" -S demand
```

:::
