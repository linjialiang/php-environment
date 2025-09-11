---
title: 编译安装 nginx
titleTemplate: 环境搭建教程
---

# 编译安装 nginx

Nginx 是现如今性能最强劲的 Web 服务器及反向代理服务器

## 构建前准备

### 1. 依赖项

```bash
apt install -y libgd-dev libgeoip-dev
```

### 2. CAP_NET_BIND_SERVICE

`CAP_NET_BIND_SERVICE` 是 Linux 内核中的一个能力（capability），它允许进程绑定低于 `1024` 的端口。这个能力通常用于网络服务程序，如 Web 服务器、邮件服务器等，以便它们能够监听系统保留的低端口。

在 Linux 系统中，只有 root 用户和具有 `CAP_NET_BIND_SERVICE` 能力的进程才能绑定低于 `1024` 的端口。为了给一个应用程序设置这个能力，可以使用 setcap 命令。

本次 nginx 的 master 进程用户是 nginx 而非 root，所以需要为程序设置 `CAP_NET_BIND_SERVICE`

::: code-group

```bash [启用CAP_NET_BIND_SERVICE能力]
setcap cap_net_bind_service=+eip /server/nginx/sbin/nginx
```

```bash [移除CAP_NET_BIND_SERVICE能力]
setcap -r /server/nginx/sbin/nginx
```

:::

## 开始构建

### 编译安装

::: code-group

```bash [构建目录]
su - nginx -s /bin/zsh
tar -xzf nginx-1.28.0.tar.gz
tar -xzf openssl-3.5.2.tar.gz
tar -xjf pcre2-10.45.tar.bz2
tar -xJf zlib-1.3.1.tar.xz
mkdir ~/nginx-1.28.0/build_nginx
cd ~/nginx-1.28.0
```

<<<@/assets/environment/source/nginx/build_now.bash [构建选项]

```bash [编译安装]
# 4核以上可以使用 make -j4 编译
make -j4 > make.log
# 不挂起，后台执行
nohup make -j4 &
# 安装
make install
```

<<<@/assets/environment/source/nginx/build_all.bash [全部构建选项]

```bash [查看帮助]
cd /home/nginx/nginx-1.28.0
./configure --help > help.ini
```

<<<@/assets/environment/source/nginx/help.ini [全部参数]

:::

### 测试安装结果

使用 curl 检测是否成功

::: code-group

```bash [common]
/server/nginx/sbin/nginx
curl -I 127.0.0.1
```

```bash [成功信号]
HTTP/1.1 200 OK
Server: nginx/1.22.1
Date: Tue, 30 Aug 2022 11:50:51 GMT
Content-Type: text/html
Content-Length: 615
Last-Modified: Tue, 30 Aug 2022 11:50:36 GMT
Connection: keep-alive
ETag: "630df98c-267"
Accept-Ranges: bytes
```

```bash [失败信号]
curl: (7) Failed to connect to 127.0.0.1 port 80: 拒绝连接
```

:::

## 平滑升级

Nginx 可实现平滑升级，具体操作如下：

### 1. 构建指令

构建指令参考 [开始构建](#开始构建) 基本一样

::: warning 注意
平滑升级时，新版 nginx 只需要执行编译，不要执行安装
:::

### 2. 替换主文件

```bash
# 先备份旧版主文件，防止意外出现
mv /server/nginx/sbin/nginx{,.bak}
# 拷贝新的主文件,到指定目录
cp -p -r /home/nginx/nginx-1.26.3/build_nginx/nginx /server/nginx/sbin/
```

### 3. 升级操作

::: code-group

```bash [1.查看 pid]
# 通过 ps 指令，检查旧版 nginx 的 pid
ps -ef|grep -E "nginx|PID" |grep -v grep
ps aux|grep -E "nginx|PID" |grep -v grep
# 通过 cat 查看 pid 文件，并记录进程 id 号
cat /run/nginx/nginx.pid
```

```bash [2.启动新版主文件]
# 通过 `kill -USR2 <pid>` 启动新版 nginx 可执行文件
kill -USR2 `cat /run/nginx/nginx.pid`
```

```bash [3.关闭旧版进程]
# 使用 `kill -WINCH <pid>` 来关闭旧版 nginx 进程
kill -WINCH <old_nginx_pid>
```

:::

::: tip 提示
`kill -WINCH <pid>` 指令可实现：当进程没有访问者时，系统自动关闭当前进程
:::

## 配置

nginx 配置仅有一个入口文件，统一称做 `主配置文件`

`主配置文件` 通过 `include` 指令加载其它文件中的配置信息，统一称做 `子配置文件`

下面是一些具有代表性的配置文件，提供参考：

### 1. 主配置文件 {#main-conf}

::: code-group
<<<@/assets/environment/source/nginx/nginx.conf{nginx} [配置文件]
<<<@/assets/environment/source/etc/example/nginx/main.nginx{nginx} [主配置]
<<<@/assets/environment/source/etc/example/nginx/custom/default.nginx [默认站点]
<<<@/assets/environment/source/etc/example/nginx/custom/php_forward{nginx} [php 文件转发]
:::

### 2. fastcgi 参数模版

nginx 自带了两个 fastcgi 参数模版，这里推荐使用官方最新的 `fastcgi.conf`

如果不符合项目需求，还可以自定义 fastcgi 参数配置模板
::: tip 移除 PATH_INFO
nginx+php-fpm 不论如何配置，PATH_INFO 始终为空，暂无法解决，而且对业务来讲 PATH_INFO 并没有提供任何优秀的作用，所以决定移除 PATH_INFO
:::

### 3. 缓存模板

站点不经常变动的静态文件，可以让客户端缓存，以减轻服务器压力
::: code-group
<<<@/assets/environment/source/etc/example/nginx/custom/cache{nginx} [统一缓存模板案例]
<<<@/assets/environment/source/etc/example/nginx/custom/no_cache{nginx} [禁用缓存模板案例]
:::

### 4. 开启压缩

nginx 支持对文件开启 gzip 压缩，以加快网络传输速度，下面是 html 缓存模板案例：

<<<@/assets/environment/source/etc/example/nginx/custom/gzip{nginx}

### 5. 限制请求数量

nginx 通过 `http 区块` 和 `server 区块` 结合可以限制请求数量

::: code-group
<<<@/assets/environment/source/etc/example/nginx/custom/limit_req_http{nginx} [http 区块]
<<<@/assets/environment/source/etc/example/nginx/custom/limit_req_server{nginx} [server 区块]

::: tip 提示
server 区块里的 `zone=with_ip` 对应 http 区块里的 `$binary_remote_addr` ，可以直接限制同 ip 地址的访问频率

对于不同站点(server 区块) ，设置可能各不相同，直接在站点文件中设置即可
:::

### 6. 文件禁止访问

`nginx server` 可以对特定文件和目录进行访问限制

<<<@/assets/environment/source/etc/example/nginx/custom/no_access{nginx}

### 7. 跨域请求

`nginx server` 可以配置跨域请求，跨域请求没有单独文件，按需写入对应站点的 `location 区块`

<<<@/assets/environment/source/etc/example/nginx/custom/cross_domain{nginx}

### 8. 站点配置案例

::: code-group
<<<@/assets/environment/source/nginx/sites/example.nginx [配置说明]
<<<@/assets/environment/source/nginx/sites/static.nginx [静态站点]
<<<@/assets/environment/source/nginx/sites/tp.nginx [tp 站点]
<<<@/assets/environment/source/nginx/sites/qy.nginx [qy 站点]
<<<@/assets/environment/source/nginx/sites/laravel.nginx [laravel 站点]
:::

### 9. SSL 证书权限

SSL 证书的存放目录及其文件的权限只应该让需要的程序看到，比如只让 nginx 的主进程用户读取

```bash
# 查看 nginx 主进程用户
ps -ef|grep -E "nginx|PID"|grep -E "master|PID"|grep -v grep
ps aux|grep -E "nginx|PID"|grep -E "master|PID"|grep -v grep
```

::: tip 设置 SSL 证书权限

本次测试环境的 nginx 主进程用户是 nginx 用户

```bash
chown nginx -R /server/ssl/
chmod 700 /server/ssl/
chmod 400 /server/ssl/*
```

:::

## 管理

### 1. 内置指令

nginx 内置管理指令

| common                             |     info     |
| ---------------------------------- | :----------: |
| /server/nginx/sbin/nginx           |     启动     |
| /server/nginx/sbin/nginx -s quit   |   正常关闭   |
| /server/nginx/sbin/nginx -s stop   |   快速关闭   |
| /server/nginx/sbin/nginx -s reload |   重新载入   |
| /server/nginx/sbin/nginx -s reopen | 重新打开日志 |
| /server/nginx/sbin/nginx -t        | 检测配置文件 |
| /server/nginx/sbin/nginx -h        | 显示帮助信息 |
| /server/nginx/sbin/nginx -T        | 列出配置信息 |

### 2. 指定配置文件

nginx 可以在运行时指定配置文件入口：

```bash
/server/nginx/sbin/nginx -c /server/nginx/conf/nginx.conf
```

检测指定的 Nginx 配置文件：

```bash
/server/nginx/sbin/nginx -t -c /server/nginx/conf/nginx.conf
```

### 3. 强制停止

linux 可以强制停止 Nginx 进程

```bash
pkill -9 nginx
```

## Systemd

linux 服务器推荐使用 `Systemd 单元(Unit)` 来管理守护进程，下面为 Nginx 添加 Systemd

### 1. 添加 Systemd 流程

::: code-group

<<<@/assets/environment/source/service/nginx.service{ini} [案例]

```bash [重载]
# 重新载入 Systemd 配置
systemctl daemon-reload
# nginx.service 加入开机启动
systemctl enable nginx.service
```

:::

### 2. 管理 Systemd

| common                         | info                    |
| ------------------------------ | ----------------------- |
| systemctl start nginx.service  | 立即启动 nginx 服务     |
| systemctl stop nginx.service   | 立即停止 nginx 服务     |
| systemctl reload nginx.service | 重新加载 nginx 服务配置 |
| systemctl status nginx.service | 检查 nginx 状态         |

## 用户身份认证

`nginx server` 通过 `ngx_http_auth_basic_module` 模块下的 `HTTP Basic Authentication (HTTP 基本身份验证)` 协议，可以设置客户端通过验证用户名和密码来访问网站资源

nginx 的用户身份认证，仅区分登录用户和非登录用户的访问差异，没有其它更复杂的权限认证功能

### 1. 配置

`ngx_http_auth_basic_module` 模块，只有 2 条配置指令：

#### auth_basic 指令

该指令，用于控制 nginx 是否开启 `HTTP 基本身份验证` 功能

-   语法 : auth_basic string | off;
-   默认 : auth_basic off；
-   区块 : http 、 server 、 location 、 limit_except

> 语法分析：

-   off : 表示关闭 `HTTP 基本身份验证` 功能，这是默认值
-   string : 任意字符串，表示开启 `HTTP 基本身份验证` 功能，并且字符串会作为登录提示信息出现

> 区块说明：

-   通常不会在 http 区块上开启 `HTTP 基本身份验证` 功能，除非服务器上所有网站都仅供内部访问
-   server 和 location 区块是最常使用的
-   当然 `HTTP 基本身份验证` 功能，只有非常简单的站点需要使用它，比如：纯静态资源站或者其它没有认证系统的网站

::: warning 注意
在同一区块，仅一个 `auth_basic` 生效，后面覆盖前面
:::

#### auth_basic_user_file 指令

该指令，用于指定保存用户名和密码的文件

-   语法 : auth_basic_user_file file;
-   默认 : —
-   区块 : http 、 server 、 location 、 limit_except

> 指定保存用户名和密码的文件，格式如下：

```
＃ 备注说明
用户名1:加密密码1
用户名2:加密密码2:用户2说明
用户名3:加密密码3
```

> 区块说明：基本跟 auth_basic 指令一致

::: warning 注意
在同一区块，`auth_basic_user_file` 仅一个生效，后面覆盖前面
:::

### 2. 生成加密密码

主要使用以下加密类型生成密码：

1.  `crypt()` : 标准 Unix 密码算法
2.  `MD5-based password algorithm (apr1)` : 基于 MD5 的密码算法，Apache 变体

> 提示：`apr1` 加密类型是 nginx 支持的加密方式中最安全的

对生成密码的工具没有特殊要求，只要加密方式正确，nginx 就可以正确验证

事实上，nginx 是通过 `--with-openssl=xxx` 自己编译的 openssl 外库来验证的

本次我们就直接使用 openssl passwd 来演示生成加密密码，具体如下：

::: code-group

```bash [创建必要文件]
# 创建目录
mkdir /server/default/nginx_auth
# 创建两个站点的认证文件，文件名与站点名相似
touch /server/default/nginx_auth/{public,qydoc,wangdoc}
# 权限 nginx 读
chown root:nginx /server/default/nginx_auth/{public,qydoc,wangdoc}
chmod 640 /server/default/nginx_auth/{public,qydoc,wangdoc}
```

```bash [默认站点身份认证]
# 用户1 emad 密码 123
echo -n 'emad:' >>  /server/default/nginx_auth/public
openssl passwd -apr1 123 >> /server/default/nginx_auth/public
```

```bash [qydoc 站点身份认证]
# 用户1 emad 密码 123
echo -n 'emad:' >>  /server/default/nginx_auth/qydoc
openssl passwd -apr1 123 >> /server/default/nginx_auth/qydoc

# 用户2 qydoc 密码 qy123
echo -n 'qydoc:' >>  /server/default/nginx_auth/qydoc
openssl passwd -apr1 qy123 >> /server/default/nginx_auth/qydoc
```

```bash [wangdoc 站点身份认证]
# 用户1 emad 密码 123
echo -n 'emad:' >>  /server/default/nginx_auth/wangdoc
openssl passwd -apr1 123 >> /server/default/nginx_auth/wangdoc

# 用户2 wangdoc 密码 wd123
echo -n 'wangdoc:' >>  /server/default/nginx_auth/wangdoc
openssl passwd -apr1 wd123 >> /server/default/nginx_auth/wangdoc
```

:::

### 3. 站点配置

nginx 虚拟主机配置 `HTTP 基本身份验证` 案例

::: code-group

```nginx [默认站点]
server
{
    ...
    # 整个站点启用身份验证
    auth_basic           "default site";
    auth_basic_user_file /server/default/nginx_auth/public;
}
```

```nginx [qydocs 站点]
server
{
    ...
    # 整个站点启用身份验证
    location /
    {
        ...
        auth_basic           "qydocs 站点";
        auth_basic_user_file /server/default/nginx_auth/qydocs;
        ...
    }

    # /other/ 目录下禁用身份验证
    location ^~ /other/
    {
        auth_basic  false;
    }
    ...
}
```

```nginx [wangdocs 站点]
server
{
    ...
    # 指定目录下启用身份验证
    location ~ ^/(?:css|html|javascript)/
    {
        ...
        auth_basic           "wangdocs 站点";
        auth_basic_user_file /server/default/nginx_auth/wangdocs;
        ...
    }
    ...
}
```

:::

## 权限

::: code-group

```bash [部署]
chown nginx:nginx -R /server/{nginx,sites}
find /server/{nginx,sites} -type f -exec chmod 640 {} \;
find /server/{nginx,sites} -type d -exec chmod 750 {} \;
# conf和sbin目录下的内容权限 nginx 640
# 其他的*_temp不是很重要
# 可执行文件需要执行权限
chmod 750 -R /server/nginx/sbin

chown nginx:nginx -R /server/logs/nginx
chmod 750 /server/logs/nginx
# -- 因为日志文件权限是 nginx 644

# 每次对 sbin 修改权限后，都需要重新启用CAP_NET_BIND_SERVICE能力
setcap cap_net_bind_service=+eip /server/nginx/sbin/nginx
```

```bash [开发]
# 权限同部署环境
# 开发用户 emad 加入lnpp包用户组
usermod -a -G sqlite,redis,postgres,mysql,php-fpm,nginx emad
```

:::
