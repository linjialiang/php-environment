---
title: 编译安装 MySQL
titleTemplate: 环境搭建教程
---

# 编译安装 MySQL

MySQL 没有为 Debian12 做适配，所以最好的选择就是自己编译安装

## 下载源码包

![Mysql 源码包](/assets/environment/images/mysql_compile-01.png)

::: tip 提示

对于 8.3.0 之前版本，如果没有特殊要求，建议下载 `mysql-boost-version.tar.gz` ，这是包含 `Boost(Includes Boost Headers)` 的源码包；

Boost 是一个 C++标准库，因为 mysql 主要是用 C++写的，它依赖于 C++标准库(即 Boost)；

如果下载的是不含`boost`的包，你还要自己去下载对应版本的 Boost，自己下载的 boost 版本未必能满足当前正在编译的 mysql 版本。
:::

## 编译前依赖准备

1. cmake
2. gcc
3. g++
4. openssl
5. Boost(一个 C++标准库，`8.3.0` 以后已捆绑)；
6. ncurses 库
7. 充足的内存（最好有 2GB 以上的空闲内存，不够的话就添加虚拟内存）；
8. perl(不做 test 就不需要)。
9. bison 2.1 或更高版本

```bash
apt update
apt install -y g++ cmake libldap-dev libsasl2-dev
```

::: warning 注意
编译前系统已经存在的依赖库这里并未指出需要安装，如果系统版本不一样，需要自己根据提示安装其余部分依赖
:::

::: details debian12 纯净版所需完整依赖

```bash
apt install -y make cmake gcc g++ libldap-dev libsasl2-dev libssl-dev \
libncurses-dev bison pkg-config
```

::: tip FIDO 告警说明
`FIDO` 告警无需理会，因为这是企业版才有的功能

```bash
...
CMake Warning at cmake/fido2.cmake:188 (MESSAGE):
  WITH_FIDO is set to "none".  FIDO based authentication plugins will be
  skipped.
Call Stack (most recent call first):
  CMakeLists.txt:1969 (MYSQL_CHECK_FIDO)

...

CMake Warning at libmysql/fido_client/common/CMakeLists.txt:26 (MESSAGE):
  Skipping the fido_client_common library.

...
```

:::

## 编译

在不了解干什么的时候，尽量使用 MySQL 的默认值，并且 MySQL 很多参数都可以通过 my.ini 重新修改。

::: code-group

```bash [创建构建目录]
su - mysql -s /bin/zsh
mkdir ~/mysql-8.4.6/build
cd ~/mysql-8.4.6/build
```

```bash [查看编译选项]
# 查询有关 CMake 支持的选项的信息
cd /home/mysql/mysql-8.4.6/build
cmake .. -LH
# 选项写入文件
cmake .. -LH > options.list
```

```bash [构建指令]
cmake .. \
-DWITH_DEBUG=ON \
-DCMAKE_INSTALL_PREFIX=/server/mysql \
-DWITH_SYSTEMD=ON \
-DFORCE_COLORED_OUTPUT=ON \
-DWITH_MYSQLX=OFF \
-DWITH_UNIT_TESTS=OFF \
-DINSTALL_MYSQLTESTDIR=

make -j4
make test
make install
```

:::

::: tip 注意事项

-   如果 cmake 错误，可删除 build 目录中的文件，即可清楚 cache，然后重新 cmake
-   如果 make 错误，可以执行 `make clean` 后在 make
-   如果 make 没有错误，而是由于 CPU、内存等不够，或者人为 `ctrl+C` 中断的，可以直接 make，不需要再 `make clean`
    -- 由于 mysql 编译过程需要花费很多时间，如果全部重新 make，需要很多时间
    -- 内存不足容易出现如下代码：

    ```bash
    c++: fatal error: Killed signal terminated program cc1plus
    compilation terminated.
    make[2]: *** [sql/CMakeFiles/sql_gis.dir/build.make:146: sql/CMakeFiles/sql_gis.dir/gis/difference_functor.cc.o] Error 1
    make[1]: *** [CMakeFiles/Makefile2:28998: sql/CMakeFiles/sql_gis.dir/all] Error 2
    make: *** [Makefile:166: all] Error 2
    ```

:::

### cmake 选项说明

| commom                         | note                                                                        |
| ------------------------------ | --------------------------------------------------------------------------- |
| -DWITH_DEBUG                   | 是否开启调式模式，开启的同时会禁用优化                                      |
| -DCMAKE_INSTALL_PREFIX         | MySQL 安装基目录                                                            |
| -DMYSQL_DATADIR                | MySQL 数据目录                                                              |
| -DSYSCONFDIR                   | 选项文件目录                                                                |
| -DMYSQL_UNIX_ADDR              | Unix 套接字文件                                                             |
| -DWITH_SYSTEMD                 | 启用 systemd 支持文件的安装                                                 |
| -DSYSTEMD_SERVICE_NAME         | systemd 下的 MySQL 服务名称                                                 |
| -DENABLED_LOCAL_INFILE         | 是否支持将本地文件转换为数据库数据                                          |
| -DFORCE_COLORED_OUTPUT         | 编译时是否为 gcc 和 clang 启用彩色编译器输出                                |
| -DWITH_MYSQLX                  | 是否启用 X 协议，默认开启                                                   |
| -DWITH_UNIT_TESTS              | 是否使用单元测试编译 MySQL                                                  |
| -DINSTALL_MYSQLTESTDIR         | 是否安装单元测试目录(mysql-test)，不需要就设为空值(-DINSTALL_MYSQLTESTDIR=) |
| -DTMPDIR                       | 临时文件的位置(指定目录必须存在)                                            |
| ~~-DDEFAULT_CHARSET~~          | 默认字符集，默认使用 `utf8mb4` 字符集                                       |
| ~~-DDEFAULT_COLLATION~~        | 默认排序规则，默认使用 `utf8mb4_0900_ai_ci`                                 |
| ~~-DMYSQL_TCP_PORT~~           | TCP/IP 端口号，默认值为 `3306`                                              |
| ~~-DWITH_SSL~~                 | SSL 支持类型，默认 system ，使用系统自带 openssl                            |
| ~~-DSYSTEMD_PID_DIR~~          | systemd 下的 PID 文件的目录，指定无效，会被 INSTALL_LAYOUT 值隐式更改       |
| ~~-DWITH_BOOST~~               | 构建 MySQL 需要 Boost 库（8.3.0 后不存在此选项）                            |
| ~~-DDOWNLOAD_BOOST~~           | boost 查不到，是否下载 Boost 库（8.3.0 后不存在此选项）                     |
| ~~`-DDOWNLOAD_BOOST_TIMEOUT`~~ | 下载 Boost 库的超时秒数（8.3.0 后不存在此选项）                             |

### 启用 systemd 支持文件

选项 `-DWITH_SYSTEMD=ON` 用于启用 systemd 支持文件；

启用后将安装 systemd 支持文件，并且不再安装 `mysqld_safe` 和 `System V 初始化` 等脚本；

在 systemd 不可用的平台上，启用 WITH_SYSTEMD 会导致 CMake 出错。

### 什么是 X Plugin

MySQL X Plugin 是 MySQL 的一种插件，它可以在 MySQL 服务器中运行，为 Python 和 JavaScript 等编程语言提供 API 接口。

## 配置

### mysql 配置文件

::: code-group
<<<@/assets/environment/source/etc/mysql/init.sql [init.sql]
<<<@/assets/environment/source/etc/mysql/my.cnf{ini} [my.cnf]
:::

### 数据初始化

```bash
cd /server/mysql

# root 有密码，并且标记为过期，非系统 root 用户登录，必须创建一个新密码
bin/mysqld --initialize --user=mysql --basedir=/server/mysql --datadir=/server/data
# root 没有密码，如果要开启可插拔认证，选择没有密码
bin/mysqld --initialize-insecure --user=mysql --basedir=/server/mysql --datadir=/server/data
```

### systemd 单元

::: code-group
<<<@/assets/environment/source/service/mysqld-84.service{bash} [mysqld-84.service]

```bash [启用单元]
systemctl enable mysqld-84.service
systemctl daemon-reload
```

:::

## 登录

服务器上使用 socket 登录

```bash
systemctl start mysqld-84.service

# 使用sock文件登录
# 编译时如果未指定socket路径或socket与编译时指定的不一致，就需要手动指定socket路径
# 默认使用的登录插件是 caching_sha2_password ，所有系统用户均可登录
mysql --socket=/run/mysql/mysqld-84.sock -u root
```

## 身份验证插件

MySQL 常用的身份验证插件有 3 种：

| plugin                 | note |
| ---------------------- | ---- |
| auth_socket            | 本地 |
| caching_sha2_password  | 新的 |
| mysql_native_password​ | 旧的 |

### auth_socket

auth_socket.so 是动态插件，需手动配置才能生效，全称是 `套接字对等凭据可插拔身份验证`

auth_socket 身份验证插件通过 Unix 套接字文件从本地主机连接的客户端进行身份验证。

该插件使用 SO_PEERCRED 套接字选项来获取有关运行客户端程序的用户的信息。因此，该插件只能在支持 SO_PEERCRED 选项的系统上使用，例如 Linux。

::: code-group

```ini [安装]
# 修改 my.cnf 后，重新启动服务器以使新设置生效。

# /server/etc/my.cnf
[mysqld]
plugin-load-add=auth_socket.so
```

```bash [临时安装]
# 在运行时加载插件，重启失效
mysql> INSTALL PLUGIN auth_socket SONAME 'auth_socket.so';
# 临时卸载，重启失效
mysql> UNINSTALL PLUGIN auth_socket;
```

```bash [验证安装]
mysql> SELECT PLUGIN_NAME, PLUGIN_STATUS FROM INFORMATION_SCHEMA.PLUGINS WHERE PLUGIN_NAME LIKE '%socket%';
+-------------+---------------+
| PLUGIN_NAME | PLUGIN_STATUS |
+-------------+---------------+
| auth_socket | ACTIVE        |
+-------------+---------------+
1 row in set (0.02 sec)
```

```sql [使用用户]
-- 创建用户，采用 CREATE USER
CREATE USER 'root'@'localhost' IDENTIFIED WITH auth_socket;
-- 创建用户，'root'@'localhost' 用户除了系统root登录外还支持mysql用户登录
CREATE USER 'root'@'localhost' IDENTIFIED WITH auth_socket AS 'mysql';

-- 更新用户，采用 ALTER USER
ALTER USER 'root'@'localhost' IDENTIFIED WITH auth_socket;
-- 更新用户，'root'@'localhost' 用户除了系统root登录外还支持mysql用户登录
-- mysql系统账户作为 mysql 的主账号，无需授权也可以支持登录 root 用户的
ALTER USER 'root'@'localhost' IDENTIFIED WITH auth_socket AS 'mysql';

-- 删除 'root'@'localhost' 用户
DROP USER 'root'@'localhost';
```

:::

### caching_sha2_password

从 `MySQL 8.0.4` 开始，MySQL 默认身份验证插件从 mysql_native_password​ 改为 caching_sha2_password；

客户端如果是从 TCP/IP 连接 MySQL 的现在都推荐使用 `caching_sha2_password`，它更加的安全可靠，不过速度略有牺牲；

caching_sha2_password 是 MySQL 捆绑插件，无需额外载入。

::: code-group

```sql [创建用户]
-- WITH caching_sha2_password 指定插件，8.0.4以后默认就是 caching_sha2_password
-- BY '1' 设置密码为1，mysql会自动进行对应的加密处理
-- WITH 和 BY 都可以省略，但是 WITH 必须在 BY 前面（未指定插件，如何加密）
CREATE USER 'admin'@'192.168.%.%' IDENTIFIED WITH caching_sha2_password BY '1';
CREATE USER 'admin'@'127.0.0.1' IDENTIFIED WITH caching_sha2_password BY '1';
```

```sql [用户授权]
-- ALL 对应 ON 后面表的全部功能
-- 使用 ON 时，该语句授予权限
-- 如果没有 ON ，该语句将授予角色
-- *.* 授权 所有数据库.所有表
-- WITH GRANT OPTION 能够向其他用户授予或撤消您自己拥有的权限
GRANT ALL ON *.* TO 'admin'@'192.168.%.%' WITH GRANT OPTION;
GRANT ALL ON *.* TO 'admin'@'127.0.0.1' WITH GRANT OPTION;
```

```sql [更新用户]
-- 备注同创建
ALTER USER 'admin'@'192.168.%.%' IDENTIFIED WITH caching_sha2_password BY '1';
ALTER USER 'admin'@'127.0.0.1' IDENTIFIED WITH caching_sha2_password BY '1';
```

```sql [删除用户]
DROP USER 'admin'@'192.168.%.%';
```

:::

::: tip 查看用户的验证插件

新版 MySQL 使用 `authentication_string` 字段替换了之前的 `password` 字段

```sql
select user, host, plugin, authentication_string from mysql.user;

-- +------------------+-----------+-----------------------+------------------------------------------------------------------------+
-- | user             | host      | plugin                | authentication_string                                                  |
-- +------------------+-----------+-----------------------+------------------------------------------------------------------------+
-- | mysql.infoschema | localhost | caching_sha2_password | $A$005$THISISACOMBINATIONOFINVALIDSALTANDPASSWORDTHATMUSTNEVERBRBEUSED |
-- | mysql.session    | localhost | caching_sha2_password | $A$005$THISISACOMBINATIONOFINVALIDSALTANDPASSWORDTHATMUSTNEVERBRBEUSED |
-- | mysql.sys        | localhost | caching_sha2_password | $A$005$THISISACOMBINATIONOFINVALIDSALTANDPASSWORDTHATMUSTNEVERBRBEUSED |
-- | root             | localhost | auth_socket           | emad                                                                   |
-- +------------------+-----------+-----------------------+------------------------------------------------------------------------+

```

:::

::: tip 关于授权
https://dev.mysql.com/doc/refman/8.4/en/grant.html
:::

## 权限

::: code-group

```bash [部署]
chown mysql:mysql -R /server/mysql /server/data /server/logs/mysql /server/etc/mysql
find /server/mysql /server/logs/mysql /server/etc/mysql -type f -exec chmod 640 {} \;
find /server/mysql /server/logs/mysql /server/etc/mysql -type d -exec chmod 750 {} \;
chmod 700 /server/data
chmod 750 -R /server/mysql/bin
```

```bash [开发]
# 权限同部署环境
# 开发用户 emad 加入lnpp包用户组
usermod -G php-fpm,nginx,postgres,mysql,redis emad
```

:::
