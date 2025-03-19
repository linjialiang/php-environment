## 六、 MySQL

Windows 下使用多版本 MySQL 共存的方式

### 1. 目录结构

::: details 安装目录如下：

```
├─ C:\mysql ==================================================== [MySQL 安装根目录]
|   ├─ my -------------------------------------------- 配置基目录
|   |  ├─ my-56.ini -------------- 配置文件
|   |  ├─ my-56.ini -------------- 配置文件
|   |  ├─ my-57.ini -------------- 配置文件
|   |  ├─ my-80.ini -------------- 配置文件
|   |  ├─ init-80.sql ------------ 初始化sql
|   |  ├─ my-84.ini -------------- 配置文件
|   |  ├─ init-84.sql ------------ 初始化sql
|   |  └─ ...
|   |
|   ├─ temp ------------------------------------------ 临时文件基目录
|   |  ├─ mysql-56.pid ----------- pid 文件
|   |  ├─ mysql-56.pid ----------- pid 文件
|   |  ├─ mysql-57.pid ----------- pid 文件
|   |  ├─ mysql-80.pid ----------- pid 文件
|   |  ├─ mysql-84.pid ----------- pid 文件
|   |  ├─ mysql-56.sock ---------- socket 文件
|   |  ├─ mysql-56.sock ---------- socket 文件
|   |  ├─ mysql-57.sock ---------- socket 文件
|   |  ├─ mysql-80.sock ---------- socket 文件
|   |  ├─ mysql-84.sock ---------- socket 文件
|   |  └─ ...
|   |
|   ├─ logs ------------------------------------------ 日志基目录
|   |  ├─ 56 -------------------- mysql5.5日志目录
|   |  |  ├─ error.log
|   |  |  ├─ bin-log.index
|   |  |  ├─ bin-log.000001
|   |  |  └─ ...
|   |  |
|   |  ├─ 56 -------------------- mysql5.6日志目录
|   |  |  ├─ 同 56
|   |  |  └─ ...
|   |  |
|   |  ├─ 57 -------------------- mysql5.7日志目录
|   |  |  ├─ 同 56
|   |  |  └─ ...
|   |  |
|   |  ├─ 80 -------------------- mysql8.0日志目录
|   |  |  ├─ 同 56
|   |  |  └─ ...
|   |  |
|   |  ├─ 84 -------------------- mysql8.4日志目录
|   |  |  ├─ 同 56
|   |  |  └─ ...
|   |  |
|   |  └─ ...
|   |
|   ├─ product ------------------------------------------ 产品源码基目录
|   |  ├─ 56 -------------------- mysql-5.5.62-winx64 [终版]
|   |  ├─ 56 -------------------- mysql-5.6.51-winx64 [终版]
|   |  ├─ 57 -------------------- mysql-5.7.44-winx64 [终版]
|   |  ├─ 80 -------------------- mysql-8.0.41-winx64
|   |  ├─ 84 -------------------- mysql-8.4.4-winx64
|   |  └─ ...
|   |
|   └─
└─
```

:::

### 2. 初始化数据目录

Windows 版的 MySQL 在 5.7 之前未提供数据初始化的能力，但提供了初始 data 目录，建议将 data 目录备份后再使用，默认情况下 root 账户密码为空，需要自己设置。

MySQL >= 5.7 版本支持数据初始化，具体操作如下：

::: code-group

```batch [57]
C:
cd C:\mysql\57
bin\mysqld.exe --defaults-file="C:\mysql\my\my-57.ini" --initialize-insecure --console
```

```batch [80]
C:
cd C:\mysql\80
bin\mysqld.exe --defaults-file="C:\mysql\my\my-80.ini" --initialize-insecure --console
```

```batch [84]
C:
cd C:\mysql\84
bin\mysqld.exe --defaults-file="C:\mysql\my\my-84.ini" --initialize-insecure --console
```

:::

| 初始化指令              | 区别                                                                         |
| ----------------------- | ---------------------------------------------------------------------------- |
| `--initialize`          | 为 root 用户生成一个随机的临时密码， 修改后才能操作数据                      |
| `--initialize-insecure` | 不生成 root 密码，root 账户初始密码为空，<br>使用 `--skip-password` 直接登录 |

### 3. 修改密码

::: code-group

```batch [<5.7]
REM root 用户密码为空，为了安全起见，需要设置密码

C:
cd C:\mysql\55\bin
mysql -uroot -P10055
mysql> set password for root@localhost=password('123456');
mysql> flush privileges;
```

```batch [>=5.7]
C:
cd C:\mysql\57\bin

REM 初始化使用 --initialize-insecure 无密码
REM mysql -uroot -P10057 --skip-password

REM 初始化使用 --initialize 有随机密码
mysql -uroot -P10057 -p
Enter password: 随机密码

mysql> ALTER USER 'root'@'localhost' IDENTIFIED BY '123456';
mysql> flush privileges;
```

:::

### 4. 加入系统服务

::: code-group

```batch [56]
REM 安装到系统服务
C:
cd C:\mysql\56\bin
mysqld -install mysql-56 --defaults-file="C:\mysql\my\my-56.ini"

REM 启动服务
net start mysql-56
REM 关闭服务
net stop mysql-56
REM 系统服务设为手动启动
sc config mysql-56 start=demand
REM 从系统服务中移除
mysqld -remove mysql-56
```

```batch [56]
REM 安装到系统服务
C:
cd C:\mysql\56\bin
mysqld -install mysql-56 --defaults-file="C:\mysql\my\my-56.ini"

REM 启动服务
net start mysql-56
REM 关闭服务
net stop mysql-56
REM 系统服务设为手动启动
sc config mysql-56 start=demand
REM 从系统服务中移除
mysqld -remove mysql-56
```

```batch [57]
REM 安装到系统服务
C:
cd C:\mysql\57\bin
mysqld -install mysql-57 --defaults-file="C:\mysql\my\my-57.ini"

REM 启动服务
net start mysql-57
REM 关闭服务
net stop mysql-57
REM 系统服务设为手动启动
sc config mysql-57 start=demand
REM 从系统服务中移除
mysqld -remove mysql-57
```

```batch [80]
REM 安装到系统服务
C:
cd C:\mysql\80\bin
mysqld -install mysql-80 --defaults-file="C:\mysql\my\my-80.ini"

REM 启动服务
net start mysql-80
REM 关闭服务
net stop mysql-80
REM 系统服务设为手动启动
sc config mysql-80 start=demand
REM 从系统服务中移除
mysqld -remove mysql-80
```

```batch [84]
REM 安装到系统服务
C:
cd C:\mysql\84\bin
mysqld -install mysql-84 --defaults-file="C:\mysql\my\my-84.ini"

REM 启动服务
net start mysql-84
REM 关闭服务
net stop mysql-84
REM 系统服务设为手动启动
sc config mysql-84 start=demand
REM 从系统服务中移除
mysqld -remove mysql-84
```

:::

### 5. 创建远程用户

```batch
REM 请使用对应版本自带的MySQL客户端来操作
C:
cd C:\mysql\55\bin
mysql -uroot -P10055 -p
mysql> create user 'root'@'192.168.%.%' identified by '123456';
mysql> grant all privileges on *.* to 'root'@'192.168.%.%' WITH GRANT OPTION;
mysql> flush privileges;
```

::: danger ⚠️ 警告：
操作时，强烈建议 MySQL 客户端跟 MySQL 服务器为同个主版本，因为不同版本之间会存在兼容性问题
:::
