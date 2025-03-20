## 七、Redis

Redis 官方并没有 Windows 的二进制包，我们可以使用 github 上开源的第三方二进制包 [redis-windows](https://github.com/redis-windows/redis-windows)

为方便起见我这里选择了 `msys2-with-Service` 版本，该版本通常可以一键搞定

::: tip 提示
该项目的 Redis 测试时不支持 tls 功能
:::

### 1. 安装路径

你可以随意定义安装路径，这里为了统一起见，将路径放置在 `C:\redis` 下面

### 2. 配置

1. 创建 RDB 存储目录: `C:\redis\rdbData`

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
# 指定RDB文件的存储目录
# - 注意：AOF文件，也将在此目录中创建
dir ./rdbData

# 取消注释后，则开启ACL(访问控制列表)
# aclfile ./users.acl

# 设置密码为 1
requirepass 1

# 是否开启AOF持久化。
# - 默认为no，表示关闭AOF持久化。
# - 如果设置为yes，则开启AOF持久化。
appendonly yes
```

:::
