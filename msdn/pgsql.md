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
|   |  ├─ 17 --------------------- pgsql 17 数据
|   |  |  ├─ pg_hba.conf ----- 客户端配置
|   |  |  ├─ postgresql.conf - 服务端配置
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
|   ├─ log ------------------------------------------ 产品源码基目录
|   |  ├─ 17 --------------------- pgsql 17 日志
|   |  |  ├─ postgres.log -------- 操作日志
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

```ini [pg_hba.conf]
# TYPE  DATABASE        USER            ADDRESS                 METHOD

# "local" is for Unix domain socket connections only
local   all             all                                     trust
# IPv4 local connections:
#host    all             all             127.0.0.1/32            trust
# IPv6 local connections:
#host    all             all             ::1/128                 trust
# Allow replication connections from localhost, by a user with the
# replication privilege.
local   replication     all                                     trust
#host    replication     all             127.0.0.1/32            trust
#host    replication     all             ::1/128                 trust

# custom
# 局域网和外网需要使用 ssl认证+密码认证 建立连接
hostssl   all   admin,emad   192.168.0.0/16    scram-sha-256   clientcert=verify-full
# 本地仅需要通过 密码认证 建立连接
hostnossl all   admin,emad   127.0.0.1/32      scram-sha-256
```

```ini [postgresql.conf]
max_connections = 100
shared_buffers = 128MB
dynamic_shared_memory_type = windows
max_wal_size = 1GB
min_wal_size = 80MB
log_file_mode = 0640
log_timezone = 'Asia/Shanghai'
datestyle = 'iso, ymd'
timezone = 'Asia/Shanghai'
lc_messages = 'Chinese_China.936'
lc_monetary = 'Chinese_China.936'
lc_numeric = 'Chinese_China.936'
lc_time = 'Chinese_China.936'
default_text_search_config = 'pg_catalog.simple'

# custom
listen_addresses = '127.0.0.1,192.168.3.8'
port = 5432
external_pid_file = 'c:/pgsql/temp/17/process.pid'
unix_socket_directories = 'c:/pgsql/temp/17'

ssl = on
ssl_ca_file = 'c:/pgsql/tls/root.crt'
ssl_cert_file = 'c:/pgsql/tls/server.crt'
#ssl_crl_file = ''
#ssl_crl_dir = ''
ssl_key_file = 'c:/pgsql/tls/server.key'
#openssl>=3.0以后，安全性得到提升，通常不配置此项
# 如果需要更高的安全性或特定的兼容性要求，并且服务器资源允许，那么可以配置此项
#ssl_dh_params_file = 'c:/pgsql/tls/pgsql.dh'

# 启用复制至少是 replica
wal_level = replica
archive_mode = on
# 把 WAL 片段拷贝到目录 c:/pgsql/log/17/wal_archive/
archive_command = 'test ! -f c:/pgsql/log/17/wal_archive/%f && cp %p c:/pgsql/log/17/wal_archive/%f'

# 包括错误日志，访问日志等各种日志
log_destination = 'jsonlog'
logging_collector = on
log_directory = 'c:/pgsql/log/17'
# 应该对windows无效，保持默认的 0640
# log_file_mode = 0640
# 日志保留指定天数(推荐)
log_truncate_on_rotation = on       # on 轮换日志文件时，如文件存在，则覆盖内容
log_filename = '%d.log'    			# %a保留一周、%d保留[01,31]
log_rotation_age = 1d               # 每天轮换日志文件
log_rotation_size = 0               # 日志文件大小不限制
```

:::

### 5. 创建账号

默认创建了超级管理员用户 postgres，支持通过套接字和 `tcp/ip` 的方式登录，这里我们使用 postgres 账号来创建和设置

::: code-group

```ps1 [登录]
c:
cd C:\pgsql\product\17\bin
.\psql.exe -h C:\pgsql\temp\17 -U postgres
psql (17.4)
输入 "help" 来获取帮助信息.

postgres=#
```

```ps1 [创建用户]
CREATE USER admin SUPERUSER LOGIN PASSWORD '1';
```

:::
