---
title: 安装 Redis
titleTemplate: 环境搭建教程
---

<script setup>
import { computed } from 'vue'

const logrotateLink = computed(() => {
  return __SITE_CONFIG__.tutorialApiUrl + 'debian/bash/logrotate.html'
})

const redisTlsLink = computed(() => {
  return __SITE_CONFIG__.tutorialApiUrl + 'nosql/redis/redis-conf.html#tls'
})
</script>

# 安装 Redis

Redis 是当下最热门的键值对(Key-Value)存储数据库

::: code-group

```bash [构建指令]
su - redis -s /bin/zsh

tar -xzf redis-8.4.0.tar.gz
cd ~/redis-8.4.0

make distclean
make USE_SYSTEMD=yes BUILD_TLS=yes -j4 > make.log
make test > makeTest.log
# 当出现高亮信息 `\o/ All tests passed without errors!` 证明测试通过

make install PREFIX=/server/redis
```

:::

::: tip 提示

-   Redis 编译内置依赖库时会用到 `C++`，如果没有安装会导致各种出错

-   低配虚拟机通常会因为 AOF 持久化的最大延迟（max_latency）> 40 发出异常报错

    ```bash
    *** [err]: Active defrag - AOF loading in tests/unit/memefficiency.tcl
    Expected 46 <= 40 (context: type eval line 37 cmd {assert {$max_latency <= 40}} proc ::test)
    ```

::: details 编译选项

1.  `USE_SYSTEMD=yes` 启用 systemd 支持

    ::: code-group

    ````md [启用systemd支持]
    -   编译选项：加入 `USE_SYSTEMD=yes`
    -   systemd unit 配置:
        1. `Type=notify`
        2. 不需要跟踪 PID 文件
    -   配置文件：

        ```
        daemonize no
        supervised systemd
        ```
    ````

    ````md [禁用systemd支持]
    -   编译选项：加入 `USE_SYSTEMD=no`
    -   systemd unit 配置:
        1. `Type=forking`
        2. 需要跟踪 PID 文件
    -   配置文件：

        ```
        daemonize yes
        supervised auto
        ```
    ````

    ```md [自动检测]
    如果编译时未加入 `USE_SYSTEMD` 选项，可能会自动检测(未测试)：

    1. 如果找到 systemd 开发库，自动启用支持；
    2. 如果没找到，编译时不包含 systemd 代码。
    ```

    :::

2.  `BUILD_TLS=yes` 启用 tls 支持

:::

## 配置文件

redis 源码包中自带了参考配置文件，可以备份该参考配置，按需增减配置，最后清除不必要的注释行

:::code-group

```bash [备份默认配置]
cp ~/redis-8.4.0/redis-full.conf /server/etc/redis/config/source-full.conf
cp ~/redis-8.4.0/redis.conf /server/etc/redis/config/source.conf
```

<<< @/assets/linux/script/redis/redis.bash [主配置]
:::

### 自定义配置

::: code-group
<<< @/assets/linux/etc/example/redis/config/custom/01-network.conf{ini} [01-network.conf]
<<< @/assets/linux/etc/example/redis/config/custom/02-tls.conf{ini} [02-tls.conf]
<<< @/assets/linux/etc/example/redis/config/custom/03-general.conf{ini} [03-general]
<<< @/assets/linux/etc/example/redis/config/custom/04-rdb.conf{ini} [04-rdb]
<<< @/assets/linux/etc/example/redis/config/custom/05-replication.conf{ini} [05-replication]
<<< @/assets/linux/etc/example/redis/config/custom/06-keys-tracking.conf{ini} [06-keys-tracking]
<<< @/assets/linux/etc/example/redis/config/custom/07-acl.conf{ini} [07-acl]
<<< @/assets/linux/etc/example/redis/config/custom/08-client.conf{ini} [08-client]
<<< @/assets/linux/etc/example/redis/config/custom/09-memory-management.conf{ini} [09-memory-management]
<<< @/assets/linux/etc/example/redis/config/custom/10-lazy-freeing.conf{ini} [10-lazy-freeing]
<<< @/assets/linux/etc/example/redis/config/custom/11-io.conf{ini} [11-io]
<<< @/assets/linux/etc/example/redis/config/custom/12-oom.conf{ini} [12-oom]
<<< @/assets/linux/etc/example/redis/config/custom/13-thp.conf{ini} [13-thp]
<<< @/assets/linux/etc/example/redis/config/custom/14-aof.conf{ini} [14-aof]
<<< @/assets/linux/etc/example/redis/config/custom/15-shutdown.conf{ini} [15-shutdown]
<<< @/assets/linux/etc/example/redis/config/custom/16-long-blocking.conf{ini} [16-long-blocking]
<<< @/assets/linux/etc/example/redis/config/custom/17-long-cluster.conf{ini} [17-long-cluster]
<<< @/assets/linux/etc/example/redis/config/custom/18-long-cluster-support.conf{ini} [18-long-cluster-support]
<<< @/assets/linux/etc/example/redis/config/custom/19-slow-log.conf{ini} [19-slow-log]
<<< @/assets/linux/etc/example/redis/config/custom/20-latency.conf{ini} [20-latency]
<<< @/assets/linux/etc/example/redis/config/custom/21-event-notification.conf{ini} [21-event-notification]
<<< @/assets/linux/etc/example/redis/config/custom/22-advanced-config.conf{ini} [22-advanced-config]
<<< @/assets/linux/etc/example/redis/config/custom/23-active-defragmentation.conf{ini} [23-active-defragmentation]
:::

### 日志分割

Redis 可以使用 Logrotate 自动轮转来分割日志，详细说明<a :href="logrotateLink" target="_blank">[:point_right:点此查看]</a>

<<< @/assets/linux/script/logrotate.d/redis{bash}

## 配置系统单元

推荐统一使用 systemd 管理各种服务

::: code-group

<<< @/assets/linux/service/redis.service{ini} [系统单元配置]

```bash [重载配置]
systemctl daemon-reload
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

::: info 源码包自带生成工具

redis 源码包上的 `./utils/gen-test-certs.sh` 脚本，用于一键生成 TLS 相关证书和密钥：

::: code-group

```bash [执行脚本]
./gen-test-certs.sh
cp -r ~/redis-8.4.0/utils/tests/tls/ /server/etc/redis/
# 文件权限在最后统一授权
```

```bash [生成文件]
#   tests/tls/ca.{crt,key}          自签名CA证书和私钥
#   tests/tls/redis.{crt,key}       没有限制的证书和私钥
#   tests/tls/client.{crt,key}      限制为SSL客户端使用的证书和私钥
#   tests/tls/server.{crt,key}      限制为SSL服务器使用的证书和私钥
#   tests/tls/redis.dh              DH参数文件，新版openssl已经不建议使用
```

<<< @/assets/linux/script/redis/gen-test-certs.sh [脚本]
:::

### 2. 配置 TLS

Redis 配置 TLS 相关<a :href="redisTlsLink" target="_blank">[:point_right:请点此查看详情]</a>

### 3. 分发证书

将 CA 证书（ca.crt）分发给所有客户端，以便它们能够验证服务器的身份。

如果使用了客户端证书认证，还需要将客户端证书（client.crt）分发给客户端，并将 CA 证书分发给服务器，以便服务器能够验证客户端的身份。

## 使用 redis-cli 登录

::: code-group

```bash [本机]
redis-cli -p 6379
# 带用户密码时（其他登录此功能相同，略）
auth [username] password
# 可以选择特定数据库（其他登录此功能相同，略）
SELECT 3
```

```bash [本机服务端认证]
redis-cli -p 16379 --tls --cacert /server/etc/redis/tls/ca.crt
```

```bash [本机双向认证]
redis-cli -p 16379 --tls \
--cacert /server/etc/redis/tls/ca.crt \
--cert /server/etc/redis/tls/client.crt \
--key /server/etc/redis/tls/client.key
```

```bash [远程]
redis-cli -h 192.168.66.256 -p 6379
```

```bash [远程服务端认证]
redis-cli -h 192.168.66.256 -p 16379 --tls --cacert /server/etc/redis/tls/ca.crt
```

```bash [远程双向认证]
redis-cli -h 192.168.66.256 -p 16379 --tls \
--cacert /server/etc/redis/tls/ca.crt \
--cert /server/etc/redis/tls/client.crt \
--key /server/etc/redis/tls/client.key
```

:::

## 权限

```bash
chown redis:redis -R /server/redis /server/logs/redis /server/etc/redis
find /server/redis /server/logs/redis /server/etc/redis -type f -exec chmod 640 {} \;
find /server/redis /server/logs/redis /server/etc/redis -type d -exec chmod 750 {} \;
chmod 750 -R /server/redis/bin
find /server/etc/redis/tls -type f -exec chmod 600 {} \;
find /server/etc/redis/tls -type d -exec chmod 700 {} \;
```
