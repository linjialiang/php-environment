## 七、Redis

Redis 官方并没有 Windows 的二进制包，我们可以使用 github 上开源的第三方二进制包 [redis-windows](https://github.com/redis-windows/redis-windows)

为方便起见我这里选择了 `msys2-with-Service` 版本

### 1. 安装路径

你可以随意定义安装路径，这里为了统一起见，将路径放置在 `C:\Redis` 下面

### 2. 配置

1. 创建 RDB 存储目录: `C:\Redis\rdbData`

::: details 改动配置说明

```ini
# 不能以守护进程的方式运行
daemonize no
# 修改pid文件存放路径
pidfile ./process.pid
# 启用密码登陆
requirepass 1
# 日志路径
logfile ./redis.log
# 开启本地和局域网
# 提示：监听的网卡ip地址必须全部正常，否则网卡都无法正常连接
#   - 设为 0.0.0.0 则监听服务器的全部网卡
#   - 设为 127.0.0.1 -::1 192.168.66.254  监听多个网卡，如网卡不存在可能会报错
bind 127.0.0.1 -::1
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
dir ./rdbData

# 取消注释后，则开启ACL(访问控制列表)
# aclfile ./users.acl

# 设置密码为 1
requirepass 1

# 是否开启AOF持久化。
# - 默认为no，表示关闭AOF持久化。
# - 如果设置为yes，则开启AOF持久化。
appendonly yes

# 配置
# port 设为 0 则禁用非tls连接
port 6379
tls-port 16379
tls-cert-file ./tls/server.crt
tls-key-file ./tls/server.key
tls-ca-cert-file ./tls/ca.crt
tls-client-cert-file ./tls/client.crt
tls-client-key-file ./tls/client.key
# tls-auth-clients optional
# 旧版本OpenSSL（<3.0）需要此配置。新版本不需要此配置，并建议不使用DH参数文件
tls-dh-params-file ./tls/redis.dh
```

:::

### 3. 注册 Windows 服务

使用 sc 安装 Windows 系统服务

::: code-group

```ps1 [安装服务]
sc.exe create Redis binpath="C:\Redis\RedisService.exe -c C:\Redis\redis.conf" start=demand displayName="Redis Service"
```

```ps1 [卸载服务]
sc.exe delete Redis
```

:::

::: warning Redis 系统服务缺陷说明：

该版本的 Redis 服务程序 `redis-server.exe` 不具备创建系统服务的能力，需要通过 RedisService.exe 程序来创建系统服务，所以会有如下问题：

1. 开启守护模式缺陷

    - 开启服务：服务状态正常启动，并同时启动 RedisService.exe 和 redis-server.exe 程序
    - 停止服务：服务状态正常停止，终止 RedisService.exe 程序，但开启守护进程的 redis-server.exe 不会终止，Redis 服务正常
    - 解决：关闭守护模式 `daemonize no`

2. 配置异常缺陷

    - 开启服务：服务状态正常启动， 并同时启动 RedisService.exe 程序，但配置异常导致 redis-server.exe 启动失败，无法提供 Redis 服务
    - 停止服务：服务状态正常停止，终止 RedisService.exe 程序
    - 解决：只能提交 bug 让开发者解决

3. PID 文件无法销毁缺陷

    - pid 文件里的 pid 值跟启动的 RedisService.exe 和 redis-server.exe 程序 pid 值都不一样
    - 解决：只能提交 bug 让开发者解决

:::

::: details 附录：配置案例
<<<@/assets/iis/redis/redis.conf{ini}
:::
