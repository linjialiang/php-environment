## 八、PostgreSQL

::: details 下载 zip 存档步骤
下载页面 https://www.postgresql.org/download/windows/

![zip存档列表](/assets/iis/pgsql/1.jpg)
![选择版本](/assets/iis/pgsql/2.jpg)
:::

### 1. 目录结构

::: details 安装目录如下：

```
├─ C:\mysql ==================================================== [PgSQL 根目录]
|   ├─ data -------------------------------------------- 数据基目录
|   |  ├─ 17 --------------------- pgsql 17 数据
|   |  |  ├─ pg_hba.conf ----- 客户端配置
|   |  |  ├─ postgresql.conf - 服务端配置
|   |  |  └─ ...
|   |  |
|   |  └─ ...
|   |
|   ├─ product ------------------------------------------ 产品源码基目录
|   |  ├─ 17 --------------------- postgresql-17.4-1
|   |  |  ├─ bin/
|   |  |  └─ ...
|   |  |
|   |  └─ ...
|   |
|   ├─ log ------------------------------------------ 产品源码基目录
|   |  ├─ 17 --------------------- pgsql 17 日志
|   |  |  ├─ postgres.log -------- 操作日志文件
|   |  |  ├─ wal_archive/ -------- WAL日志目录
|   |  |  └─ ...
|   |  |
|   |  └─ ...
|   |
|   ├─ temp ----------------------------------------- 临时目录
|   |  ├─ 17 --------------------- pgsql 17 临时目录
|   |  |  ├─ process.pid -------- pid文件
|   |  |  ├─ .s.PGSQL.5432 ------ 套接字文件
|   |  |  ├─ .s.PGSQL.5432.lock - 套接字锁
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
|   |  └─ ...
|   |
|   └─
└─
```

::: tip 默认账户

-   postgres: 超级用户，密码 1

    -   支持套接字登录
    -   支持 TCP/IP 登录

-   admin: 超级用户，密码 1
    -   支持套接字登录
    -   支持 TCP/IP 登录
    -   支持 TLS 登录

:::

### 2. 初始化数据库

::: code-group

```ps1 [17]
C:
cd C:\pgsql\product\17\bin
.\initdb.exe -D "C:\pgsql\data\17" -E UTF8 --locale=Chinese_China.936 -U postgres -W

# 测试 开启服务
.\pg_ctl.exe -D "C:\pgsql\data\17" -l "C:\pgsql\log\17\postgres.log" start
# 测试 停止服务
.\pg_ctl.exe -D "C:\pgsql\data\17" -l "C:\pgsql\log\17\postgres.log" start
```

:::

::: tip 目录权限方面有点奇怪：

1. 我的操作系统登录账户是 `Administrator`；
2. `C:\pgsql\` 目录授权给用户组 `Administrators` 全部权限依然会报错；
3. `C:\pgsql\` 必须授权给用户 `Administrator` 全部权限才可以正常初始化。

:::

### 3. 注册 Windows 服务

Windows 版的 PgSQL 不支持 `Administrator` 账户作为服务所属账户，注册时需要指定服务所属系统账户。

::: details 这里以 postgres 系统账户为例(系统账户名可以随便取，不一定是 postgres)
![创建系统用户](/assets/iis/pgsql/3.jpg)
:::

::: code-group

```ps1 [17]
C:
cd C:\pgsql\product\17\bin
# Administrator 账户登录
.\pg_ctl.exe register -D "C:\pgsql\data\17" -N "pgsql-17" -U postgres -P "1" -S demand
# 非 Administrator 账户登录
.\pg_ctl.exe register -D "C:\pgsql\data\17" -N "pgsql-17" -S demand
```

```ps1 [服务启停]
# 启动
net start pgsql-17
# 停止
net stop pgsql-17
```

:::

::: details pgsql 17.4 版本通过 pg_ctl 注册时的选项说明

```ps1
pg_ctl register  [-D 数据目录] [-N 服务名称] [-U 用户名] [-P 口令] [-S 启动类型] [-e 源] [-W] [-t 秒数] [-s] [-o 选项]
```

::: code-group

````md [-D]
# -D (数据目录)

-   作用：指定 PostgreSQL 数据库集簇（pgdata）的路径（必填项）。
-   要求：必须为通过 initdb 初始化的有效数据目录。
-   示例：

    ```ps1
    -D "C:\pgsql\data\17"
    ```
````

````md [-N]
# -N (服务名称)

-   作用：指定要注册的 Windows 服务名称。
-   默认值： `PostgreSQL`
-   示例：

    ```ps1
    -N "PostgreSQL_17"
    ```
````

````md [-U]
# -U (运行账户)

-   作用：设置服务运行时的 Windows 用户账户。可以是：

    -   内置账户（如 NT AUTHORITY\NetworkService）；
    -   本地用户账户（如 .\postgres）；
    -   超级管理员账户无法启动。

-   注意：账户需有数据目录的读写权限。
-   示例：

    ```ps1
    # 无密码
    -U "postgres"
    # 有密码
    -U "postgres" -P "1"
    ```
````

````md [-P]
# -P (账户密码)

-   作用：为 `-U` 指定的用户账户提供密码。若使用内置账户（如 NETWORK SERVICE），可省略。
-   示例：

    ```ps1
    -P "1"
    ```
````

````md [-S]
# -S (启动类型)

-   作用：定义服务的启动类型，支持以下值：

    -   `auto`：系统启动时自动运行（默认）。
    -   `demand`：手动启动。
    -   `disabled`：禁用服务。

-   示例：

    ```ps1
    -S demand
    ```
````

````md [-e]
# -e (事件源名称)

-   作用：指定服务在 Windows 事件日志中的事件源名称。
-   默认：默认为 PostgreSQL。
-   示例：

    ```ps1
    -e "PG_17_Events"
    ```
````

```md [-W]
# -W (不等待)

-   作用：默认情况下，pg_ctl 会等待服务注册完成。使用 -W 可跳过等待直接返回。
-   适用场景：脚本中无需同步等待时使用。
```

````md [-t]
# -t (超时时间)

-   作用：设置等待服务操作完成的超时时间（秒）。超时后命令终止。
-   默认值：通常为 60 秒。
-   示例：

    ```ps1
    -t 120
    ```
````

```md [-s]
# -s (静默模式)

-   作用：禁止输出非错误信息，仅显示错误日志。
-   适用场景：自动化脚本中减少冗余输出。
```

````md [-o]
# -o (附加启动选项)

-   作用：向 postgres 主进程传递额外的命令行参数。
-   示例：

    ```ps1
    -o "-p 5433 -c shared_buffers=256MB"
    ```
````

:::

### 4. 配置文件案例

::: code-group
<<<@/assets/iis/pgsql/pg_hba.conf{ini} [pg_hba.conf]
<<<@/assets/iis/pgsql/postgresql.conf{ini} [postgresql.conf]
:::

### 5. 创建账号

默认创建了超级管理员用户 postgres，支持通过套接字和 `tcp/ip` 的方式登录，这里我们使用 postgres 账号来创建和设置

::: code-group

```ps1 [登录]
➜ ~  c:
➜ ~  cd C:\pgsql\product\17\bin
➜ bin  .\psql.exe -h C:\pgsql\temp\17 -U postgres
psql (17.4)
输入 "help" 来获取帮助信息.

postgres=#
```

```sql [创建用户]
CREATE USER admin SUPERUSER LOGIN PASSWORD '1';
```

```ps1 [测试登录]
➜ bin  .\psql.exe -h C:\pgsql\temp\17 -U admin -d postgres
psql (17.4)
输入 "help" 来获取帮助信息.

postgres=#
```

:::
