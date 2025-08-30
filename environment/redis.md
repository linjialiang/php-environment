---
title: 安装 Redis
titleTemplate: 环境搭建教程
---

# 安装 Redis

[redis](https://redis.io/download) 是当下最热门的键值对(Key-Value)存储数据库

在 Debian 13 下构建 Redis 的详细流程如下

## 安装依赖

```bash
apt install -y g++ libssl-dev pkg-config
```

## 编译安装

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

::: details 基础配置
::: code-group
<<<@/assets/environment/source/redis/redis.conf{ini} [主配置]
<<<@/assets/environment/source/etc/redis/config/source.conf{ini} [原始配置]
:::

::: details 自定义配置
::: code-group
<<<@/assets/environment/source/etc/redis/config/custom/01-network.conf{ini} [01-network.conf]
<<<@/assets/environment/source/etc/redis/config/custom/02-tls.conf{ini} [02-tls.conf]
<<<@/assets/environment/source/etc/redis/config/custom/03-general.conf{ini} [03-general]
<<<@/assets/environment/source/etc/redis/config/custom/04-rdb.conf{ini} [04-rdb]
<<<@/assets/environment/source/etc/redis/config/custom/05-replication.conf{ini} [05-replication]
<<<@/assets/environment/source/etc/redis/config/custom/06-keys-tracking.conf{ini} [06-keys-tracking]
<<<@/assets/environment/source/etc/redis/config/custom/07-acl.conf{ini} [07-acl]
<<<@/assets/environment/source/etc/redis/config/custom/08-client.conf{ini} [08-client]
<<<@/assets/environment/source/etc/redis/config/custom/09-memory-management.conf{ini} [09-memory-management]
<<<@/assets/environment/source/etc/redis/config/custom/10-lazy-freeing.conf{ini} [10-lazy-freeing]
<<<@/assets/environment/source/etc/redis/config/custom/11-io.conf{ini} [11-io]
<<<@/assets/environment/source/etc/redis/config/custom/12-oom.conf{ini} [12-oom]
<<<@/assets/environment/source/etc/redis/config/custom/13-thp.conf{ini} [13-thp]
<<<@/assets/environment/source/etc/redis/config/custom/14-aof.conf{ini} [14-aof]
<<<@/assets/environment/source/etc/redis/config/custom/15-shutdown.conf{ini} [15-shutdown]
<<<@/assets/environment/source/etc/redis/config/custom/16-long-blocking.conf{ini} [16-long-blocking]
<<<@/assets/environment/source/etc/redis/config/custom/17-long-cluster.conf{ini} [17-long-cluster]
<<<@/assets/environment/source/etc/redis/config/custom/18-long-cluster-support.conf{ini} [18-long-cluster-support]
<<<@/assets/environment/source/etc/redis/config/custom/19-slow-log.conf{ini} [19-slow-log]
<<<@/assets/environment/source/etc/redis/config/custom/20-latency.conf{ini} [20-latency]
<<<@/assets/environment/source/etc/redis/config/custom/21-event-notification.conf{ini} [21-event-notification]
<<<@/assets/environment/source/etc/redis/config/custom/22-advanced-config.conf{ini} [22-advanced-config]
<<<@/assets/environment/source/etc/redis/config/custom/23-active-defragmentation.conf{ini} [23-active-defragmentation]
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
cp -r ~/redis-8.2.1/utils/tests/tls/ /server/etc/redis/
chmod 640 -R /server/etc/redis/tls
chown redis:redis -R /server/etc/redis/tls
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
