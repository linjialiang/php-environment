## 日志管理 {#Logrotate-common}

Linux/Unix 系统使用 `Logrotate` 来管理日志文件。

::: code-group
<<< @/assets/environment-lnmpp/source/logrotate.d/redis{bash} [redis]
<<< @/assets/environment-lnmpp/source/logrotate.d/nginx{bash} [nginx]
<<< @/assets/environment-lnmpp/source/logrotate.d/example{bash} [带备注]
:::

| 常用指令                                     | 描述                                              |
| -------------------------------------------- | ------------------------------------------------- |
| `logrotate -vf /etc/logrotate.conf`          | 强制立即轮转所有配置文件（-v 显示详细过程）       |
| `logrotate -vf /etc/logrotate.d/your_config` | 强制立即轮转指定的配置文件（-v 显示详细过程）     |
| `logrotate -d /etc/logrotate.d/your_config`  | 模拟轮转过程并显示详细信息                        |
| `grep logrotate /var/log/syslog`             | 查看 logrotate 自身的执行记录和可能出现的错误信息 |

::: warning :warning: `copytruncate/create` 两者区别

-   copytruncate :

    1. 复制当前日志文件后清空原文件，避免需重启 Redis 或发送信号
    2. 对于不支持主动重新打开日志的程序非常有用
    3. 但理论上在复制和清空之间可能有极小量的日志丢失

-   create :
    1. 确保日志不丢失，
    2. 需要配置 `Logrotate` 脚本，在轮转后对程序发送信号，重新打开日志

:::
