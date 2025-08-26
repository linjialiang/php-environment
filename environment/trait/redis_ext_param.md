不同版本的参数会有差异，默认值也不会完全一样，这里以 `Redis 8.2.1` 为例：

### 1. 配置示例

为了读取配置文件，Redis 必须以文件路径作为第一个参数，如：

```bash
/server/redis/bin/redis-server /server/redis/redis.conf
```

### 2. 单位说明

当需要内存大小时，可以以通常的形式指定它，例如 `1k` `5GB` `4M` 等

| 单位 | 对应比特               |
| ---- | ---------------------- |
| 1k   | 1000 bytes             |
| 1kb  | 1024 bytes             |
| 1m   | 1000000 bytes          |
| 1mb  | `1024*1024` bytes      |
| 1g   | 1000000000 bytes       |
| 1gb  | `1024*1024*1024` bytes |

其中，单元不区分大小写，因此 `1GB` `1Gb` `1gB` 都是相同的。

### 3. 配置文件包含

1. 允许在主配置文件中引入其他配置文件；如：

    ```
    include /server/redis/conf/a.conf
    include /server/redis/conf/b.conf
    include /server/redis/conf/c.conf
    ```

2. 支持**嵌套包含**​​：被引入的文件可以进一步包含其他文件，形成层级化的配置结构（需谨慎使用以避免循环引用）；

3. 前面的选项会被后面相同的选项覆盖；

4. 可以使用 `*` 通配符引入相同目录下的多个文件，如：

    ```
    include /server/redis/conf/*.conf
    ```

### 4. 模块

Redis 允许通过外部动态库（.so 文件）扩展功能，例如新增数据类型（如 JSON 支持）、命令（如全文搜索）等；

loadmodule 指令用于在 Redis 服务启动时自动加载模块，若加载失败则服务会终止（abort），确保依赖模块的可用性。

-   语法：`loadmodule /path/to/module.so [可选参数1 可选参数2 ...]`

-   示例：

    ```
    loadmodule /opt/redis/modules/rejson.so
    loadmodule /opt/redis/modules/rediseach.so SEARCH_THREADS=4
    ```

### 5. 网络连接

这部分配置主要定义了 Redis 服务器的网络连接和安全相关配置，包括监听地址、端口、连接限制、安全模式等。

1. `bind 127.0.0.1 -::1` 绑定服务要监听的网卡列表

    - `bind 0.0.0.0` ：监听本机所有的 `IPv4` 网卡
    - `bind 127.0.0.1 192.168.66.254` ：监听 1）本机客户端；2）允许通过 `192.168.66.254` 网卡访问 redis 服务的客户端
    - `-` 前缀：表示若地址不可用（如无对应网卡），Redis 不会启动失败（但已占用或协议不支持的地址仍会报错）。

2. `protected-mode yes` 保护模式

    - 作用：防止未授权访问，当以下条件同时满足时，仅允许本地连接：

        1. protected-mode yes
        2. 未配置 bind 或仅绑定到本地地址（如 127.0.0.1）
        3. 未设置密码（requirepass）

    - 禁用场景：若需允许远程连接且未配置密码，必须显式关闭（protected-mode no），但强烈建议同时设置密码和防火墙规则

    - 无需关注的危险参数：Redis 为了你的安全，默认锁死了某些关键配置和危险命令，如：

        1. `​​enable-protected-configs`
        2. `enable-debug-command`
        3. `enable-module-command`

3. `port 6379` tcp 监听端口

    设为 0 可禁用 TCP 监听，仅通过 Unix Socket 访问

4. `tcp-backlog 511` 已完成三次握手的连接队列最大数量

    内核(sysctl 里的 net.core.somaxconn)需同步调整，以避免被截断

5. `​​Unix Socket​​`

    - 作用：通过 unixsocket 和 unixsocketperm 配置本地套接字路径及权限，提升本地通信效率。
    - 说明：虽然 socket 效率高，但通常更加推荐 tcp 连接

6. `timeout 0` 空闲超时

    - 作用；客户端空闲超时时间（秒），0 表示不主动断开连接
    - 提示：设为 `0`，配合 `tcp-keepalive 300` 是最优解

7. `tcp-keepalive 300` 心跳包

    - 作用；默认每 300 秒发送 TCP ACK 保活包，检测死连接并维持网络设备中的连接状态

8. `# socket-mark-id 0` 无需理会

    允许你为 Redis 服务器的监听套接字打上一个特定的标记（mark），主要用于实现复杂的网络路由和流量控制策略

### 6. TLS/SSL

1.  `tls-port 6379` TLS 监听端口

    -   作用：tls 传输的监听端口，端口号必须跟 `port` 不一样

    ::: code-group

    ```ini [仅开启 tls 监听端口]
    # port 0          # 禁用普通 tcp 监听端口
    # tls-port 6379  # tls 监听端口设为 6379
    ```

    ```ini [同时开启普通 tcp 与 tls 监听端口]
    # port 6379       # 普通 tcp 监听端口设为 6379
    # tls-port 16379  # tls 监听端口设为 16379
    ```

    :::

2.  证书配置

    ::: code-group

    ```ini [服务器证书]
    # tls-cert-file redis.crt      # X.509证书(PEM格式)
    # tls-key-file redis.key       # 私钥文件(PEM格式)
    # tls-key-file-pass secret     # 私钥密码(如果有)
    ```

    ```ini [客户端证书]
    # 应用于双向认证场景
    # tls-client-cert-file client.crt # X.509证书(PEM格式)
    # tls-client-key-file client.key  # 私钥文件(PEM格式)
    # tls-client-key-file-pass secret # 私钥密码(如果有)
    ```

    :::

3.  安全增强配置-认证机制

    ::: code-group

    ```ini [​​CA证书配置]
    # tls-ca-cert-file ca.crt          # CA证书文件
    # tls-ca-cert-dir /etc/ssl/certs   # CA证书目录
    ```

    ```ini [客户端认证]
    # tls-auth-clients no        # 不要求客户端证书
    # tls-auth-clients optional  # 可选客户端证书
    ```

    :::

4.  密钥交换-DH 参数

    `OpenSSL 3.0+` 不再需要此配置

    ```ini
    # tls-dh-params-file redis.dh
    ```

5.  协议与加密配置

    ```ini
    # 协议版本控制
    # 安全建议​​：禁用TLSv1.0和TLSv1.1以降低攻击面
    # tls-protocols "TLSv1.2 TLSv1.3"  # 推荐配置

    # 加密套件
    # - TLSv1.2及以下​​：
    # tls-ciphers DEFAULT:!MEDIUM
    # - TLSv1.3专用​​：
    # tls-ciphersuites TLS_CHACHA20_POLY1305_SHA256

    # 控制加密算法选择优先级
    # - yes：服务端优先选择加密算法（推荐）
    # - no：客户端优先选择加密算法（默认）
    # tls-prefer-server-ciphers yes
    ```

6.  性能优化配置

    通过会话缓存，减少 TLS 握手开销，提升连接建立速度

    ```ini
    # tls-session-caching no         # 使用此命令禁用会话缓存(默认启用)
    # tls-session-cache-size 5000    # 缓存会话数(默认20480)
    # tls-session-cache-timeout 60   # 会话超时(秒，默认300)
    ```

7.  特殊场景配置

    ```ini
    # 复制链路加密
    tls-replication yes  # 启用主从复制加密

    # 集群总线加密
    tls-cluster yes      # 启用集群通信加密
    ```

### 7. 通用配置

1.  `daemonize no` 守护进程模式 ​

    ```
    - 默认前台运行，设为yes转为后台守护进程
    - 守护进程模式下会自动生成PID文件（默认/var/run/redis.pid）
    ```

2.  `supervised auto` 进程监管支持

    | 选项值  | 适用场景               | 工作原理                                                                | 特殊要求                            |
    | ------- | ---------------------- | ----------------------------------------------------------------------- | ----------------------------------- |
    | no      | 默认值，不集成监管系统 | Redis 完全独立运行，不发送任何状态信号                                  | 无                                  |
    | upstart | 使用 upstart 的系统    | 通过发送 SIGSTOP 信号表明服务已就绪                                     | 需在 upstart 配置中添加 expect stop |
    | systemd | 现代 Linux             | 通过 $NOTIFY_SOCKET 发送 READY=1 信号，并定期更新状态                   | 需要正确的 systemd unit 文件        |
    | auto    | 自动检测环境           | 检查 UPSTART_JOB 或 NOTIFY_SOCKET 环境变量决定使用 upstart 还是 systemd | 环境变量需正确设置                  |

3.  `pidfile /var/run/redis_6379.pid` PID 文件

    ```md
    1. 当以 `守护进程模式` 运行（daemonize yes）时：
        - 若未指定 pidfile，默认创建 /var/run/redis.pid
        - 若指定 pidfile，则使用指定路径
    2. 当以 `前台模式` 运行（daemonize no）时:
        - 仅当显式配置 pidfile 才会创建 PID 文件
    ```

4.  `loglevel notice` 日志级别

    -   作用：loglevel 参数控制 Redis 服务器的日志输出详细程度，直接影响：

        1. 系统资源占用（CPU/磁盘 I/O）
        2. 故障排查能力
        3. 敏感信息暴露风险

    -   日志级别列表：

        | 级别           | 输出内容                                           | 性能影响 |
        | -------------- | -------------------------------------------------- | -------- |
        | debug          | 所有调试信息（包括每个命令执行细节）               | 高       |
        | verbose        | ​ 比 debug 少内部操作日志，保留关键事件            | 中       |
        | notice(默认值) | 生产环境推荐级别（服务启停/持久化事件/内存警告等） | 低       |
        | warning        | 仅关键错误和警告（内存不足/持久化失败等）          | 极低     |
        | nothing        | 完全禁用日志                                       | 无       |

5.  `logfile ""` 日志文件

    -   作用：`logfile` 参数控制 Redis 的日志输出目的地：

        1. `""`​​：日志输出到标准输出(stdout)
        2. `文件路径`：日志写入指定文件（如 /var/log/redis.log）
        3. ​​ 特殊行为 ​​：当以守护进程模式运行(daemonize yes)且未指定日志文件时，日志会被重定向到 /dev/null（即丢弃）

    ::: details Redis 日志轮转配置详解

    ```bash
    /server/logs/redis/*.log {
        daily                      # 每天轮转一次
        rotate 30                  # 保留30个历史日志文件
        compress                   # 启用gzip压缩历史日志
        delaycompress              # 延迟一天压缩（压缩前一天的日志）
        missingok                  # 如果日志文件不存在也不报错
        notifempty                 # 空日志文件不轮转
        create 640 redis redis     # 新日志文件权限和属主
        sharedscripts              # 多个日志文件共用postrotate脚本
        postrotate
            # 向Redis发送信号重新打开日志文件
            /usr/bin/kill -USR1 $(cat /run/redis/process.pid 2>/dev/null) 2>/dev/null || true
        endscript
    }
    ```

    | 参数          | 说明                                       | 推荐值                  |
    | ------------- | ------------------------------------------ | ----------------------- |
    | daily         | 轮转频率：daily(天)/weekly(周)/monthly(月) | 生产环境建议 daily      |
    | rotate        | 保留的历史日志文件数量                     | 根据磁盘空间调整(15-30) |
    | compress      | 使用 gzip 压缩历史日志                     | 建议启用                |
    | delaycompress | 延迟压缩(压缩前一天的日志)                 | 建议启用                |
    | create        | 新日志文件的权限和属主                     | 644 redis:redis         |
    | postrotate    | 轮转后执行的命令                           | Redis 需要 USR1 信号    |

    :::

6.  系统日志（Syslog）集成配置 ​

    作用 ​​：将 Redis 日志转发到系统的 syslog 服务

    ```ini
    syslog-enabled no       # 是否启用系统日志转发（默认 no）
    syslog-ident redis      # 系统日志标识（默认"redis"）
    syslog-facility local0  # 日志设施级别（必须为 USER 或 LOCAL0-LOCAL7）
    ```

7.  崩溃日志控制 ​

    ```ini
    # 禁用崩溃日志可获得更干净的core dump文件
    crash-log-enabled no          # 禁用崩溃日志（默认启用）

    # 禁用内存检查可让Redis更快终止（牺牲诊断信息）
    crash-memcheck-enabled no     # 禁用崩溃时的内存检查（默认启用）
    ```

8.  `databases 16` Redis 数据库数量

    databases 参数控制 Redis 实例中逻辑数据库的数量，默认值为 16。

    ::: code-group

    ```md [数据库标识]
    -   数据库编号从 0 开始
    -   最大编号为 databases-1
    -   默认使用 DB 0
    ```

    ```bash [切换数据库]
    SELECT <dbid>  # 切换到指定数据库
    ```

    ```md [隔离性]
    -   不同数据库完全隔离
    -   共享相同的 Redis 进程资源
    ```

    ::: details 数据库数量对服务器性能影响

    | 数据库数量 | 内存开销 | 管理复杂度 | 使用场景   |
    | :--------: | -------- | ---------- | ---------- |
    |     1      | 最低     | 最简单     | 单一应用   |
    |  16(默认)  | 中等     | 可控       | 通用场景   |
    |    32+     | 较高     | 较复杂     | 多租户系统 |

    :::

9.  `always-show-logo no`

    控制 Redis 启动时是否显示 ASCII 艺术字标志

10. `hide-user-data-from-log yes`

    控制是否在日志中隐藏用户敏感数据

    ```bash
    hide-user-data-from-log no  # 默认值（记录完整数据）
    hide-user-data-from-log yes # 启用隐私保护模式
    ```

11. `set-proc-title yes`

    控制是否允许 Redis 动态修改进程名称，默认启用

12. `proc-title-template "{title} {listen-addr} {server-mode}"`

    控制 Redis 进程在系统进程列表（如 ps、top 命令）中的显示格式

13. `locale-collate ""`

    控制字符串比较和排序时使用的本地化规则：

    | 可选值          | 行为                                            | 说明                 |
    | --------------- | ----------------------------------------------- | -------------------- |
    | `""`            | 继承系统                                        | 多语言环境           |
    | `"C"`           | 使用二进制字节序排序（区分大小写，A-Za-z 顺序） | 跨系统结果一致(推荐) |
    | `"POSIX"`       | 使用二进制字节序排序（区分大小写，A-Za-z 顺序） | 同上                 |
    | `"en_US.UTF-8"` | 按英语规则排序（不区分大小写，aAbBcC 顺序）     | 英语用户友好排序     |
    | `"zh_CN.UTF-8"` | 按中文规则排序（拼音顺序）                      | 中文内容排序         |

### 8. 快照配置

快照就是 Redis RDB 持久化配置

1. `save 3600 1 300 100 60 10000` 快照触发条件

    - 作用：定义自动触发 RDB 快照的条件，格式为 `save [<秒数1> <写操作次数1>] [<秒数2> <写操作次数2>] ...`
    - 默认配置：

        1. 3600 秒(1 小时)内至少有 1 次修改
        2. 300 秒(5 分钟)内至少有 100 次修改
        3. 60 秒内至少有 10000 次修改

2. `stop-writes-on-bgsave-error yes` 持久化失败处理

    - 作用：当后台 RDB 保存失败时是否拒绝写入请求
    - 场景分析：

        1. `yes`：快照失败，拒绝任何写入命令，仅支持读取；直到下次快照成功，可保证快照数据准确性
        2. `no`：快照失败，写入操作不受影响；快照可能会丢失数据（大多数场景推荐）

3. `rdbcompression yes` RDB 压缩

    控制 Redis 在生成 RDB 快照时是否对字符串数据进行压缩，默认启用压缩

4. `rdbchecksum yes` RDB 校验和

    控制 Redis 是否在 RDB 持久化文件中添加 CRC64 校验和

5. `sanitize-dump-payload no`

    控制 Redis 在加载 RDB 文件或处理 RESTORE 命令时，对底层数据结构（ziplist/listpack 等）的完整性检查强度

    | 值        | 检测访问                                            | 性能影响 |
    | --------- | --------------------------------------------------- | -------- |
    | `no`      | 不进行深度检查                                      | 无       |
    | `yes`     | 全量检查（包括主从同步和 RDB 加载）                 | 高       |
    | `clients` | 仅检查客户端直接请求（排除主从同步和特殊 ACL 连接） | 中       |

6. `dbfilename dump.rdb` RDB 文件名

    快照存储文件名

7. `rdb-del-sync-files no` 复制文件清理

    控制 Redis 是否自动删除用于复制的 RDB 文件，默认不自动删除

8. `dir ./` 工作目录

    dir 参数指定 Redis 的工作目录，用于存储：

    - RDB 持久化文件 (dbfilename)
    - AOF 文件（如果启用）
    - 集群节点配置文件（如果启用集群模式）

### 9. 主从复制

不常用略过

###
