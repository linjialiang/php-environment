---
title: 安装 pgsql
titleTemplate: 环境搭建教程
---

# 安装 pgsql

## 配置源 {#conf-source}

官方推荐使用源安装，这是非常好的。

只有等你对操作系统和 pgsql 有足够深的了解，再考虑自己编译吧！

```bash
apt install gnupg2 -y
echo "deb http://mirrors.ustc.edu.cn/postgresql/repos/apt bookworm-pgdg main" > /etc/apt/sources.list.d/pgdg.list
wget -qO- http://mirrors.ustc.edu.cn/postgresql/repos/apt/ACCC4CF8.asc | tee /etc/apt/trusted.gpg.d/postgresql.asc
apt update
apt install postgresql-15 -y
```

::: tip 关于版本选择
v14 有 [[中文文档]](http://www.postgres.cn/docs/14/index.html)

v15 还没有中文手册 `[2023-08-02]`
:::

## 配置

###

### 配置文件

::: code-group

```bash [停止服务]
systemctl stop postgresql.service
```

```bash [操作]
cp /etc/postgresql/15/main/postgresql.conf{,.bak}
vim /etc/postgresql/15/main/postgresql.conf
```

<<<@/assets/environment-new/source/pgsql-apt/postgresql.conf{ini} [新配置]

```bash [权限]
# 数据目录
chown postgres:postgres /server/pgsql
chmod 700 /server/pgsql

# pid unix_socket
chown postgres:postgres /server/run/pgsql
chmod 775 /server/run/pgsql
```

```bash [初始化数据]
# 先移除数据的默认位置
mv /var/lib/postgresql/15/main{,.bak}

# 再初始化数据到指定目录
su - postgres
/usr/lib/postgresql/15/bin/initdb -D /server/pgsql --auth-local peer --auth-host scram-sha-256 --no-instructions
```

<<<@/assets/environment-new/source/pgsql-apt/postgresql.conf.bak{ini} [原始配置]
:::

## 修改 systemctl 单元文件

::: code-group
<<<@/assets/environment-new/source/service/pgsql-apt/postgresql@.service{ini} [新]
<<<@/assets/environment-new/source/service/pgsql-apt/postgresql.service.bak{ini} [原始 1]
<<<@/assets/environment-new/source/service/pgsql-apt/postgresql@.service.bak{ini} [原始 2]
:::

## 创建角色

::: code-group

```bash [psql登录]
su - postgres
psql
```

```sql [创建角色]
-- 创建组角色 (该角色不可登录)
CREATE ROLE admin_group INHERIT;
-- 创建成员角色 (该角色可以登录)
CREATE USER admin WITH PASSWORD '1';
CREATE USER admin_lan WITH PASSWORD '1';
-- 为组角色添加成员角色
GRANT admin_group TO admin;
GRANT admin_group TO admin_lan;
-- 为组角色授予超级用户权限
ALTER ROLE admin_group SUPERUSER;
-- 让成员角色临时获得组权限
SET ROLE admin_group;
```

<<<@/assets/environment-new/source/pgsql-apt/pg_hba/pg_hba_1.conf{7-8 ini} [修改 pg_hba 配置]

```sql [登录]
# 本地 unix_socket 通过密码登录
psql -U admin -d postgres -W
```

```sql [删除角色]
DROP ROLE admin_group;
DROP ROLE admin;
```

:::
