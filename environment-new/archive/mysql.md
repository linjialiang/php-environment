---
title: 安装 MySQL
titleTemplate: 环境搭建教程
---

# 安装 MySQL

::: danger 警告
MySQL 不再维护
:::

PHP 项目首选关系型数据库是 MySQL，这里以 MySQL8.0 为例

::: danger 警告
MySQL 官方还未适配 Debian12 的源，所以这里先不安装
:::

## 安装 MySQL

推荐使用 MySQL 官方的 apt 源来安装 MySQL8.0

### 1. 添加源

> 这是官方源添加方式

```bash
cd /package/
curl -O https://repo.mysql.com/mysql-apt-config_0.8.25-1_all.deb
chmod +x mysql-apt-config_0.8.25-1_all.deb

# mysql-apt-config.deb 依赖于 gnupg lsb-release
apt install gnupg lsb-release -y
dpkg -i ./mysql-apt-config_0.8.25-1_all.deb
```

::: details 选择 MySQL 服务器
![选择 MySQL 服务器和集群](/assets/environment-new/images/mysql-apt-config.png)
![选择 mysql-8.0](/assets/environment-new/images/mysql-8.0.png)
![选择 ok](/assets/environment-new/images/mysql-apt-config-ok.png)
:::

::: tip 可以考虑国内源

> 国内-清华源

```bash
vim /etc/apt/sources.list.d/mysql-community.list
# 内容如下：
deb https://mirrors.tuna.tsinghua.edu.cn/mysql/apt/debian bookworm mysql-8.0 mysql-tools
```

> 国内-中科大源

```bash
deb http://mirrors.ustc.edu.cn/mysql-repo/apt/debian/ bookworm mysql-apt-config
deb http://mirrors.ustc.edu.cn/mysql-repo/apt/debian/ bookworm mysql-8.0
deb http://mirrors.ustc.edu.cn/mysql-repo/apt/debian/ bookworm mysql-cluster-8.0
deb http://mirrors.ustc.edu.cn/mysql-repo/apt/debian/ bookworm mysql-tools
deb http://mirrors.ustc.edu.cn/mysql-repo/apt/debian/ bookworm mysql-tools-preview

deb-src http://mirrors.ustc.edu.cn/mysql-repo/apt/debian/ bookworm mysql-apt-config
deb-src http://mirrors.ustc.edu.cn/mysql-repo/apt/debian/ bookworm mysql-8.0
deb-src http://mirrors.ustc.edu.cn/mysql-repo/apt/debian/ bookworm mysql-cluster-8.0
deb-src http://mirrors.ustc.edu.cn/mysql-repo/apt/debian/ bookworm mysql-tools
deb-src http://mirrors.ustc.edu.cn/mysql-repo/apt/debian/ bookworm mysql-tools-preview
```

:::

### 2. 更新源

```bash
apt update
```

::: tip 更换 mysql 源
MySQL 官方的源通常比较卡，如果不能正常下载，可以考虑更换国内源，清华大学开源软件镜像站支持 MySQL
:::

### 3. 安装

```bash
apt install mysql-server
```

::: details 配置详情
![将密码留空，以启用UNIX套接字登录验证](/assets/environment-new/images/mysql-01.png)
![将密码留空，以启用UNIX套接字登录验证](/assets/environment-new/images/mysql-02.png)
:::

## Unit 单元

MySQL 安装成功后，自动配置好 Unit 单元服务器

-   原始地址：`/lib/systemd/system/mysql.service`
-   创建链接：`/etc/systemd/system/multi-user.target.wants/mysql.service`

::: details MySQL 系统单元配置文件：
<<<@/assets/environment-new/source/service/mysqld-84.service{ini}
:::

> MySQL 系统单元管理：

| 操作 | 指令                    |
| ---- | ----------------------- |
| 启动 | systemctl start mysql   |
| 关闭 | systemctl stop mysql    |
| 重启 | systemctl restart mysql |
| 状态 | systemctl status mysql  |

::: tip
MySQL 的单元文件不需要修改，默认即可
:::

## 配置 MySQL

### 1. 配置文件概述

mysql 入口配置文件为 `/etc/mysql/my.cnf`

`/etc/mysql/my.cnf` 是一个软链接，最终的文件是 `/etc/mysql/mysql.cnf`

mysql 服务器配置文件推荐全部放在 `/etc/mysql/mysql.conf.d/` 目录下

mysql 客户端配置文件推荐全部放在 `/etc/mysql/conf.d/` 目录下

### 2. 创建目录

创建目录并授权

```bash
mkdir /server/run/mysqld /server/logs/mysqld /server/data
chown mysql:mysql /server/run/mysqld/ /server/logs/mysqld/ /server/data/
chmod 750 /server/data/
```

### 3. 修改配置文件

MySQL 默认配置的内容较少，我们可以参考 MariaDB 以及 MySQL 官方说明来调整配置内容，具体如下：

::: details 默认配置
文件丢失
:::

::: details 修改后的配置参考
文件丢失
:::

::: details init_file
文件丢失
:::

## 数据初始化

### 1. 数据初始化方式

要初始化数据目录，请调用 mysqld 的 `--initialize` 或者 `--initialize-insecure` 选项，

这主要取决于您是否希望服务器为 'root'@'localhost' 帐户：`生成随机的初始密码` 或 `创建没有密码的帐户`：

-   `--initialize` 安装时，会为 root 账户生成随机密码，并且该密码被标记为已过期，如果非系统 root 用户登录，您必须创建一个新密码
-   `--initialize-insecure` 默认不生成 root 密码，这是不安全；

### 2. 数据初始化指令

```bash
# root 有密码，并且标记为过期，非系统 root 用户登录，必须创建一个新密码
mysqld --defaults-file=/etc/mysql/my.cnf --initialize --user=mysql
# root 没有密码，如果要开启可插拔认证，选择没有密码
mysqld --defaults-file=/etc/mysql/my.cnf --initialize-insecure --user=mysql
```

> 查看当前 mysql-server 选项：

```bash
mysqld --help --verbose
```

## 可插拔认证插件

MySQL 服务器端的 `auth_socket 验证插件` 支持 Socket Peer-Credential 可插拔认证

`auth_socket 验证插件` 对从本地主机连接的客户端进行通过 Unix 套接字文件身份验证

`auth_socket 验证插件` 使用 `SO_PEERCRED` 套接字选项获取有关运行客户端程序的用户的信息，所以只能在支持的系统上使用，例如：Linux

### 1. 安装插件

安装 MySQL 时，通常会自动安装 `auth_socket` 插件，但 `auth_socket` 不是内置插件，需要手动加载

-   路径: `/usr/lib/mysql/plugin/auth_socket.so`
-   MySQL 默认插件路径： `/usr/lib/mysql/plugin`，使用 `plugin_dir` 选项可以指定插件目录

### 2. 加载插件

内置插件 MySQL 启动时会按需自动加载，非内置插件则需要手动加载：

::: details 修改配置文件，加载插件（推荐）

```ini
# /etc/mysql/mysql.conf.d/mysqld.cnf

[mysqld]
plugin-load-add=auth_socket.so
```

:::

::: details 运行时修改数据库，加载插件（临时）

```bash
mysql> INSTALL PLUGIN auth_socket SONAME 'auth_socket.so';
```

:::

::: details 检查插件是否加载成功

```bash
mysql> SELECT PLUGIN_NAME, PLUGIN_STATUS FROM INFORMATION_SCHEMA.PLUGINS WHERE PLUGIN_NAME LIKE '%socket%';
+-------------+---------------+
| PLUGIN_NAME | PLUGIN_STATUS |
+-------------+---------------+
| auth_socket | ACTIVE        |
+-------------+---------------+
1 row in set (0.00 sec)
```

:::

::: details 查看全部加载的插件

```bash
mysql> SELECT * FROM INFORMATION_SCHEMA.PLUGINS\G
```

文件丢失
:::

### 3. 卸载插件

内置插件不可卸载，非内置插件卸载如下：

::: details 修改配置文件，卸载插件

配置文件里将对应的 `plugin-load-add` 选项移除

```ini
# /etc/mysqld/mysql.conf.d/mysqld.cnf

[mysqld]
# plugin-load-add=auth_socket.so
```

:::

::: details 运行时修改数据库，卸载插件

```bash
mysql> UNINSTALL PLUGIN auth_socket;
```

:::

### 4. 使用插件

`auth_socket 验证插件` 允许两种方式建立连接

1. 检查系统用户名

    操作系统用户名跟 MySQL 的套接字用户名匹配后，由 MySQL 服务器端程序向指定的客户端操作系统用户建立连接

    ::: tip
    亲测：`auth_socket` 验证方式只支持本机客户端操作系统用户连接数据库
    :::

2. 检查 `mysql.user` 表

    如果检查系统用户名失败，`auth_socket 验证插件` 会继续检查套接字用户名是否跟 `mysql.user` 表的 `authentication_string 字段` 中的行匹配；

    如果匹配则由客户端程序给指定的服务器建立连接，其中 `authentication_string` 的值可以使用下面的方式实现：

    ::: details 为本机客户端操作系统用户，通过套接字文件建立数据库连接

    ```bash
    # 首次添加用户用户，采用 CREATE USER
    CREATE USER 'root'@'localhost' IDENTIFIED WITH auth_socket;
    # 更新用户，采用 ALTER USER
    ALTER USER 'root'@'localhost' IDENTIFIED WITH auth_socket;
    # 首次添加用户，'root'@'localhost' 用户除了系统root登录外还支持系统mysql用户登录
    CREATE USER 'root'@'localhost' IDENTIFIED WITH auth_socket AS 'mysql';
    # 更新用户，'root'@'localhost' 用户除了系统root登录外还支持系统mysql用户登录
    ALTER USER 'root'@'localhost' IDENTIFIED WITH auth_socket AS 'mysql';

    # 删除 'root'@'localhost' 用户
    DROP USER 'root'@'localhost';
    ```

    :::

### 5. 登录

统一登录方式：`mysql -u <mysql用户名> -h <主机地址>`，MySQL 客户端支持简写，具体如下：

-   系统 root 用户登录 `root@localhost`

    ```bash
    mysql
    ```

-   系统 www 用户登录 `root@localhost`

    ```bash
    mysql -u root
    ```

## 启用安全更新模式

```sql
# 设置session级别(当前会话)的系统变量，重启失效
mysql> SET sql_safe_updates=1, sql_select_limit=1000, max_join_size=1000000;
# 设置全局(全部会话)的系统变量，重启失效
mysql> SET global sql_safe_updates=1, sql_select_limit=1000, max_join_size=1000000;
```

::: details 启用安全更新和安全删除
启用 sql_safe_updates 后，在运行 UPDATE 和 DELETE 语句时，如果未指定约束(如：where)也未提供子句(如：limit)，则会发生错误

```sql
UPDATE tbl_name SET not_key_column=val WHERE key_column=val;
UPDATE tbl_name SET not_key_column=val LIMIT 1;
```

:::

::: details 设置 sql_select_limit
设置 `sql_select_limit=1000` ，在没有指定 `LIMIT` 时， SELECT 结果集限制为 `1000` 行；

指定 `LIMIT` 后，SELECT 结果集不受此限制
:::

::: details 设置 max_join_size
设置 `max_join_size=1000000` ，使用 JOIN 多表 SELECT 语句最多检查 `1000000` 行
:::

## 配置选项说明

### 1. bind_address

侦听 TCP/IP 连接的 IP 地址管理网络接口，默认限制了全部网络接口连接 mysql

::: details MySQL 8.0.13 之前

MySQL 8.0.13 之前，bind_address 值，只能接受单个地址：

1. 可以指定单个非通配符 IP 地址或主机名，如：`127.0.0.1`
2. 允许在多个网络接口上监听的通配符地址格式之一：
    - `*` - 支持全部 ipv4 和 ipv6
    - `0.0.0.0` - 支持全部 ipv4
    - `::` - 支持全部 ipv6

:::

::: details MySQL 8.0.13 之后：

MySQL 8.0.13 之后，bind_address 值，可以接受单个地址或地址列表：

1. 可以指定单个或多个非通配符 IP 地址或主机名，如：
    - `127.0.0.1`
    - `127.0.0.1,192.168.10.201`
    - 注意：不允许出现通配符 ip
2. 允许在多个网络接口上监听的通配符地址格式之一：
    - `*` - 支持全部 ipv4 和 ipv6
    - `0.0.0.0` - 支持全部 ipv4
    - `::` - 支持全部 ipv6

:::

### 2. skip_external_locking

`skip_external_locking` 选项用于锁定外部访问，只影响 MyISAM 表的访问

-   未配置（`off`），mysqld 使用外部锁定（系统锁定），外部无法访问 MyISAM 表
-   已配置（`on`），mysqld 外部锁定被禁用，外部可以访问 MyISAM 表

### 3. skip_name_resolve

禁止客户端连接 mysqld 时检查解析主机名，只检查 IP 地址，从而加速远程客户端连接

::: tip

这可能会导致 localhost 用户无法访问

在 MySQL8.0 和 MariaDB 高版本中都没有遇到此问题

:::

## MySQL 常用指令

### 创建本地用户

创建允许本地客户端登陆的超级管理员用户

```sql
create user emad@'127.0.0.1' identified by '1';
grant all privileges on *.* to emad@'127.0.0.1' WITH GRANT OPTION;
flush privileges;
```

::: tip
MySQL 的 `root@localhost` 用户，只允许系统指定的登陆用户免密登陆

开发环境：通常需要为 phpMyAdmin 等服务器管理工具创建一个本地超级管理员用户
:::

### 创建远程用户

创建允许远程客户端登陆的超级管理员用户

```sql
create user emad@'192.168.%.%' identified by '1';
grant all privileges on *.* to 'emad'@'192.168.%.%' WITH GRANT OPTION;
flush privileges;
```

::: tip
bind_address 选项允许 MySQL 系统支持客户端远程连接

MySQL 为了保证安全，在用户登录验证阶段，使用的验证方式还需要判断用户所属 ip

开发环境：通常需要为 navicat 等客户端管理工具创建一个超级管理员用户
:::

### 修改用户密码

```sql
ALTER USER emad@localhost IDENTIFIED BY '123';
flush privileges;
```

### 删除用户

```sql
DROP USER emad@localhost;
```

### 授予超级管理员权限

```sql
GRANT ALL PRIVILEGES ON  *.* to emad@localhost WITH GRANT OPTION;
```

### 用户重命名

支持多个用户同时重命名

```sql
RENAME USER 'emad'@'192.168.%.%' TO 'admin'@'192.168.10.%', 'emad'@'localhost' TO 'admin'@'127.0.0.1';
```
