---
title: 安装 Redis
titleTemplate: 环境搭建教程
---

# 安装 Redis

[redis](https://redis.io/download) 是当下最热门的键值对(Key-Value)存储数据库

在 Debian 13 下构建 Redis 的详细流程如下

### 安装依赖

```bash
apt install -y g++ libssl-dev pkg-config
```

### 编译安装

::: code-group

```bash [构建指令]
su - redis -s /bin/zsh
# make 时需要通过 ../deps/** 来获取自带的依赖依赖
# - 所以必须在子目录中构建
mkdir ~/redis-8.2.1/build_redis
cd ~/redis-8.2.1/build_redis
make -C ../ BUILD_TLS=yes -j4
```

```bash [检测编译结果]
make -C ../ test
# 当出现高亮信息 `\o/ All tests passed without errors!` 证明测试通过
# -- 低配虚拟机通常会因为AOF持久化的最大延迟（max_latency）> 40 发出异常报错
*** [err]: Active defrag - AOF loading in tests/unit/memefficiency.tcl
Expected 46 <= 40 (context: type eval line 37 cmd {assert {$max_latency <= 40}} proc ::test)
```

```bash [安装并指定目录]
# 需要回到源码根目录执行安装
cd ~/redis-8.2.1/
make install PREFIX=/server/redis
```

```text [目录结构]
====================================================
Redis 初始目录结构
====================================================
├─ /server/redis                        redis基目录
|   ├─ bin
|   |  ├─ redis-benchmark               Redis 压力测试工具
|   |  ├─ redis-cli                     Redis 客户端
|   |  ├─ redis-server                  Redis 服务器
|   |  ├─ redis-check-aof               redis-server软链接
|   |  ├─ redis-check-rdb               redis-server软链接
|   |  ├─ redis-sentinel                redis-server软链接
|   |  └─ ...
|   |
└─  └─  rdbData                         手动创建，快照和本地持久化文件存放路径指向此目录

```

:::

## 配置文件

redis 源码包中自带了参考配置文件，可以备份该参考配置，按需增减配置，最后清除不必要的注释行

### 1. 配置参考

::: code-group

```bash [备份默认配置]
cp -p -r ~/redis-8.2.1/redis.conf /server/redis/redis.conf.source
```

```bash [RDB存储目录]
# 可忽略，mkdir.bash 脚本已处理
mkdir /server/redis/rdbData
chown redis:redis /server/redis/rdbData
chmod 750 /server/redis/rdbData
```

:::

#### 配置案例

::: details 配置案例
::: code-group
<<<@/assets/environment/source/redis/redis.conf{ini} [配置案例]
<<<@/assets/environment/source/redis/redis.conf.source{ini} [自带配置]
:::

### 2. 配置说明

::: code-group

```bash [改动的配置说明]
# 以守护进程的方式运行
daemonize yes
# 修改pid文件存放路径
pidfile /run/redis/process.pid
# 启用密码登陆
requirepass 1
# 日志路径
logfile "/server/logs/redis/redis.log"
# 开启本地和局域网
# 提示：监听的网卡ip地址必须全部正常，否则网卡都无法正常连接
#   - 设为 0.0.0.0 则监听服务器的全部网卡
bind 127.0.0.1 192.168.66.254
# 启用 RDB 持久化，后面的3组数字表示自动快照策略
# - 3600秒（60分钟）内有至少1个key发生变化；
# - 300秒（5分钟）内有至少100个key发生变化；
# - 60秒（1分钟）内有至少1万个key发生变化；
save 3600 1 300 100 60 10000 # 如果设为 save "" 代表关闭
# 指定工作目录
# - 配置文件redis.conf上所有相对路径的父级目录，如：
#   1. RDB文件的存储目录
#   2. AOF文件的存储目录
#   3. 使用了相对路径的pid文件
#   4. 使用了相对路径的日志文件
#   5. 使用了相对路径的tls相关文件
#   6. 访问控制列表
dir /server/redis/rdbData

# 是否开启AOF持久化。
# - 默认为no，表示关闭AOF持久化。
# - 如果设置为yes，则开启AOF持久化。
appendonly yes

# 启用TLS，证书需自签名或购买
tls-port 16379
tls-cert-file /server/redis/tls/redis.crt
tls-key-file /server/redis/tls/redis.key
tls-ca-cert-file /server/redis/tls/ca.crt
tls-auth-clients optional
# 旧版本OpenSSL（<3.0）需要此配置。新版本不需要此配置并建议不使用DH参数文件
# tls-dh-params-file /server/redis/tls/redis.dh
```

```bash [RDB配置说明]
# RDB是快照(Snapshotting)全量备份，只能恢复最近1次的快照

# 启用 RDB 持久化，后面的3组数字表示自动快照策略
# - 3600秒（60分钟）内有至少1个key发生变化；
# - 300秒（5分钟）内有至少100个key发生变化；
# - 60秒（1分钟）内有至少1万个key发生变化；
save 3600 1 300 100 60 10000 # 如果设为 save "" 代表关闭
# 当后台保存出错时，是否停止写入操作。
# - 默认为yes，表示如果后台保存失败，那么Redis将停止接收写请求。
stop-writes-on-bgsave-error yes
# 是否开启RDB文件压缩。
# - 默认为yes，表示Redis会在保存RDB文件时进行压缩，以节省磁盘空间。
rdbcompression yes
# 是否开启RDB文件校验和。
# - 默认为yes，表示Redis会在保存RDB文件时计算校验和，用于检查RDB文件是否损坏。
rdbchecksum yes
# 指定RDB文件的名称。默认为 dump.rdb
dbfilename dump.rdb
# 是否删除旧的RDB文件。默认为no，表示Redis不会删除旧的RDB文件。
rdb-del-sync-files no
# 指定RDB文件的存储目录
# - 注意：AOF文件，也将在此目录中创建
dir /server/redis/rdbData
```

```bash [AOF配置说明]
# AOF 全称 “APPEND ONLY MODE” 是实时记录操作，默认配置可以恢复1秒前的数据
# -- AOF还可以通过混合持久化的方式，结合RDB的快照来提高启动效率和数据恢复的速度。

# 是否开启AOF持久化。
# - 默认为no，表示关闭AOF持久化。
# - 如果设置为yes，则开启AOF持久化。
appendonly yes
# 指定AOF文件的名称。默认为 appendonly.aof
appendfilename "appendonly.aof"
# 指定AOF文件的存储目录。默认为当前工作目录
appenddirname "appendonlydir"
# 设置AOF文件同步的频率。
# - 默认为 everysec，表示每秒同步一次。
# - 可选值有 everysec/always/no
appendfsync everysec
# 在AOF重写期间是否进行同步。
# - 默认为no，表示在AOF重写期间不进行同步。
# - 如果设置为yes，则在AOF重写期间进行同步。
no-appendfsync-on-rewrite no
# 设置自动触发AOF重写的条件，即当AOF文件大小超过上一次重写后大小的百分之多少时触发。
# - 默认为100，表示每次重写都会触发。
auto-aof-rewrite-percentage 100
# 设置自动触发AOF重写的最小文件大小。默认为 64Mb
auto-aof-rewrite-min-size 64mb
# 当AOF文件被截断时，是否加载截断后的AOF文件。
# - 默认为yes，表示加载截断后的AOF文件。
aof-load-truncated yes
# 是否在AOF文件中添加RDB格式的前导部分。
# - 默认为yes，表示添加前导部分。
aof-use-rdb-preamble yes # yes即开启混合持久化 整体格式为：[RDB file][AOF tail]
# 是否在AOF文件中添加时间戳。
# - 默认为no，表示不添加时间戳。
aof-timestamp-enabled no
```

:::

::: tip 混合持久化
Redis 的持久化是它的一大特性，可以将内存中的数据写入到硬盘中；

Redis 分为 RDB 和 AOF 两种持久化，其中 `AOF` 可以结合 `RDB` 实现混合持久化。
:::

## 配置 redis 系统单元

推荐统一使用 systemd 管理各种服务

::: code-group

<<<@/assets/environment/source/service/redis.service{ini} [系统单元配置]

```bash [重载配置]
# 重新载入 Systemd 配置
systemctl daemon-reload
# redis.service 加入开机启动
systemctl enable redis
```

```md [管理单元]
| common                          | info         |
| ------------------------------- | ------------ |
| systemctl start redis.service   | 立即激活单元 |
| systemctl stop redis.service    | 立即停止单元 |
| systemctl restart redis.service | 重新启动     |
```

```bash [查看启动状态]
ps -ef|grep -E "redis|PID" |grep -v grep
ps aux|grep -E "redis|PID" |grep -v grep
```

:::

## 启用 TLS 功能

Redis 支持通过 SSL/TLS 协议进行加密通信，可以提供更高的安全性。要启动 Redis 的 SSL 功能，需要按照以下步骤进行配置：

### 1. 生成 TLS 证书和密钥

生成 TLS 证书和密钥涉及到多个步骤，包括创建私钥、生成证书签名请求（CSR）、签署证书以及分发证书。

::: details 源码包自带生成工具
redis 源码包上的 `./utils/gen-test-certs.sh` 脚本，用于一键生成 TLS 相关证书和密钥：

::: code-group
<<<@/assets/environment/source/redis/gen-test-certs.sh [脚本]

```bash [执行脚本]
su - redis -s /bin/zsh
cd ~/redis-8.2.1/utils
chmod +x ./gen-test-certs.sh
./gen-test-certs.sh
mkdir /server/etc/redis/tls
chmod 640 /server/etc/redis/tls
chown redis:redis /server/etc/redis/tls
cp -r ~/redis-8.2.1/utils/tests/tls /server/etc/redis/tls
```

```bash [生成文件]
#   tests/tls/ca.{crt,key}          自签名CA证书和私钥
#   tests/tls/redis.{crt,key}       没有限制的证书和私钥
#   tests/tls/client.{crt,key}      限制为SSL客户端使用的证书和私钥
#   tests/tls/server.{crt,key}      限制为SSL服务器使用的证书和私钥
#   tests/tls/redis.dh              DH参数文件，新版openssl已经不建议使用
```

:::

::: tip 文件权限
gen-test-certs.sh 脚本生成的文件，只需要 400 即可；

开发环境：为方便修改以及 emad 用户通过 redis-cli 登录，使用 640 权限。
:::

### 2. 配置 Redis 服务器

::: code-group

```bash [非tls/tls同时开启]
# /server/redis/redis.conf
# 这里需要注意 非tls和tls端口不能重复
port 6379
tls-port 16379
tls-cert-file /server/redis/tls/redis.crt
tls-key-file /server/redis/tls/redis.key
tls-ca-cert-file /server/redis/tls/ca.crt
tls-auth-clients no
```

```bash [禁用双向认证]
# /server/redis/redis.conf
port 0  # port 设为 0 禁用非 tls 连接
tls-port 6379
tls-cert-file /server/redis/tls/redis.crt
tls-key-file /server/redis/tls/redis.key
tls-ca-cert-file /server/redis/tls/ca.crt
tls-auth-clients no
```

```bash [启用双向认证]
# /server/redis/redis.conf
port 0
tls-port 6379
tls-cert-file /server/redis/tls/server.crt
tls-key-file /server/redis/tls/server.key
tls-ca-cert-file /server/redis/tls/ca.crt
tls-client-cert-file /server/redis/tls/client.crt
tls-client-key-file /server/redis/tls/client.key
tls-auth-clients optional
# 旧版本OpenSSL（<3.0）需要此配置。新版本不需要此配置并建议不使用DH参数文件
# tls-dh-params-file /server/redis/tls/redis.dh
```

````bash [redis-cli登录]
# 双向认证 - 远程需要指定ip
redis-cli -h 192.168.66.254 -p 16379 --tls \
--cacert /server/redis/tls/ca.crt \
--cert /server/redis/tls/client.crt \
--key /server/redis/tls/client.key

# 客户端不做验证 - 本机可以省略ip
redis-cli -p 16379 --tls --cacert /server/redis/tls/ca.crt
```
:::

::: details tls-auth-clients 选项

1. 缺省选项：`Redis服务端` 必须验证 `Redis客户端` 是否正确；
2. 选项：`optional`
    - `Redis客户端` 有传 `密钥/证书`，`Redis服务端` 验证 `Redis客户端` 是否正确；
    - `Redis客户端` 没传 `密钥/证书`，`Redis服务端` 不验证 `Redis客户端`；
3.  选项：no
    - `Redis客户端` 不论是否有传 `密钥/证书`，一律不验证 `Redis客户端` 。

:::

### 3. 分发证书

将 CA 证书（ca.crt）分发给所有客户端，以便它们能够验证服务器的身份。

如果使用了客户端证书认证，还需要将客户端证书（client.crt）分发给客户端，并将 CA 证书分发给服务器，以便服务器能够验证客户端的身份。

## 开启 ACL(访问控制列表)

配置文件里 `aclfile /path/to/users.acl` 行取消注释后，则开启

## 附录：配置文件说明

<!-- @include: ./trait/redis_ext_param.md -->

## 权限

::: code-group

```bash [部署]
chown redis:redis -R /server/redis /server/logs/redis
find /server/redis /server/logs/redis -type f -exec chmod 640 {} \;
find /server/redis /server/logs/redis -type d -exec chmod 750 {} \;
chmod 750 -R /server/redis/bin
````

```bash [开发]
# 权限同部署环境
# 开发用户 emad 加入 lnpp包用户组
usermod -G sqlite,redis,postgres,mysql,php-fpm,nginx emad
```

:::
