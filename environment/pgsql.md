---
title: 编译安装 pgsql
titleTemplate: 环境搭建教程
---

# 编译安装 pgsql

学习 PostgreSQL 从编译安装开始，从官网下载指定版本的[[源码包]](https://www.postgresql.org/ftp/source/)

## 编译前准备

::: tip 必要说明
PostgreSql 官方推荐使用 GCC 最新版编译器来构建，但为了支持 JIT 这里用的是 `llvm+clang` 编译器套件；

GCC 编译器不支持 JIT 即时编译功能；

也就是说如果要增加 `--with-llvm` 编译选项，就需要使用 `llvm+clang` 编译器组合。

C 语言编译器主要有四种： `MSVC`/`GCC`/`MinGW`/`Clang+LLVM`
:::

```bash
apt install -y clang liblz4-dev libzstd-dev bison flex libreadline-dev \
zlib1g-dev libpam0g-dev libxslt1-dev uuid-dev libsystemd-dev libicu-dev
```

### 依赖包说明

| package         | note                                                                  |
| --------------- | --------------------------------------------------------------------- |
| clang           | c/c++ 编译器，`llvm+clang` 是套组合                                   |
| liblz4-dev      | 用于 LZ4 压缩算法的开发库                                             |
| libzstd-dev     | 用于 Zstandard 压缩算法的开发库                                       |
| bison           | 一个广泛使用的语法分析器生成器，主要用于 Unix 和类 Unix 系统          |
| flex            | 一个词法分析器生成工具，通常与 Bison 结合使用，以创建完整的编译器前端 |
| libreadline-dev | 提供命令行编辑功能的开发库                                            |
| zlib1g-dev      | 用于 zlib 压缩和解压缩数据的开发库                                    |
| libpam0g-dev    | 用于 PAM 支持的开发库                                                 |
| libxslt1-dev    | 包含用于开发 XSLT 应用程序的库和头文件                                |
| uuid-dev        | 包含了用于生成和处理 UUID 的库和头文件                                |
| libsystemd-dev  | 用于开发与 systemd 相关的应用程序的包，它提供了一组头文件和库文件     |
| libicu-dev      | icu 开发库包，支持国际化（i18n）和本地化（l10n）功能                  |

### 编译选项说明

| commom                | note                                                 |
| --------------------- | ---------------------------------------------------- |
| --prefix=PREFIX       | 指定安装路径                                         |
| --datadir=DIR         | 指定数据目录路径                                     |
| --enable-debug        | 启用调试模式                                         |
| --enable-cassert      | 启用断言检查                                         |
| CC=CMD                | 指定 C 编译器( gcc/clang 注意是区分大小写的)         |
| CXX=CMD               | 指定 C++ 编译器( c++/clang++ 注意是区分大小写的)     |
| --with-llvm           | 启用基于 LLVM 的 JIT 支持，优化适合 `OLTP/OLAP`      |
| LLVM_CONFIG=PATH      | 用于定位 LLVM 安装的程序                             |
| --with-pgport=PortNum | 指定 pgsql 服务器监听的端口号                        |
| --with-pam            | 允许 pgsql 使用系统的 PAM 认证机制进行用户身份验证   |
| --with-systemd        | 确保 PostgreSQL 与 systemd 服务和日志系统集成        |
| --with-uuid=e2fs      | 构建 uuid-ossp 使用 e2fsprogs 库，用于生成唯一标识符 |
| --with-libxml         | 支持 XML 数据类型                                    |
| --with-libxslt        | 支持 XSLT 转换，扩展 XML 处理能力                    |
| --with-lz4            | 启用 LZ4 压缩算法的支持                              |
| --with-zstd           | 启用 Zstandard 压缩算法的支持                        |
| --with-openssl        | 启用 OpenSSL 支持，用于加密通信                      |

-   `--enable-debug`：启用后，可以在调试器中运行程序来分析问题，这会大大增加已安装的可执行文件的大小，并且在非 GCC 编译器上它通常也会禁用编译器优化，生产环境中只建议在选择 GCC 编译器时添加此选项。
-   编译器：`llvm+clang` 跟 `gcc` 是两个编译器，是互斥的，如果要启用 `--with-llvm` 就要使用 `clang`
-   安装部分依赖包时，可能会自动安装 `gcc` 编译器包
-   `--with-ossp`: uuid 支持 3 种方式 `ossp-uuid(维护不积极)` `bsd(跨平台支持)` `e2fs(兼容linux，性能高)`

### 资源限制

修改操作系统打开最大文件句柄数，具体请参考[[系统级优化-资源管理]](./trait/kernel#resourc_management)

## 编译

::: code-group

```bash [进入编译目录]
su - postgres -s /bin/zsh
tar -xjf postgresql-17.6.tar.bz2
mkdir ~/postgresql-17.6/build_postgres
cd ~/postgresql-17.6/build_postgres
```

```bash [编译指令]
# 使用postgres账户编译
# 关于生产环境要不要添加 --enable-debug 选项问题：使用gcc编译器时可以启用debug
# 使用 llvm+clang 编译器套件时不应该启用debug，因为llvm可以优化pgsql性能，而使用 --enable-debug 选项，通常会禁用编译器的性能优化
# LLVM_CONFIG 的路径在不同发行版不同，如，debian12路径为：/usr/lib/llvm-14/bin/llvm-config
../configure --prefix=/server/postgres \
--enable-debug \
--enable-cassert \
CC=clang \
CXX=clang++ \
--with-llvm \
LLVM_CONFIG=/usr/lib/llvm-19/bin/llvm-config \
--with-pam \
--with-systemd \
--with-uuid=e2fs \
--with-lz4 \
--with-zstd \
--with-openssl \
--with-libxml \
--with-libxslt > stdout.log
```

```bash [安装指令]
# 使用postgres账户安装
make -j4 > make.log
make check > make.log
make install
# 编译安装完后记得移除源码包，节省空间
rm -rf ~/postgresql-17.6 ~/postgresql-17.6.tar.bz2
```

```bash [数据初始化]
su - postgres -s /bin/zsh
# 初始化 pgsql 数据库，-D 参数指定数据目录路径，
# 执行命令后将在指定目录下创建必要的文件和目录结构
/server/postgres/bin/initdb -D /server/pgData -E UTF8 --locale=zh_CN.utf8 -U postgres
```

```bash [测试]
# 这些指令通常不需要使用
# 启动 pgsql 数据库服务器，-D 参数指定数据目录路径，-l 参数指定了日志文件的路径，
# 执行命令后数据库服务器将开始运行，并记录日志到指定的文件中。
/server/postgres/bin/pg_ctl -D /server/pgData  -l logFile start
# 这个命令用于创建一个名为 "test" 的新数据库。
# 执行该命令后，将在数据库中创建一个名为 "test" 的新数据库。
/server/postgres/bin/createdb test
# 这个命令用于启动 PostgreSQL 命令行客户端，并连接到名为 "test" 的数据库。
# 执行该命令后，你将进入一个交互式的 PostgreSQL 命令行界面，可以执行 SQL 查询和操作。
/server/postgres/bin/psql test
```

:::

## psql

::: code-group

```bash [postgres用户登录]
# 最简单登录指令
psql
# 修改 sock 文件路径后，需要指定 sock 文件所在目录才能正常登录
psql -h /run/postgres
```

````md [admin用户登录]
```bash
# 注意：如果没有跟用户同名的数据库就必须使用 -d 指定数据库名
psql -h 127.0.0.1 -U admin -d postgres -W
psql -h 192.168.66.254 -U admin -d postgres -W
```

> 这是一个用于连接到 PostgreSQL 数据库的命令。其中：

-   psql 是 PostgreSQL 的命令行工具。
-   -h /run/postgres 指定了要连接的数据库服务器的主机名、 IP 地址或 socket 文件，这里是/run/postgres。
-   -U admin 指定了要使用的用户名，这里是 admin。
-   -d postgres 指定了要连接的数据库名称，这里是 postgres。
-   -W 表示在连接时需要输入密码。
````

:::

## systemd 单元

::: code-group

<<<@/assets/environment/source/service/postgres.service{bash} [unit]

```bash [开机启动]
systemctl enable postgres
systemctl daemon-reload
```

:::

::: tip 注意
使用 `Type=notify` 需要在编译构建阶段 configure 时，使用 `--with-systemd` 选项
:::

## 配置文件

PostgreSQL 主要有以下几个配置文件：

1. `postgresql.conf`：这是主要的配置文件，用于设置数据库的各种参数和选项。它包含了许多可配置的参数，例如内存分配、连接数限制、日志记录等。通过编辑该文件，可以自定义数据库的行为。
2. `postgresql.auto.conf`： 该文件自动生成的，每当 postgresql.conf 被读取时，这个文件会被自动读取。 postgresql.auto.conf 中的设置会覆盖 postgresql.conf 中的设置。
3. `pg_hba.conf`：这个文件用于配置数据库的身份验证方式。它定义了不同类型连接（如本地连接、TCP/IP 连接）的认证方法。你可以根据需要设置不同的认证方式，例如信任所有用户、使用密码加密等。
4. `pg_ident.conf`：这个文件用于配置数据库的用户映射。它允许将外部系统（如操作系统用户）映射到数据库用户。通过配置该文件，可以实现对外部系统的用户进行身份验证和授权。

::: details 配置案例

::: code-group
<<<@/assets/environment/source/postgres/postgresql.conf{ini} [服务端配置文件]
<<<@/assets/environment/source/postgres/pg_hba.conf{ini} [客户访问限制配置文件]
:::

### 1. 基本配置

```bash [基本]
# /server/pgData/postgresql.conf

listen_addresses = '127.0.0.1,192.168.66.254'
#port = 5432
external_pid_file = '/run/postgres/process.pid'
unix_socket_directories = '/run/postgres'
```

### 2. 启用 TLS

PostgreSQL 本身支持使用 ssl 连接来加密客户端/服务器通信，以提高安全性。这需要在客户端和服务器系统上都安装 OpenSSL，并且在 PostgreSQL 构建时启用 ssl 支持

SSL 登录验证通常分为 `单向验证` 和 `双向验证` 两种方式：

::: details 1. 单向验证（One-way SSL）

单向验证也被称为服务器端验证，指的是服务器要求客户端提供身份验证证书，以证明客户端的身份。

在单向验证中，服务器会向客户端发送自己的证书，但不会要求客户端提供证书。

这种方式可以确保客户端与正确的服务器进行通信，但无法验证客户端的身份。

单向验证需要证书: `服务器私钥+服务器自签名证书`

::: code-group

```bash [自签名证书]
su postgres -s /bin/zsh
mkdir /server/postgres/tls
cd /server/postgres/tls/
# 要为服务器创建一个简单的自签名证书，有效期为365天，
# - 请使用以下OpenSSL命令，将 [debian12-lnpp] 替换为服务器的主机名：
openssl req -new -x509 -days 365 -nodes -text -out server.crt \
-keyout server.key -subj "/CN=debian12-lnpp"
# 注意：确保私钥仅属主用户有权限，否则服务器会拒绝
chmod 600 server.key
```

```bash [配置]
# /server/pgData/postgresql.conf
ssl = on
ssl_cert_file = '/server/postgres/tls/server.crt'
ssl_key_file = '/server/postgres/tls/server.key'
```

:::

::: details 2. 双向验证（Two-way SSL）

双向验证也被称为相互验证，指的是服务器和客户端都需要提供身份验证证书。

在双向验证中，服务器会向客户端发送自己的证书，并要求客户端提供证书。

客户端收到服务器的证书后，会对其进行验证，确认服务器的身份。

然后，客户端会向服务器提供自己的证书，服务器也会对其进行验证，确认客户端的身份。

这种方式可以确保客户端与正确的服务器进行通信，并且可以验证客户端的身份。

双向验证需要证书: `ca根证书`/`服务器私钥+服务器部署证书`/`客户端私钥+客户端部署证书`

::: code-group

```bash [CA根证书]
su postgres -s /bin/zsh
mkdir /server/postgres/tls
cd /server/postgres/tls/

# 1. 首先创建 [颁发机构CA根证书]
# - 1.1 创建证书签名请求（CSR）和证书私钥文件：
openssl req -new -nodes -text -out root.csr \
-keyout root.key -subj "/CN=Certificate Authority/O=PostgreSQL"
chmod 600 root.key  # 注意：确保私钥仅属主用户有权限，否则服务器会拒绝
# - 1.2 使用 [证书签名请求+证书私钥+openssl配置] 创建 [颁发机构CA根证书]
#   - 使用 openssl version -d 指令可以获取 openssl.cnf 路径
openssl x509 -req -in root.csr -text -days 3650 \
-extfile /etc/ssl/openssl.cnf -extensions v3_ca \
-signkey root.key -out root.crt
chmod 600 root.* # 安全起见，全部设为仅属主可见
```

```bash [服务器证书]
# 2. 创建 [服务器部署证书]
# - 2.1 创建服务器签名请求（CSR）和服务器私钥文件
# - 如果客户端使用了 【verify-full】 SSL模式，则CN对应的值必须是客户端连接数据库时的[服务器主机ip]
#       如，服务器IP或服务器回环地址: 127.0.0.1 或 192.168.66.254 或 localhost 等
openssl req -new -nodes -text -out server.csr \
-keyout server.key -subj "/CN=192.168.66.254/O=PostgreSQL"
chmod 600 server.key  # 注意：确保私钥仅属主用户有权限，否则服务器会拒绝
# - 2.2 使用 [CA根证书+证书私钥+服务器私钥] 创建 [服务器部署证书]
openssl x509 -req -in server.csr -text -days 365 \
-CA root.crt -CAkey root.key -CAcreateserial \
-out server.crt
chmod 600 server.* # 安全起见，全部设为仅属主可见
```

```bash [客户端证书]
# 3. 创建 [客户端证书]
# - 3.1 创建客户端签名请求（CSR）和客户端私钥文件
# - 如果对应的hostssl行没有设置认证选项，则客户端只需要开启SSL，客户端是否认证由客户端自己控制
# - 如果对应的hostssl行加入了认证选项【clientcert={verify-ca|verify-full}】，则客户端需要开启SSL，并使用正确的客户端验证
# - 如果对应的hostssl行加入了 【clientcert=verify-full】 认证选项，则CN对应的值必须是数据库登录用户名
openssl req -new -nodes -text -out client-emad.csr \
-keyout client-emad.key -subj "/CN=emad/O=PostgreSQL"
# - 3.2 使用 [CA根证书+证书私钥+客户端私钥] 创建 [客户端证书]
openssl x509 -req -in client-emad.csr -text -days 365 \
-CA root.crt -CAkey root.key -CAcreateserial \
-out client-emad.crt

# - admin 用户 私钥+签名请求
openssl req -new -nodes -text -out client-admin.csr \
-keyout client-admin.key -subj "/CN=admin/O=PostgreSQL"
# - admin 用户 部署证书
openssl x509 -req -in client-admin.csr -text -days 365 \
-CA root.crt -CAkey root.key -CAcreateserial \
-out client-admin.crt

chmod 600 client-*  # 客户端证书是提供给特定客户的，安全起见，全部设为仅属主可见
```

<<<@/assets/environment/source/postgres/gen-test-certs.sh [一键脚本]

```bash [吊销证书]
# 证书吊销比较复杂，放到后面再处理
```

```bash [配置]
# /server/pgData/postgresql.conf
ssl = on
ssl_ca_file = '/server/postgres/tls/root.crt'
ssl_cert_file = '/server/postgres/tls/server.crt'
#ssl_crl_file = ''
#ssl_crl_dir = ''
ssl_key_file = '/server/postgres/tls/server.key'
#openssl>=3.0以后，安全性得到提升，通常不配置此项
# 如果需要更高的安全性或特定的兼容性要求，并且服务器资源允许，那么可以配置此项
#ssl_dh_params_file = '/server/postgres/tls/pgsql.dh'
```

```bash [pg_hba说明]
# /server/pgData/pg_hba.conf

# hostssl 指 tcp/ip 一定是 ssl 传输的，这个跟客户端是否勾选 [使用ssl] 没有关系

# 仅支持ssl/all全部数据库/emad用户/允许连接的客户端IP段/密码使用scram-sha-256加密方式/服务器不验证客户端
# - 客户端不需要勾选 [使用ssl]
hostssl    all      emad            192.168.0.0/16          scram-sha-256

# 仅支持ssl/all全部数据库/emad用户/允许连接的客户端IP段/密码使用scram-sha-256加密方式/服务器验证客户端
# - 这是双向验证，客户端必须勾选 [使用ssl]，表示客户端也是ssl传输
# - 认证选项verify-ca：服务器将验证客户端的证书是否由一个受信任的证书颁发机构签署
hostssl    all      emad            192.168.0.0/16          scram-sha-256   clientcert=verify-ca

# 仅支持ssl/all全部数据库/emad用户/允许连接的客户端IP段/密码使用scram-sha-256加密方式/服务器验证客户端
# - 这是双向验证，客户端必须勾选 [使用ssl]，表示客户端也是ssl传输
# - 认证选项verify-full：服务器不仅验证证书链，还将检查用户名或其映射是否与所提供的证书的 cn（通用名称）相匹配
hostssl    all      emad            192.168.0.0/16          scram-sha-256   clientcert=verify-full
```

:::

::: warning TLS 备注说明

1. 在 PostgreSQL 中 `SSL` 指的就是 `TLS`

2. 签名创建证书的指南来源于 [`Postgres 官方文档`](https://www.postgresql.org/docs/current/ssl-tcp.html#SSL-CERTIFICATE-CREATION)

3. `一键脚本` 参考了 Redis 提供的生成工具，与 `2` 中的手工创建略有不同

4. 虽然自签名证书可用，但在实际生产中建议使用由证书颁发机构（CA）（通常是企业范围的根 CA）签名的证书。而双向验证也必须要 `CA根证书`

:::

### 3. 预写式日志

预写式日志(WAL) 即 Write-Ahead Logging，是一种实现事务日志的标准方法。

```bash [配置]
# /server/pgData/postgresql.conf

# 启用复制至少是 replica
wal_level = replica
archive_mode = on
# 把 WAL 片段拷贝到目录 /server/logs/postgres/wal_archive/
archive_command = 'test ! -f /server/logs/postgres/wal_archive/%f && cp %p /server/logs/postgres/wal_archive/%f'
```

::: tip 注意：wal_archive 目录需要预先创建并授予 postgres 用户权限

```bash
mkdir /server/logs/postgres/wal_archive
chown postgres /server/logs/postgres/wal_archive/
```

:::

### 4. 复制

复制(REPLICATION)，这里只介绍基于 WAL 通信的流复制

### 5. 查询调优

后面实现

### 6. 日志配置

::: code-group

```bash [日志基本]
# /server/pgData/postgresql.conf

# 包括错误日志，访问日志等各种日志
log_destination = 'jsonlog'
logging_collector = on
log_directory = '/server/logs/postgres'
log_file_mode = 0640
```

```bash [方案一]
# /server/pgData/postgresql.conf

# 方案一：日志保留指定天数(推荐)
log_truncate_on_rotation = on       # on 轮换日志文件时，如文件存在，则覆盖内容
log_filename = 'postgres-%d.log'    # %a保留一周、%d保留[01,31]
log_rotation_age = 1d               # 每天轮换日志文件
log_rotation_size = 0               # 日志文件大小不限制
```

```bash [方案二]
# /server/pgData/postgresql.conf

# 方案二：日志按天来
log_truncate_on_rotation = off      # off 轮换日志文件时，如文件存在，则追加内容
log_filename = 'postgres-%Y-%m-%d_%H%M%S.log'
log_rotation_age = 1d
log_rotation_size = 0
```

```bash [方案三]
# /server/pgData/postgresql.conf

# 方案二：日志按大小来
log_truncate_on_rotation = off
log_filename = 'postgres-%Y-%m-%d_%H%M%S.log'
log_rotation_age = 0
log_rotation_size = 10M
```

:::

## 数据库角色

PostgreSQL 使用角色的概念管理数据库访问权限。一个角色可以被看成是一个数据库用户或者是一个数据库用户组，这取决于角色被怎样设置。

角色可以拥有数据库对象（例如，表和函数）并且能够把那些对象上的权限赋予给其他角色来控制谁能访问哪些对象。此外，还可以把一个角色中的成员资格授予给另一个角色，这样允许成员角色使用被赋予给另一个角色的权限。

角色的概念把 `用户` 和 `组` 的概念都包括在内。在 PostgreSQL 版本 8.1 之前，用户和组是完全不同的两种实体，但是现在只有角色。任意角色都可以扮演用户、组或者两者。

::: code-group

```sql [角色]
-- 查看角色列表 (psql可以使用 \du 列出现有角色)
SELECT rolname FROM pg_roles;
-- 创建角色（不允许登录），角色不能批量创建
CREATE ROLE name;
-- 移除角色，2种方式等效，移除多个角色用逗号分隔
DROP ROLE name1,name2;
DROP USER name1,name2;
```

```sql [角色属性]
-- CREATE 和 ALTER 指令的 [WITH] 是可选的，你也可以省略
-- 登录特权属性，创建允许登录的角色，2种方式等效
CREATE ROLE user_a WITH LOGIN;
CREATE USER user_b;
-- 设置 password 属性，仅pg_hba.conf对应行需要口令验证时才有意义
CREATE ROLE user_c WITH LOGIN PASSWORD '1'
-- 超级用户特权属性，绕开除登录特权外的所有权限检查
CREATE ROLE role_a WITH SUPERUSER;
-- 创建数据库特权属性
CREATE ROLE role_b WITH CREATEDB;
-- 授予角色特权属性
CREATE ROLE role_c WITH CREATEROLE;
-- 同时赋予多个属性
CREATE ROLE role_admin WITH CREATEDB CREATEROLE;

-- user_A修改密码
ALTER USER user_a WITH PASSWORD '1';
-- 授予user_A超级用户特权属性
ALTER USER user_a WITH SUPERUSER;
```

```sql [角色继承]
-- 组角色增加成员，多个成员以逗号,隔开
GRANT role_admin TO user_a,user_b,user_c;
-- 组角色移除成员，多个成员以逗号,隔开
REVOKE role_admin FROM user_a,user_b;

-- 成员角色默认继承除特殊权限属性{LOGIN|SUPERUSER|CREATEDB|CREATEROLE}外普通权限
--  如果成员角色带有 NOINHERIT 属性，则不会继承组角色的任何权限
--  使用 set role group_name 后临时获取组角色全部权限，但成员角色自身权限不存在

-- 案例，user_c 继承 role_c; role_c 继承 role_b
GRANT role_b TO role_c;
GRANT role_c TO user_c;
-- 登录 role_c，由于 role_b/role_c 只有特权属性，所以 user_c 暂时没有任何权限
-- 下面这步骤后，该回话权限将变成 role_c 的权限，user_c 自身权限已经无关
SET ROLE role_c;
-- 下面这步骤后，该回话权限将变成 role_b 的权限
SET ROLE role_b;
-- 下面这步骤后，权限不变，因为 user_c 并没有 role_a 的继承权
SET ROLE role_a;
-- 下面这步骤后，权限变成 user_c 的权限
SET ROLE user_c;
```

```sql [案例]
-- 创建超级管理员 admin
CREATE USER admin WITH SUPERUSER PASSWORD '1';
```

:::

## 客户端权限

::: code-group

```bash [禁用客户端连接规则]
# pg_hba.conf
# 除了 local 外，其它类型的【无条件地允许客户端连接(trust)】都应该禁用
# "local" is for Unix domain socket connections only
local   all             all                                     trust
# 通过TCP/IP连接的本地客户端，无条件建立连接【需要禁用】
# host    all             all             127.0.0.1/32            trust
# host    all             all             ::1/128                 trust
# Allow replication connections from localhost, by a user with the
# replication privilege.
local   replication     all
# 对于replication权限的用户，通过TCP/IP无条件建立连接 【需要禁用】
# host    replication     all             127.0.0.1/32            trust
# host    replication     all             ::1/128                 trust

# 自定义
# 局域网和外网需要使用 ssl认证+密码认证 建立连接
hostssl   all   admin,qyphp   192.168.0.0/16    scram-sha-256   clientcert=verify-full
# 本地仅需要通过 密码认证 建立连接
hostnossl all   admin,qyphp   127.0.0.1/32      scram-sha-256
```

```bash [psql]
# pg_hba.conf
hostssl     all      admin          192.168.0.0/16          scram-sha-256   clientcert=verify-full
hostssl     all      user_c         192.168.0.0/16          scram-sha-256   clientcert=verify-ca
hostnossl   all      user_a         192.168.0.0/16          scram-sha-256

# psql 登录 emad
psql "dbname=postgres user=admin host=192.168.66.254 sslmode=verify-full sslrootcert=/server/postgres/tls/root.crt sslcert=/server/postgres/tls/client-admin.crt sslkey=/server/postgres/tls/client-admin.key"
# psql 登录 user_c
psql "dbname=postgres user=user_c host=192.168.66.254 sslmode=verify-ca sslrootcert=/server/postgres/tls/root.crt sslcert=/server/postgres/tls/client-emad.crt sslkey=/server/postgres/tls/client-emad.key"
# psql 登录 user_a
psql "dbname=postgres user=user_a host=192.168.66.254"
```

:::

## 升级

升级和安装是一样的，在执行 `make install` 前，请在空闲时段关闭 postgres 单元服务，这样尽可能保证数据不会出错：

```bash
systemctl stop postgres.service
make install
systemctl start postgres.service
```

::: danger 升级警告
如果当前版本没有发现漏洞，线上环境不要对数据库进行升级，如果确实需要升级，就一定要做好快照备份
:::

## 权限

::: code-group

```bash [部署]
chown postgres:postgres -R /server/postgres /server/pgData /server/logs/postgres /server/etc/postgres
find /server/postgres /server/pgData /server/logs/postgres /server/etc/postgres -type f -exec chmod 640 {} \;
find /server/postgres /server/pgData /server/logs/postgres /server/etc/postgres -type d -exec chmod 750 {} \;
# bin目录二进制文件需执行权限
chmod 750 -R /server/postgres/bin
# postgres 要求证书最高权限是 600
find /server/etc/postgres/tls -type f -exec chmod 600 {} \;
find /server/etc/postgres/tls -type d -exec chmod 700 {} \;
```

```bash [开发]
# 权限同部署环境
# 开发用户 emad 加入 lnpp包用户组
usermod -a -G sqlite,redis,postgres,mysql,php-fpm,nginx emad
```

:::
