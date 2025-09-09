## 系统级优化

::: warning :warning: 需理解的是：
系统级的配置仅代表这台服务器最大允许的值，软件的配置文件通常也会提供对应值来做更精准的限制。
:::

### 内核管理 {#kernel_management}

服务器中如果存在多个主要的服务，优化内核就需要兼顾到各种服务的特性，这里通过修改 sysctl 配置文件（`/etc/sysctl.d/*.conf`）来实现：

#### 1. 虚拟内存限制

控制进程是否允许使用虚拟内存

-   0：进程只能使用物理内存(默认值)
-   1：进程可以使用比物理内存更多的虚拟内存

```bash
echo "vm.overcommit_memory = 1" > /etc/sysctl.d/overcommit_memory.conf
```

#### 2. 全连接队列长度

TCP 全连接队列(Accept 队列)最大长度，即已完成三次握手但未被应用层 accept() 的连接数

-   debian13 默认为 4096，通常足够
-   超过 65535 需确认内核是否支持

```bash
echo "net.core.somaxconn = 4096" > /etc/sysctl.d/somaxconn.conf
```

#### 3. 半连接队列长度

TCP 半连接队列(SYN 队列)最大长度，即处于 SYN_RECV 状态的未完成握手连接数

-   debian13 默认为 512，存在高并发服务建议设为 4096
-   增大值会占用更多内存

```bash
echo "net.ipv4.tcp_max_syn_backlog = 4096" \
> /etc/sysctl.d/tcp_max_syn_backlog.conf
```

::: warning :warning: 注意：
在 Debian 13 中，sysctl 的配置文件路径发生了显著变化，从传统的单一文件 `/etc/sysctl.conf` 转向了模块化的分散配置目录。

以下是 Debian13 具体的配置文件路径及其优先级规则：

| 路径                             | 优先级 | 说明           |
| -------------------------------- | ------ | -------------- |
| `/etc/sysctl.d/*.conf`           | 1      | 优先级最高     |
| `/run/sysctl.d/*.conf`           | 2      | 重启失效       |
| `/usr/local/lib/sysctl.d/*.conf` | 3      | 第三方软件配置 |
| `/usr/lib/sysctl.d/*.conf`       | 4      | 系统默认配置   |
| `/lib/sysctl.d/*.conf`           | 5      | 兼容旧版       |

```bash
sysctl --system             # 加载所有配置文件
sysctl -a                   # 检查所有生效参数
sysctl vm.overcommit_memory # 查看特定参数
```

:::

### 资源管理 {#resourc_management}

限制资源的目的是为了防止单个用户或进程过度消耗系统资源（如 CPU、内存、文件打开数等），从而保障系统的稳定性和安全性。

通过修改 `/etc/security/limits.conf` 配置文件，可以对用户或用户组在系统上使用资源的最大值进行限制，具体说明如下：

#### 1. 配置文件

limits 主要分主配置文件和模块化配置文件，这里推荐使用模块化配置文件：

| 配置文件                        | 说明               |
| ------------------------------- | ------------------ |
| `/etc/security/limits.conf`     | 资源限制主配置文件 |
| `/etc/security/limits.d/*.conf` | 模块化管理配置文件 |

#### 2. 案例

::: code-group

```bash [debian11+]
# 针对 postgres 用户
echo "postgres  soft  nofile  65535
postgres  hard  nofile  65535" > /etc/security/limits.d/postgres.conf

# 针对 redis 用户
echo "redis soft nofile 65535
redis hard nofile 65535" > /etc/security/limits.d/redis.conf
```

```bash [debian11-]
echo "
# 针对 postgres 用户
postgres  soft  nofile  65535
postgres  hard  nofile  65535

# 针对 redis 用户
echo "redis soft nofile 65535
redis hard nofile 65535
" >> /etc/security/limits.conf
```

:::
