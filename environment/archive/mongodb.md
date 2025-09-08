---
title: 安装 MongoDB
titleTemplate: 环境搭建教程
---

# 安装 MongoDB

::: danger 警告
MongoDB 不再维护
:::

MongoDB 是最流行的非关系型数据库

## 安装

::: code-group

```bash [安装gnupg]
install gnupg curl
```

```bash [导入公共GPG密钥]
curl -fsSL https://pgp.mongodb.com/server-7.0.asc | gpg -o /usr/share/keyrings/mongodb-server-7.0.gpg --dearmor
```

```bash [添加apt源]
echo "deb [ signed-by=/usr/share/keyrings/mongodb-server-7.0.gpg ] http://repo.mongodb.org/apt/debian bookworm/mongodb-org/7.0 main" | tee /etc/apt/sources.list.d/mongodb-org-7.0.list
```

```bash [安装]
apt update
apt install -y mongodb-org
```

:::
