---
title: Linux 内核管理与优化
titleTemplate: Linux 下纯手工搭建 PHP 环境
---

# Linux 内核管理与优化

内核设置定义了系统资源的“硬性上限”和“全局规则”，而服务进程配置则在这些规则的约束下，定义了具体应用的“运行逻辑”和“局部策略”。比如：

内核对 `全连接队列长度` 设为 `4096` 后，即使 Nginx 配置成 `8192` ，Nginx 进程最大可用也只有 `4096`

服务器中如果存在多个主要的服务进程，优化内核就需要兼顾到各种服务的特性，通常都是通过修改 sysctl 配置文件（`/etc/sysctl.d/*.conf`）来实现：

## 1. 虚拟内存限制

控制进程是否允许使用虚拟内存

-   0：进程只能使用物理内存(默认值)
-   1：进程可以使用比物理内存更多的虚拟内存

```bash
cat > /etc/sysctl.d/overcommit_memory.conf << EOF
vm.overcommit_memory = 1
EOF
```

## 2. 全连接队列长度

TCP 全连接队列(Accept 队列)最大长度，即已完成三次握手但未被应用层 accept() 的连接数

-   debian13 默认为 4096，通常足够
-   超过 65535 需确认内核是否支持

```bash
cat > /etc/sysctl.d/somaxconn.conf << EOF
net.core.somaxconn = 4096
EOF
```

## 3. 半连接队列长度

TCP 半连接队列(SYN 队列)最大长度，即处于 SYN_RECV 状态的未完成握手连接数

-   debian13 默认为 512，存在高并发服务建议设为 4096
-   增大值会占用更多内存

```bash
cat > /etc/sysctl.d/tcp_max_syn_backlog.conf << EOF
net.ipv4.tcp_max_syn_backlog = 4096
EOF
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
