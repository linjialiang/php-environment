---
title: 安装 PostgreSQL 数据库管理系统
titleTemplate: 环境搭建教程
---

# 安装 PostgreSQL 数据库管理系统

PostgreSQL 号称最强开源关系型数据库管理系统，主要应用于企业级应用、金融系统、电商平台、物联网（IoT）和大数据等场景。

## 编译流程

::: code-group

```bash [进入编译目录]
su - postgres -s /bin/zsh

tar -xjf postgresql-18.1.tar.bz2
mkdir ~/postgresql-18.1/build_postgres
cd ~/postgresql-18.1/build_postgres
```

```bash [设置环境变量]
# 如果系统存在多个版本或多个类型的编译器时，就需要通过环境变量来指定编译器

# 使用 gcc 编译器
export CC=/usr/bin/gcc
export CXX=/usr/bin/g++
export LLVM_CONFIG=/usr/bin/llvm-config
export CLANG=/usr/bin/clang

# 使用 clang 编译器
# export CC=/usr/bin/clang
# export CXX=/usr/bin/clang++
# export LLVM_CONFIG=/usr/bin/llvm-config
```

```bash [构建编译]
# 清理 make
make distclean

# 构建选项
../configure --prefix=/server/postgres \
--enable-debug \
--enable-cassert \
--with-llvm \
--with-systemd \
--with-liburing \
--with-uuid=e2fs \
--with-lz4 \
--with-zstd \
--with-ssl=openssl > stdout.log

# 编译&安装
make -j4 > make.log
make check > makeTest.log
make install
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

```bash [清理]
# 编译安装完后记得移除源码包，节省空间
rm -rf ~/postgresql-18.1 ~/postgresql-18.1.tar.bz2
```

:::

::: details 编译选项说明

::: code-group

```md [启用选项]
| 启用选项         | note                                             |
| ---------------- | ------------------------------------------------ |
| --prefix=PREFIX  | 指定安装根路径                                   |
| --with-llvm      | 启用基于 LLVM 的 JIT 支持                        |
| --with-systemd   | 启用 systemd 支持                                |
| --with-liburing  | 启用 io_uring 支持，用于异步 I/O                 |
| --with-uuid=LIB  | 使用指定库构建 contrib/uuid-ossp (bsd,e2fs,ossp) |
| --with-lz4       | 启用 LZ4 支持 `极致压缩`                         |
| --with-zstd      | 启用 ZSTD 支持 `对比gzip：压缩比更高、速度更快`  |
| --with-ssl=LIB   | 指定用于 SSL/TLS 支持的库 (openssl)              |
| CC=CMD           | 指定 C 编译器( gcc/clang 注意是小写)             |
| CXX=CMD          | 指定 C++ 编译器( c++/clang++ 注意是小写)         |
| LLVM_CONFIG=PATH | 用于定位 LLVM 安装的程序                         |
```

```md [开发启用选项]
| 开发启用选项     | note                   |
| ---------------- | ---------------------- |
| --enable-debug   | 启用调试符号编译（-g） |
| --enable-cassert | 启用性能分析           |
```

```md [其他常见选项]
| 常见选项              | note                                               |
| --------------------- | -------------------------------------------------- |
| --exec-prefix=EPREFIX | 指定架构相关文件安装根路径                         |
| --datadir=DIR         | 指定数据目录路径                                   |
| --with-pgport=PortNum | 设置默认端口号 [5432]                              |
| --with-pam            | 启用 PAM 支持（允许 PAM 认证机制进行用户身份验证） |
| --without-readline    | 禁用后， psql 客户端将不支持补全功能               |
```

:::

## systemd 单元

::: code-group

<<< @/assets/linux/service/postgres.service{bash} [unit]

```bash [开机启动]
systemctl enable postgres
systemctl daemon-reload
```

:::

::: tip 注意
使用 `Type=notify` 需要在编译构建阶段 configure 时，使用 `--with-systemd` 选项
:::

## 配置

PostgreSQL 主要有以下几个配置文件：

1. `postgresql.conf`：这是主要的配置文件，用于设置数据库的各种参数和选项。它包含了许多可配置的参数，例如内存分配、连接数限制、日志记录等。通过编辑该文件，可以自定义数据库的行为。
2. `postgresql.auto.conf`： 该文件自动生成的，每当 postgresql.conf 被读取时，这个文件会被自动读取。 postgresql.auto.conf 中的设置会覆盖 postgresql.conf 中的设置。
3. `pg_hba.conf`：这个文件用于配置数据库的身份验证方式。它定义了不同类型连接（如本地连接、TCP/IP 连接）的认证方法。你可以根据需要设置不同的认证方式，例如信任所有用户、使用密码加密等。
4. `pg_ident.conf`：这个文件用于配置数据库的用户映射。它允许将外部系统（如操作系统用户）映射到数据库用户。通过配置该文件，可以实现对外部系统的用户进行身份验证和授权。

### 1. 配置示例

::: code-group
<<< @/assets/linux/etc/postgres/postgresql.conf.bash [服务端配置]
<<< @/assets/linux/etc/postgres/pg_hba.conf.bash [客户端身份验证配置]
<<< @/assets/linux/etc/postgres/pg_ident.conf.bash [用户名映射配置]
:::

#### 客户端身份验证

::: code-group

```md [连接类型]
| `TYPE`    | 常用的连接类型               |
| --------- | ---------------------------- |
| `local`   | Unix 域套接字连接            |
| `host`    | `TCP/IP 协议` 连接           |
| `hostssl` | `TCP/IP协议使用SSL加密` 连接 |
```

```md [数据库名]
| `DATABASE`    | 常用的数据库名称     |
| ------------- | -------------------- |
| `all`         | 匹配所有数据库       |
| `replication` | 匹配物理复制连接     |
| `db1`         | 匹配指定的数据库     |
| `db1,db2`     | 匹配指定的多个数据库 |
```

```md [数据库用户名]
| `USER`        | 常用的数据库用户名称     |
| ------------- | ------------------------ |
| `all`         | 匹配所有用户             |
| `user1`       | 匹配指定用户             |
| `user1,user2` | 匹配指定的多个用户       |
| `+role1`      | 匹配属于此角色的任何用户 |

> 用户就是允许登录的角色
```

```md [连接地址]
| `ADDRESS`           | 常用连接地址                       |
| ------------------- | ---------------------------------- |
| `all`               | 匹配任何地址                       |
| `samehost`          | 匹配服务器自己的任何 IP 地址       |
| `samenet`           | 匹配服务器任何网卡对应的局域网地址 |
| `127.0.0.1`         | 匹配本地回源地址                   |
| `192.168.66.256/32` | 匹配单个 IP 地址                   |
| `192.168.66.0/24`   | 匹配 IP 段对应的 IP 地址           |

> `local连接类型` 通过 `Unix域套接字连接` ，没有 `ADDRESS` 选项
```

```md [身份验证方法]
| `AUTH-METHOD`           | 常用身份验证方法                                     |
| ----------------------- | ---------------------------------------------------- |
| `trust`                 | 允许无条件连接                                       |
| `reject`                | 无条件拒绝连接                                       |
| `cert`                  | SSL 客户端证书进行身份验证（无密码）                 |
| `scram-sha-256`         | SCRAM-SHA-256 加密的密码进行身份验证（允许证书选项） |
| `md5(过时的加密)`       | MD5 加密的密码进行身份验证（允许证书选项）           |
| `password`              | 未加密的密码进行身份验证（允许证书选项）             |
| `peer`                  | 数据库用户名与系统登录用户相同的身份验证             |
| `ident(local连接类型)`  | 数据库用户与映射后的系统登录用户做对等身份验证       |
| `ident(TCP/IP连接类型)` | ~~数据库用户与客户端用户匹配的身份验证(不安全)~~     |
```

:::

#### psql 登录{#psql-login}

::: code-group

```bash [系统用户[postgres]]
# 最简单登录指令（套接字文件路径为默认时）
psql
# 修改 Unix域套接字文件路径后，需要指定 Unix域套接字文件所在目录才能正常登录
psql -h /run/postgres
```

```bash [系统用户[emad]]
# 需在 pg_ident.conf 配置文件设置用户映射
# ===========================================
# 最简单登录指令（套接字文件路径为默认时）
psql -U postgres
# 修改 Unix域套接字文件路径后，需要指定 Unix域套接字文件所在目录才能正常登录
psql -h /run/postgres -U postgres
# 用户同名的数据库不存在时，必须使用 -d 选项指定数据库名
psql -U admin -d postgres
psql -h /run/postgres -U admin -d postgres
```

```bash [TCP/IP协议]
# 本机客户端登录 admin
psql -h 127.0.0.1 -U admin -d postgres -W

# 使用 TLS 协议登录 admin
psql "host=192.168.66.254 \
dbname=postgres \
user=admin \
sslmode=require \
sslrootcert=./tls/root.crt \
sslcert=./tls/client-admin.crt \
sslkey=./tls/client-admin.key" \
-W

# 注意 sslmode=<require|verify-ca|verify-full> 这是客户端的认证模式
# 服务端的认证模式在 pg_hba.conf 文件里单独指定的
```

```md [指令说明]
> 使用 psql 与 PostgreSQL 服务建立连接的命令说明：

1.  `psql`：PostgreSQL 命令行工具；
2.  `-h /run/postgres`：连接到指定的 PostgreSQL 服务地址；
    -   如：主机名、IP 地址、Unix 域套接字文件
    -   案例：`/run/postgres`
3.  `-U admin`：指定建立连接的用户名；
    -   案例：`admin`
4.  `-d postgres`：指定建立连接的数据库名；
    -   案例：`postgres`
5.  `-W`：表示在连接时需要输入密码。
```

:::

### 2. 配置说明

#### 基本配置

```bash [基本]
# /server/pgData/postgresql.conf

listen_addresses = '127.0.0.1,192.168.66.254'
#port = 5432
external_pid_file = '/run/postgres/process.pid'
unix_socket_directories = '/run/postgres'
```

#### 启用 TLS

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
cd /server/etc/postgres/tls/
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
ssl_cert_file = '/server/etc/postgres/tls/server.crt'
ssl_key_file = '/server/etc/postgres/tls/server.key'
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
cd /server/etc/postgres/tls/

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

<<< @/assets/linux/script/postgres/gen-test-certs.sh [一键脚本]

```bash [吊销证书]
# 证书吊销比较复杂，放到后面再处理
```

```bash [配置]
# /server/pgData/postgresql.conf
ssl = on
ssl_ca_file = '/server/etc/postgres/tls/root.crt'
ssl_cert_file = '/server/etc/postgres/tls/server.crt'
#ssl_crl_file = ''
#ssl_crl_dir = ''
ssl_key_file = '/server/etc/postgres/tls/server.key'
#openssl>=3.0以后，安全性得到提升，通常不配置此项
# 如果需要更高的安全性或特定的兼容性要求，并且服务器资源允许，那么可以配置此项
#ssl_dh_params_file = '/server/etc/postgres/tls/pgsql.dh'
```

```bash [pg_hba说明]
# /server/pgData/pg_hba.conf

# hostssl 指 tcp/ip 一定是 ssl 传输的，这个跟客户端是否勾选 [使用ssl] 没有关系

# 仅支持ssl/all全部数据库/admin用户/允许连接的客户端IP段/密码使用scram-sha-256加密方式/服务器不验证客户端
# - 客户端不需要勾选 [使用ssl]
hostssl    all      admin            192.168.0.0/16          scram-sha-256

# 仅支持ssl/all全部数据库/admin用户/允许连接的客户端IP段/密码使用scram-sha-256加密方式/服务器验证客户端
# - 这是双向验证，客户端必须勾选 [使用ssl]，表示客户端也是ssl传输
# - 认证选项verify-ca：服务器将验证客户端的证书是否由一个受信任的证书颁发机构签署
hostssl    all      admin            192.168.0.0/16          scram-sha-256   clientcert=verify-ca

# 仅支持ssl/all全部数据库/admin用户/允许连接的客户端IP段/密码使用scram-sha-256加密方式/服务器验证客户端
# - 这是双向验证，客户端必须勾选 [使用ssl]，表示客户端也是ssl传输
# - 认证选项verify-full：服务器不仅验证证书链，还将检查用户名或其映射是否与所提供的证书的 cn（通用名称）相匹配
hostssl    all      admin            192.168.0.0/16          scram-sha-256   clientcert=verify-full
```

:::

::: warning TLS 备注说明

1. 在 PostgreSQL 中 `SSL` 指的就是 `TLS`

2. 签名创建证书的指南来源于 [`Postgres 官方文档`](https://www.postgresql.org/docs/current/ssl-tcp.html#SSL-CERTIFICATE-CREATION)

3. `一键脚本` 参考了 Redis 提供的生成工具，与 `2` 中的手工创建略有不同

4. 虽然自签名证书可用，但在实际生产中建议使用由证书颁发机构（CA）（通常是企业范围的根 CA）签名的证书。而双向验证也必须要 `CA根证书`

:::

#### 预写式日志

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

#### 复制

复制(REPLICATION)，这里只介绍基于 WAL 通信的流复制

#### 查询调优

后面实现

#### 日志配置

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
-- 创建允许复制的用户
CREATE USER repl_user WITH REPLICATION ENCRYPTED PASSWORD '1';
```

:::

## 客户端权限

见上述[[psql 登录说明]](#psql-login)

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

```bash
chown postgres:postgres -R /server/postgres /server/pgData /server/logs/postgres /server/etc/postgres
find /server/postgres /server/logs/postgres /server/etc/postgres -type f -exec chmod 640 {} \;
find /server/postgres /server/logs/postgres /server/etc/postgres -type d -exec chmod 750 {} \;
chmod 750 -R /server/postgres/bin
find /server/etc/postgres/tls /server/pgData -type f -exec chmod 600 {} \;
find /server/etc/postgres/tls /server/pgData -type d -exec chmod 700 {} \;
```
