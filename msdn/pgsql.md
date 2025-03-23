## 八、PostgreSQL

::: details 下载 zip 存档步骤
下载页面 https://www.postgresql.org/download/windows/

![zip存档列表](/assets/iis/pgsql/1.jpg)
![选择版本](/assets/iis/pgsql/2.jpg)
:::

### 1. 目录结构

::: details 安装目录如下：

```
├─ C:\mysql ==================================================== [MySQL 安装根目录]
|   ├─ data -------------------------------------------- 数据基目录
|   |  ├─ 17 -------------------- postgresql 17 数据
|   |  |  ├─
|   |  |  ├─
|   |  |  └─ ...
|   |  |
|   |  └─ ...
|   |
|   ├─ product ------------------------------------------ 产品源码基目录
|   |  ├─ 17 -------------------- postgresql-17.4-1
|   |  |  ├─
|   |  |  ├─
|   |  |  └─ ...
|   |  |
|   |  └─ ...
|   |
|   └─
└─
```

:::

## 2. 初始化数据库

::: code-group

```ps1 [17]
C:
cd C:\pgsql\product\17
.\bin\initdb.exe -D "C:\pgsql\data\17" -E UTF8 --locale=zh_CN.utf8 -U postgres -W
```

:::
