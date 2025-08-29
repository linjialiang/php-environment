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

2. 支持**嵌套包含**：被引入的文件可以进一步包含其他文件，形成层级化的配置结构（需谨慎使用以避免循环引用）；

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

        1. `enable-protected-configs`
        2. `enable-debug-command`
        3. `enable-module-command`

3. `port 6379` tcp 监听端口

    设为 0 可禁用 TCP 监听，仅通过 Unix Socket 访问

4. `tcp-backlog 511` 已完成三次握手的连接队列最大数量

    内核(sysctl 里的 net.core.somaxconn)需同步调整，以避免被截断

5. `Unix Socket`

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

    ```ini [CA证书配置]
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
    # 安全建议：禁用TLSv1.0和TLSv1.1以降低攻击面
    # tls-protocols "TLSv1.2 TLSv1.3"  # 推荐配置

    # 加密套件
    # - TLSv1.2及以下：
    # tls-ciphers DEFAULT:!MEDIUM
    # - TLSv1.3专用：
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

1.  `daemonize no` 守护进程模式

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
        | verbose        | 比 debug 少内部操作日志，保留关键事件              | 中       |
        | notice(默认值) | 生产环境推荐级别（服务启停/持久化事件/内存警告等） | 低       |
        | warning        | 仅关键错误和警告（内存不足/持久化失败等）          | 极低     |
        | nothing        | 完全禁用日志                                       | 无       |

5.  `logfile ""` 日志文件

    -   作用：`logfile` 参数控制 Redis 的日志输出目的地：

        1. `""`：日志输出到标准输出(stdout)
        2. `文件路径`：日志写入指定文件（如 /var/log/redis.log）
        3. 特殊行为 ：当以守护进程模式运行(daemonize yes)且未指定日志文件时，日志会被重定向到 /dev/null（即丢弃）

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

6.  系统日志（Syslog）集成配置

    作用 ：将 Redis 日志转发到系统的 syslog 服务

    ```ini
    syslog-enabled no       # 是否启用系统日志转发（默认 no）
    syslog-ident redis      # 系统日志标识（默认"redis"）
    syslog-facility local0  # 日志设施级别（必须为 USER 或 LOCAL0-LOCAL7）
    ```

7.  崩溃日志控制

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

### 10. 客户端缓存追踪

Redis 的客户端缓存辅助支持系统

::: details 主要功能是：

-   服务端记录哪些客户端缓存了哪些键
-   当键被修改时，自动通知相关客户端失效缓存
-   通过 tracking-table-max-keys 限制内存使用

:::

1. `tracking-table-max-keys 1000000`

    - 默认最多跟踪 100 万个键 的客户端缓存关系
    - 达到限制后会触发 LRU 淘汰机制
    - 设为 0 表示不限制（可能消耗大量内存）

### 11. 访问控制列表(ACL)系统

Redis ACL 是 Access Control List 的缩写，它允许 Redis 限制连接客户端只可以使用部分命令。

它的工作方式是：建立连接后，客户端还需要提供 `用户名` 和 `有效的密码` 进行身份验证。

Redis 从 `≥6.0` 开始支持，统一使用访问控制列表(ACL)系统。

1. 登录方式

    - default 账户无密码时建立连接即自动登录

    - default 账户有密码时，建立连接并使用密码登录

        ```bash
        # 通过 requirepass 参数设置密码
        auth <password>
        ```

    - 非 default 账户只能通过账号密码登录

        ```bash
        auth <username> <password>
        ```

2. `acllog-max-len 128` ACL 日志

    - 作用：记录 ACL 相关事件
    - 查看日志：

        ```bash
        redis-cli acl log
        ```

3. `aclfile /etc/redis/users.acl` Redis 外部 ACL 文件

    如果不使用外部 ACL，用户会在

    - 作用：Redis 可以使用外部文件来管理访问控制列表(ACL)，而不是直接在 `redis.conf` 中定义用户规则
    - 特性：
        1. 与 redis.conf 中的用户定义格式完全相同
        2. 每行定义一个用户，支持所有 ACL 规则
        3. 不能同时在 redis.conf 和外部文件中定义用户，如果同时配置 Redis 会拒绝启动并报错
        4. 修改外部文件后需执行 `ACL LOAD` 或重启 Redis 生效

4. `requirepass foobared`

    为兼容低版本操作，默认开启了全功能账户 `default`，此账户默认无密码，可通过 `requirepass` 参数设置密码：

    ```ini
    # 将 default 账户密码设为 123456
    requirepass 123456
    ```

5. `acl-pubsub-default resetchannels` Redis 新用户默认权限

    - 作用：控制 Redis 新创建用户默认权限的配置规则，特别是针对 Pub/Sub（发布/订阅）频道访问控制 的默认行为。
    - 阶段：

        1. `<6.2`：无 Pub/Sub 频道权限控制，新用户默认可访问所有频道
        2. `6.2+`：引入 `&<pattern>` 语法控制频道访问，但需手动配置
        3. `7.0+`： 默认启用严格模式 （acl-pubsub-default resetchannels），需显式授权频道

            ```bash
            # acl-pubsub-default resetchannels 等价于： user new_user off resetkeys -@all resetchannels
            #   off：用户初始状态为禁用（需手动激活）
            #   resetkeys：不匹配任何键（无数据访问权限）
            #   -@all：禁止所有命令（无操作权限）
            #   resetchannels：禁止访问所有Pub/Sub频道（需显式授权）
            ```

    - 可选项：

        | 值            | 作用                                      | Redis 版本默认值  |
        | ------------- | ----------------------------------------- | ----------------- |
        | allchannels   | 新用户默认可以访问所有 Pub/Sub 频道       | Redis 6.2 之前    |
        | resetchannels | 新用户默认无权访问任何频道 （需显式授权） | Redis 7.0+ 默认值 |

6. `rename-command CONFIG ""` 已废弃

    - 作用：主要用于更改命令名称，官方强烈建议使用 ACL 来控制用户的权限

### 12. 客户端

1. `maxclients 10000` Redis 客户端连接数限制

    - 默认同时允许 10000 个客户端连接
    - 系统级配套设置：

        ::: code-group

        ```bash [debian11+]
        # debian11 开始支持模块化，直接覆盖对应的配置文件
        echo "redis soft nofile 65535
        redis hard nofile 65535
        " > /etc/security/limits.d/redis.conf
        ```

        ```bash [debian11-]
        # debain11以下版本追加到 limits.conf 文件
        echo "
        redis soft nofile 65535
        redis hard nofile 65535
        " >> /etc/security/limits.conf
        ```

        :::

### 13. 存储器管理

1. `maxmemory <bytes>` 最大内存限制

    - 作用：设置 Redis 实例可使用的最大内存量（默认单位：字节）

2. `maxmemory-policy noeviction` 内存淘汰策略

    - 默认 `noeviction` 不淘汰，直接报错

    ::: details 可选策略说明

    | 策略            | 说明                                     | 使用场景         |
    | --------------- | ---------------------------------------- | ---------------- |
    | volatile-lru    | 从设置了过期时间的键中淘汰最近最少使用的 | 缓存场景，需 TTL |
    | allkeys-lru     | 从所有键中淘汰最近最少使用的             | 纯缓存系统       |
    | volatile-lfu    | 从设置了过期时间的键中淘汰最不经常使用的 | 热点数据缓存     |
    | allkeys-lfu     | 从所有键中淘汰最不经常使用的             | 长期热点缓存     |
    | volatile-random | 随机淘汰有 TTL 的键                      | 无明确访问模式   |
    | allkeys-random  | 随机淘汰任意键                           | 无规律访问       |
    | volatile-ttl    | 淘汰剩余生存时间最短的键                 | 时效性强的数据   |
    | noeviction      | 不淘汰，直接报错（默认）                 | 数据不可丢失场景 |

    :::

3. `maxmemory-samples 5` 淘汰算法精度

    - 控制 `LRU/LFU/TTL` 算法的精度与性能平衡

4. `maxmemory-eviction-tenacity 10` 淘汰过程强度

    - 控制内存淘汰过程的激进程度

5. `replica-ignore-maxmemory yes` 从节点内存限制

    - 控制从节点是否遵循 maxmemory 设置

6. `active-expire-effort 1` 主动过期清理强度

    - 控制后台清理过期键的强度（1-10），值越大，内存释放速度越快、延迟越明显

### 14. Redis 惰性删除机制

Reids 提示了两种键删除的模式：

| 命令          | 删除类型 | 特点               | 效率               |
| ------------- | -------- | ------------------ | ------------------ |
| `DEL`（常用） | 同步删除 | 立即阻塞式回收内存 | 随元素数量线性增长 |
| `UNLINK`      | 异步删除 | 后台线程逐步回收   | 恒定时间操作       |

::: details 配置内容

```ini
# 1. 针对自动触发场景的惰性删除
lazyfree-lazy-eviction no   # 内存淘汰时是否异步删除（默认同步）
lazyfree-lazy-expire no     # 过期键删除时是否异步（默认同步）
lazyfree-lazy-server-del no # 内部操作导致键删除时是否异步（默认同步）
replica-lazy-flush no       # 从节点全量同步时是否异步清库（默认同步）

# 2. 用户命令行为控制
lazyfree-lazy-user-del no   # 是否将DEL命令自动转为UNLINK（默认不转换）
lazyfree-lazy-user-flush no # FLUSHDB/FLUSHALL默认是否异步（默认同步）
```

:::

### 15. Redis 多线程 I/O 机制

Redis 大多数操作仅使用单线程，但也有一些操作支持 I/O 多线程。

::: details 完全多线程化的操作
以下操作在启用 I/O 线程(io-threads > 1)时会由多线程处理：

| 操作类型 | 具体命令/场景                | 线程化优势           |
| -------- | ---------------------------- | -------------------- |
| 网络读写 | 所有命令的请求读取和响应写入 | 减少系统调用阻塞     |
| 协议解析 | RESP 协议解析                | 降低主线程 CPU 负载  |
| 大键传输 | 批量操作(MSET/MGET 等)的响应 | 加速大数据包网络传输 |

:::

::: details 部分多线程化的操作
这些操作在某些环节使用多线程：

| 操作类型      | 多线程环节                     | 单线程环节   |
| ------------- | ------------------------------ | ------------ |
| UNLINK        | 实际内存回收                   | 键标记为删除 |
| FLUSHDB ASYNC | 后台清空数据库                 | 命令触发     |
| 过期键删除    | 内存回收(lazyfree-lazy-expire) | 过期判断     |

:::

::: details 始终单线程的关键操作
以下核心操作始终在主线程执行：

| 操作类型 | 原因                   | 典型命令                       |
| -------- | ---------------------- | ------------------------------ |
| 命令执行 | 保证原子性和数据一致性 | SET/GET/HINCRBY 等所有数据操作 |
| Lua 脚本 | 脚本原子性要求         | EVAL/EVALSHA                   |
| 事务     | 事务隔离性保证         | MULTI/EXEC/DISCARD             |
| 阻塞命令 | 需要持续监控           | BLPOP/BRPOP/BZPOPMAX           |

:::

1. `io-threads 4` 线程数量

    **默认情况下，线程是禁用的**，官方建议只在至少有 4 个或更多核心的机器上启用它，并且至少留下 1 个备用核心。

    实际工作线程数为 `io-threads - 1` 因为包含了主线程。

    ::: details 线程分工

    | 线程类型 | 职责                           | 优化重点                |
    | -------- | ------------------------------ | ----------------------- |
    | 主线程   | 命令执行、过期处理、集群通信等 | CPU 密集型操作          |
    | I/O 线程 | 网络读写、协议解析             | 系统调用阻塞(send/recv) |

    :::

    ::: details 硬件适配建议

    | CPU 核心数 | 推荐配置     | 工作线程数 |
    | ---------- | ------------ | ---------- |
    | 4 核心     | io-threads 3 | 2          |
    | 8 核心     | io-threads 6 | 5          |
    | 16 核心    | io-threads 8 | 7          |

    :::

### 16. Redis OOM (Out-Of-Memory) 控制

1. `oom-score-adj no` 默认不干预内核 OOM 策略

    - 选项：

        ```
        - no：不修改Redis进程的oom_score_adj值（默认）
        - yes：等同于relative模式
        - absolute：直接使用oom-score-adj-values的绝对值设置
        - relative：基于进程初始值相对调整（通常初始为0，效果类似absolute）
        ```

2. `oom-score-adj-values 0 200 800` OOM 分数值

    - 三个值分别对应：

        ```
        - 主节点 （master）：0
        - 从节点 （replica）：200
        - 后台子进程（bgsave等）：800
        ```

### 17. Redis THP 控制机制

THP(kernel Transparent Huge Pages) 是 Linux 内核的内存管理特性，自动将 4KB 常规页合并为 2MB 大页，旨在减少 TLB 缺失和提高内存访问效率。

1. `disable-thp yes` 保持默认(`yes`)即可

### 18. AOF 持久化机制

AOF（Append Only File）是 Redis 提供的持久化机制，通过记录所有写操作命令来保证数据安全，相比 RDB 快照提供更精细的数据保护。

1. `appendonly no`

    - 作用：是否启用 AOF 持久化模式
    - 选项：
        - no：禁用（默认）
        - yes：启用，所有写操作会被记录到 AOF 文件
    - 建议：生产环境建议启用（yes）

2. `appendfilename "appendonly.aof"`

    - 作用：AOF 基础文件名（Redis 7+会扩展为多文件结构）
    - 文件示例：

        ```
        appendonly.aof.1.base.rdb：基础快照
        appendonly.aof.1.incr.aof：增量操作
        ```

3. `appenddirname "appendonlydir"`

    - 作用：AOF 文件存储的相对目录（Redis 7+引入）
    - 根目录：配置的 RDB 板块里的 `dir` 指令指定的工作目录作为根目录

4. `appendfsync everysec` 同步策略

    - 作用 ：控制数据刷盘策略
    - 默认 ：`everysec`（推荐生产环境使用）
    - 选项 ：

        | 值       | 数据安全性 | 性能 | 可能丢失数据量 |
        | -------- | ---------- | ---- | -------------- |
        | always   | 最高       | 最差 | 几乎不丢失     |
        | everysec | 中等       | 中等 | 最多 1 秒      |
        | no       | 最低       | 最好 | 依赖 OS 刷新   |

5. `no-appendfsync-on-rewrite no` 重写时同步控制

    - 作用 ：AOF 重写期间是否暂停主线程的 fsync
    - 选项 ：

        - no：保持同步（最安全，可能增加延迟）
        - yes：暂停同步（提升性能，最多丢失 30 秒数据）

    - 建议 ：高负载环境可设为 yes

6. 自动重写触发条件

    - 作用：控制 AOF 自动重写（压缩）的触发条件
    - 规则：
        - 当前 AOF 大小 > 上次重写大小 × (1 + percentage/100)
        - 且当前 AOF 大小 > min-size
    - 示例：当前 AOF 128MB 且上次重写后为 60MB 时会触发重写

        ```ini
        # 默认
        auto-aof-rewrite-percentage 100
        auto-aof-rewrite-min-size 64mb
        ```

7. `aof-load-truncated yes` 损坏文件处理

    - 作用：是否加载不完整的 AOF 文件
    - 选项：
        - yes：尝试加载并记录警告（默认）
        - no：直接拒绝启动（需手动修复）

8. `aof-use-rdb-preamble yes` 混合持久化

    - 作用：AOF 重写时是否使用 RDB 格式存储基础数据（开启 AOF 时默认启用）
    - 优势：
        - 大幅减少 AOF 体积
        - 加快重启恢复速度
    - 建议：始终保持启用（需 Redis 4+）

9. `aof-timestamp-enabled no`

    - 作用：是否在 AOF 中记录时间戳
    - 影响：
        - 启用后支持按时间点恢复
        - 但会破坏与旧版客户端的兼容性
    - 默认：禁用（no）

### 19. Redis 关机流程控制

这块配置主要控制 Redis 实例在关闭时的行为，特别是处理持久化数据（RDB）和主从同步的优雅终止流程。

1. `shutdown-timeout 10` 副本等待超时

    - 默认 10 秒等待副本同步
    - 可设置 10-30 秒（根据集群规模调整）
    - 禁用值设为 0（不推荐生产环境）

2. 信号处理行为

    通过 `shutdown-on-sigint/shutdown-on-sigterm` 两个参数来配置信号处理行为

    ::: details 两者区别

    | 特性         | shutdown-on-sigint    | shutdown-on-sigterm   |
    | ------------ | --------------------- | --------------------- |
    | 触发条件     | `Ctrl+C` 或 `kill -2` | `kill -15`            |
    | 默认行为     | default               | default               |
    | 典型使用场景 | 手动交互式操作        | 系统服务管理/容器编排 |
    | K8s 默认信号 | 不适用                | Pod 终止时默认发送    |
    | 建议生产配置 | 默认                  | 默认                  |

    :::

    ::: details 可选参数组合

    | 参数    | 作用                                         | 适用场景         |
    | ------- | -------------------------------------------- | ---------------- |
    | save    | 强制生成 RDB 快照                            | 关键数据手动备份 |
    | nosave  | 跳过 RDB 保存                                | 紧急重启         |
    | now     | 不等待副本同步                               | 快速故障转移     |
    | force   | 忽略所有错误                                 | 强制终止         |
    | default | 仅在有 save 点时保存 RDB，并等待副本（默认） | 常规生产环境     |

    :::

### 20. 长阻塞命令控制机制

这段配置用于控制可能长时间阻塞 Redis 服务器的操作（如 Lua 脚本、函数和模块命令）的执行时间限制。

1. `lua-time-limit 5000`

    - `lua-time-limit` 现在是 `busy-reply-threshold` 的别名

2. `busy-reply-threshold 5000`
    - 默认：5 秒，即：1 个操作最长只能执行 5s
    - 设为 0 或负值完全禁用时间限制保护

### 21. Redis 集群

不常用，略过

### 22. Redis 集群在 Docker/NAT 环境中的配置

不常用，略过

### 23. Redis 慢查询日志

段配置用于设置 Redis 慢查询日志系统，用于记录执行时间过长的命令。

1. `slowlog-log-slower-than 10000`

    - 作用：定义命令执行时间的阈值
    - 默认：10000（单位：微秒，10000 微秒=10 毫秒）
    - 取值：
        - `>0`：记录超过该时间的命令
        - `0`：记录所有命令（性能影响大）
        - `<0`：完全禁用慢查询日志

2. `slowlog-max-len 128`

    - 作用：控制慢查询日志的存储条数
    - 默认：128（最多记录 128 条慢查询）
    - 特点：
        - 采用 FIFO 队列结构
        - 达到上限后自动淘汰最早记录

### 24. Redis 延迟监控系统

这段配置用于控制 Redis 的延迟监控子系统，该系统专门用于检测和诊断 Redis 实例的延迟问题。

1. `latency-monitor-threshold 0`

    - 默认：关闭（单位：毫秒）
    - 作用机制：当设置为 >0 时，Redis 会记录所有执行时间超过该阈值的操作
    - 记录的数据可通过 LATENCY 命令查看，包括：
        - 延迟事件的时间分布
        - 延迟峰值统计
        - 历史延迟图表

::: details 与慢查询日志的区别

| 特性     | 延迟监控     | 慢查询日志   |
| -------- | ------------ | ------------ |
| 时间精度 | 毫秒级       | 微秒级       |
| 记录内容 | 所有操作类型 | 仅客户端命令 |
| 数据维度 | 时间分布统计 | 原始命令记录 |

:::

### 25. Redis 延迟追踪系统

这段配置控制 Redis 更精细化的延迟追踪功能，相比基础的 latency-monitor 提供更丰富的延迟分析数据。

1. `latency-tracking yes`

    - 默认启用（Redis 7.0+）

2. `latency-tracking-info-percentiles 50 99 99.9`

    - 默认 3 个百分位，适用所有生产环境

### 26. Redis 键空间事件通知配置

这段配置用于控制 Redis 的键空间通知功能，允许客户端通过 Pub/Sub 订阅键空间的操作事件。

1. `notify-keyspace-events ""` 默认禁用

### 27. 高级配置-数据结构存储优化

1. 哈希表优化

    - hash-max-listpack-entries 512：哈希元素 ≤512 时使用紧凑存储
    - hash-max-listpack-value 64：哈希值 ≤64 字节时使用紧凑存储

2. 列表优化

    - list-max-listpack-size -2：每个列表节点 ≤8KB（-2 表示按大小限制）
    - list-compress-depth 0：禁用列表压缩（0=不压缩，1=保留首尾节点不压缩）

3. 集合优化

    - set-max-intset-entries 512：纯数字集合元素 ≤512 时使用特殊编码
    - set-max-listpack-entries 128：混合集合元素 ≤128 时使用紧凑存储

4. 有序集合优化

    - zset-max-listpack-entries 128：有序集合元素 ≤128 时使用紧凑存储

5. HyperLogLog 内存表示策略高级调优选项

    - hll-sparse-max-bytes 3000：稀疏表示最大字节数（超过转密集存储）

6. Streams
    - stream-node-max-bytes 4096：单个 Stream 节点最大字节数
    - stream-node-max-entries 100：单个 Stream 节点最大条目数

### 28. 高级配置-内存管理机制

1. `activerehashing yes` 主动重哈希开关配置

    - 用于控制 Redis 是否定期执行主字典表的渐进式重哈希操作

2. `client-output-buffer-limit` Redis 客户端输出缓冲区限制策略

    - 作用：这段配置定义了 Redis 对客户端输出缓冲区的限制策略，用于防止客户端读取速度过慢导致服务器内存耗尽。
    - 语法详解：

        ```ini
        client-output-buffer-limit <class> <hard limit> <soft limit> <soft seconds>
        ```

        | 参数           | 含义           | 实例值                  | 作用                       |
        | -------------- | -------------- | ----------------------- | -------------------------- |
        | `class`        | 客户端类型     | `normal/replica/pubsub` | 指定应用规则的客户端类别   |
        | `hard limit`   | 硬限制         | 256mb                   | 缓冲区达到此值立即断开连接 |
        | `soft limit`   | 软限制         | 64mb                    | 缓冲区持续超过此值触发计时 |
        | `soft seconds` | 软限制持续时间 | 60                      | 超过软限制持续此时长后断开 |

    - 默认配置：

        ```ini
        client-output-buffer-limit normal 0 0 0
        client-output-buffer-limit replica 256mb 64mb 60
        client-output-buffer-limit pubsub 32mb 8mb 60
        ```

3. `client-query-buffer-limit 1gb` Redis 客户端输出缓冲区最大容量

    - 用于限制 Redis 单个客户端查询缓冲区的最大容量，防止异常客户端消耗过多内存

4. `maxmemory-clients` Redis 的 客户端内存保护机制

    - maxmemory-clients 是 Redis 的客户端内存保护机制，用于限制所有客户端连接（包括 Pub/Sub 和普通客户端）消耗的总内存上限，防止因客户端连接过多导致服务器 OOM（内存溢出）。

    ::: code-group

    ```ini [写法]
    # 配置以最后1个生效
    maxmemory-clients 5%    # 客户端内存不超过总内存的5%，百分比以 maxmemory <bytes> 选项值为基数
    maxmemory-clients 1g    # 客户端内存不超过1GB
    maxmemory-clients 0     # 默认值，无限制
    ```

    ```ini [百分比]
    maxmemory 16gb
    maxmemory-clients 5%   # 客户端最多使用800MB
    ```

    :::

5. `proto-max-bulk-len 512mb` Redis 大容量字符串限制

    | 值    | 作用                      | 使用场景     |
    | ----- | ------------------------- | ------------ |
    | 512mb | 允许最大 512MB 的字符串值 | 默认         |
    | 1gb   | 允许 1GB 超大字符串       | 大型文件缓存 |
    | 10mb  | 严格限制                  | 高安全环境   |

    - proto-max-bulk-len 参数控制 Redis 单个字符串值的最大允许长度，是 Redis 协议层面的安全限制。
    - 影响范围：此限制影响所有涉及字符串的操作：
        - SET key giant_string
        - APPEND key huge_data
        - GET key（返回值限制）
        - LPUSH/RPUSH（单个元素限制）
        - 事务中的批量操作

### 29. 高级配置-性能调优

1. `hz 10` 基准任务频率（1-500，默认 10 次/秒）

    范围在 1 到 500 之间，但是超过 100 的值通常不是一个好主意。

    大多数用户应该使用默认值 10，只有在需要非常低延迟的环境中才将其提高到 100。

2. `dynamic-hz yes` 根据客户端数量动态调整频率

    手动配置 `hz 10` 作为基线值，当客户端连接数增多时，Redis 会自动在实际配置的 hz 值基础上 按需倍增。连接数越多，实际使用的 hz 值也越高。

3. `aof-rewrite-incremental-fsync yes` AOF 重写时每 4MB 刷盘一次

4. `rdb-save-incremental-fsync yes` RDB 保存时每 4MB 刷盘一次

5. Redis LFU 淘汰算法调优参数

    这段配置详细说明了 Redis LFU（Least Frequently Used）淘汰算法的可调参数及其工作原理，用于优化内存淘汰策略。

    1. `lfu-log-factor 10` LFU 对数因子

        - 作用：控制访问频率计数器的增长灵敏度

        - 因子影响对比：

            | 因子值 | 100 次访问计数 | 1000 次访问计数 | 特点           |
            | ------ | -------------- | --------------- | -------------- |
            | 0      | 104            | 255             | 增长过快       |
            | 1      | 18             | 49              | 中等增长       |
            | 10     | 10             | 18              | 推荐值（默认） |
            | 100    | 8              | 11              | 增长缓慢       |

    2. `lfu-decay-time 1` LFU 衰减时间

        - 作用 ：控制访问计数器的衰减速度
        - 选项：

            | 选项 | 说明                   |
            | ---- | ---------------------- |
            | 0    | 永不衰减               |
            | 1    | 每分钟衰减一次（默认） |
            | N    | 每 N 分钟衰减一次      |

### 30. 高级配置-连接管理

Redis 新连接接纳控制机制详解

这段配置定义了 Redis 在单个事件循环周期内接受新连接的能力，包括普通连接和 TLS 加密连接的处理限制。

1. `max-new-connections-per-cycle 10` 普通连接默认 10 个/周期

    - 平衡连接建立速度与事件循环负担

2. `max-new-tls-connections-per-cycle 1` TLS 连接默认 1 个/周期

    - TLS 握手消耗 CPU 资源是普通连接的 5-10 倍

::: code-group

```ini [高并发场景]
# 适用于短连接服务
max-new-connections-per-cycle 50
tcp-backlog 1024  # 配套增加系统积压队列
```

```ini [TLS密集型服务]
# 需要强大CPU支持
max-new-tls-connections-per-cycle 5
tls-session-caching yes  # 启用会话缓存减少握手开销
```

:::

### 31. Redis 主动内存碎片整理机制

主动内存碎片整理（Active Defragmentation）是 Redis 4.0+ 引入的高级内存管理功能，用于在不重启服务的情况下减少内存碎片，提升内存利用率。

1.  `activedefrag no`

    -   主动内存碎片整理开关柜，默认禁用

2.  触发阈值

    1. `active-defrag-ignore-bytes 100mb`
        - 碎片内存 ≥100MB 才启动
    2. `active-defrag-threshold-lower 10`
        - 碎片率 ≥10%触发整理，单位 `%`
    3. `active-defrag-threshold-upper 100`
        - 碎片率上限，单位 `%`

3.  CPU 控制

    1.  `active-defrag-cycle-min 1`
        -   最小 CPU 使用率，单位 `%`
    2.  `active-defrag-cycle-max 25`
        -   最大 CPU 使用率，单位 `%`

4.  `active-defrag-max-scan-fields 1000`

    -   每次扫描字段数

5.  `jemalloc-bg-thread yes`

    -   控制 jemalloc 内存分配器后台线程的启用状态

    | 值    | 说明                             |
    | ----- | -------------------------------- |
    | `yes` | 由专属后台线程异步处理内存回收   |
    | `no`  | 依赖应用程序线程同步处理内存回收 |

6.  `server-cpulist 0-7:2`
    -   绑定 主线程 和 I/O 线程 （如果启用了多线程 I/O）。这些线程负责处理命令请求和返回响应。
7.  `bio-cpulist 1,3`
    -   绑定 后台 I/O 线程 （Background I/O threads）。这些线程负责执行一些缓慢的异步操作，例如关闭文件描述符等。
8.  `aof-rewrite-cpulist 8-11`
    -   绑定执行 AOF 重写 的 子进程 。AOF 重写是一个相对耗资源的操作，绑定 CPU 可以减少对主线程和其他操作的影响。
9.  `bgsave-cpulist 1,10-11`
    -   绑定执行 bgsave（RDB 快照持久化）的子进程 。与 AOF 重写类似，绑定 CPU 可以优化这个后台保存操作的性能。
10. `# ignore-warnings ARM64-COW-BUG`
    -   配置末尾提到的 ignore-warnings ARM64-COW-BUG 是用于让 Redis 忽略特定警告的配置。例如，在某些 ARM64 架构的机器内核版本中，可能存在一个写时复制（Copy-on-Write）方面的内核 Bug，Redis 在启动时检测到系统处于这个“不良状态”会发出警告甚至拒绝启动。此选项可以用于屏蔽此类已知的特定警告（用空格分隔多个警告），但 务必在确认风险可控的情况下使用 。
