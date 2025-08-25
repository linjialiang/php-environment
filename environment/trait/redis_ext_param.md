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

    内核(net.core.somaxconn)需同步调整，以避免被截断

5. `​​Unix Socket​​`

    - 作用：通过 unixsocket 和 unixsocketperm 配置本地套接字路径及权限，提升本地通信效率。
    - 说明：虽然 socket 效率高，但通常更加推荐 tcp 连接

6. `timeout 0` 空闲超时

    - 作用；客户端空闲超时时间（秒），0 表示不主动断开连接
    - 提示：设为 `0`，配合 `tcp-keepalive 300` 是最优解

7. `tcp-keepalive 300`

    默认每 300 秒发送 TCP ACK 保活包，检测死连接并维持网络设备中的连接状态

8. `# socket-mark-id 0` 无需理会

    允许你为 Redis 服务器的监听套接字打上一个特定的标记（mark），主要用于实现复杂的网络路由和流量控制策略

### TLS/SSL

1. `daemonize no`

    Redis 默认不是以守护进程的方式运行，可以通过该配置项修改，使用 yes 启用守护进程（Windows 不支持守护线程的配置为 no ）

2. `pidfile /var/run/redis.pid`

    Redis 默认不是以守护进程的方式运行，可以通过该配置项修改，使用 yes 启用守护进程（Windows 不支持守护线程的配置为 no ）

3. `port 6379`

    指定 Redis 监听端口，默认端口为 6379，作者在自己的一篇博文中解释了为什么选用 6379 作为默认端口，因为 6379 在手机按键上 MERZ 对应的号码，而 MERZ 取自意大利歌女 Alessia Merz 的名字

4. `bind 127.0.0.1 -::1`

    绑定的主机地址，注意：是指定 Redis 服务器要监听的网卡地址，`0.0.0.0` 代表监听本机所有的 `IPv4` 地址

    如：`bind 127.0.0.1 192.168.66.254` 指本机客户端以及 `192.168.66.0/24` 路由器下的所有局域网客户端都能访问

5. `timeout 0`

    当客户端闲置多长秒后关闭连接，如果指定为 0 ，表示关闭该功能

6. `loglevel notice`

    指定日志记录级别，Redis 总共支持四个级别：debug、verbose、notice、warning，默认为 notice

7. `logfile ""`

    日志记录方式，默认为标准输出，如果配置 Redis 为守护进程方式运行，而这里又配置为日志记录方式为标准输出，则日志将会发送给 /dev/null

8. `databases 16`

    设置数据库的数量，默认数据库为 16，可以使用 SELECT 命令在连接上指定数据库 id

9. `save <seconds> <changes> [<seconds> <changes> ...]`

    指定在多长时间内，有多少次更新操作，就将数据同步到数据文件，可以多个条件配合，Redis 默认配置文件中提供了三个条件：

    `save 3600 1 300 100 60 10000`: 3600 秒（1 小时）内有 1 个更改；300 秒（5 分钟）内有 100 个更改；60 秒内有 10000 个更改

10. `rdbcompression yes`

    指定存储至本地数据库时是否压缩数据，默认为 `yes`，Redis 采用 LZF 压缩，如果为了节省 CPU 时间，可以关闭该选项，但会导致数据库文件变的巨大

11. `dbfilename dump.rdb`

    指定本地数据库文件名，默认值为 `dump.rdb`

12. `dir ./`

    指定本地数据库存放目录

13. `slaveof <masterip> <masterport>`

    设置当本机为 slave 服务时，设置 master 服务的 IP 地址及端口，在 Redis 启动时，它会自动从 master 进行数据同步

14. `masterauth <master-password>`

    当 master 服务设置了密码保护时，slave 服务连接 master 的密码

15. `requirepass foobared`

    设置 Redis 连接密码，如果配置了连接密码，客户端在连接 Redis 时需要通过 `AUTH <password>` 命令提供密码，默认关闭

16. `maxclients 10000`

    设置同一时间最大客户端连接数，默认 10000，Redis 可以同时打开的客户端连接数为 Redis 进程可以打开的最大文件描述符数，如果设置 `maxclients 0`，表示不作限制。

    当客户端连接数到达限制时，Redis 会关闭新的连接并向客户端返回 `max number of clients reached` 错误信息

17. `maxmemory <bytes>`

    指定 Redis 最大内存限制，Redis 在启动时会把数据加载到内存中，达到最大内存后，Redis 会先尝试清除已到期或即将到期的 Key，当此方法处理后，仍然到达最大内存设置，将无法再进行写入操作，但仍然可以进行读取操作。Redis 新的 vm 机制，会把 Key 存放内存，Value 会存放在 swap 区

18. `appendonly no`

    指定是否在每次更新操作后进行日志记录，Redis 在默认情况下是异步的把数据写入磁盘，如果不开启，可能会在断电时导致一段时间内的数据丢失。因为 redis 本身同步数据文件是按上面 save 条件来同步的，所以有的数据会在一段时间内只存在于内存中。默认为 no

19. `appendfilename appendonly.aof`

    指定更新日志文件名，默认为 appendonly.aof

20. `appendfsync everysec`

    指定更新日志条件，共有 3 个可选值：

    - no：表示等操作系统进行数据缓存同步到磁盘（快）
    - always：表示每次更新操作后手动调用 fsync() 将数据写到磁盘（慢，安全）
    - everysec：表示每秒同步一次（折中，默认值）

21. `vm-enabled no`

    指定是否启用虚拟内存机制，默认值为 no，简单的介绍一下，VM 机制将数据分页存放，由 Redis 将访问量较少的页即冷数据 swap 到磁盘上，访问多的页面由磁盘自动换出到内存中（在后面的文章我会仔细分析 Redis 的 VM 机制）

22. `vm-swap-file /tmp/redis.swap`

    虚拟内存文件路径，默认值为 /tmp/redis.swap，不可多个 Redis 实例共享

23. `vm-max-memory 0`

    将所有大于 vm-max-memory 的数据存入虚拟内存，无论 vm-max-memory 设置多小，所有索引数据都是内存存储的(Redis 的索引数据就是 keys)，也就是说，当 vm-max-memory 设置为 0 的时候，其实是所有 value 都存在于磁盘。默认值为 0

24. `vm-page-size 32`

    Redis swap 文件分成了很多的 page，一个对象可以保存在多个 page 上面，但一个 page 上不能被多个对象共享，vm-page-size 是要根据存储的 数据大小来设定的，作者建议如果存储很多小对象，page 大小最好设置为 32 或者 64bytes；如果存储很大大对象，则可以使用更大的 page，如果不确定，就使用默认值

25. `vm-pages 134217728`

    设置 swap 文件中的 page 数量，由于页表（一种表示页面空闲或使用的 bitmap）是在放在内存中的，，在磁盘上每 8 个 pages 将消耗 1byte 的内存。

26. `vm-max-threads 4`

    设置访问 swap 文件的线程数,最好不要超过机器的核数,如果设置为 0,那么所有对 swap 文件的操作都是串行的，可能会造成比较长时间的延迟。默认值为 4

27. `glueoutputbuf yes`

    设置在向客户端应答时，是否把较小的包合并为一个包发送，默认为开启

28. `hash-max-zipmap-[entries|value]`

    指定在超过一定的数量或者最大的元素超过某一临界值时，采用一种特殊的哈希算法

    ```txt
    hash-max-zipmap-entries 64
    hash-max-zipmap-value 512
    ```

29. `activerehashing yes`

    指定是否激活重置哈希，默认为开启（后面在介绍 Redis 的哈希算法时具体介绍）

30. `include /path/to/local.conf`

    指定包含其它的配置文件，可以在同一主机上多个 Redis 实例之间使用同一份配置文件，而同时各个实例又拥有自己的特定配置文件
