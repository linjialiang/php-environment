## 用户说明

在用户脚本中我们创建了多个用户，其中 `Nginx` 和 `PHP-FPM` 进程和用户关系比较复杂：

::: code-group

```md [用户]
| 用户名   | 说明            |
| -------- | --------------- |
| emad     | 开发者用户      |
| sqlite   | SQLite3 用户    |
| redis    | Redis 用户      |
| postgres | PostgreSQL 用户 |
| mysql    | MySQL 用户      |
| php-fpm  | PHP-FPM 用户    |
| nginx    | Nginx 用户      |
```

```md [进程]
| process        | user    |
| -------------- | ------- |
| Nginx master   | nginx   |
| Nginx worker   | nginx   |
| PHP-FPM master | php-fpm |
| PHP-FPM pool   | php-fpm |

> Nginx 主进程：

master 进程用户需要有 worker 进程用户的全部权限，master 进程用户类型：

1.  特权用户(root)：worker 进程可以指定为其它非特权用户；
2.  非特权用户：worker 进程跟 master 进程是同一个用户。

> Nginx 工作进程：

-   worker 进程负责处理实际的用户请求；
-   代理转发和接收代理响应都是由 worker 进程处理。

> Nginx 配置文件 `user` 指令限制说明：

-   主进程是特权用户(root)：`user` 指令是有意义，用于指定工作进程用户和用户组；
-   主进程是非特权用户：`user` 指令没有意义，会被 Nginx 程序忽略掉。

> PHP-FPM 主进程：

-   master 进程负责管理 pool 进程；
-   master 进程创建和管理 pool 进程的 sock 文件；
-   master 进程需要有 pool 进程用户的全部权限，master 进程用户类型：

    1.  特权用户（root）：pool 进程可以指定为其它非特权用户
    2.  非特权用户：pool 进程用户跟 master 进程用户相同

> PHP-FPM 工作池进程：

-   pool 进程独立地处理请求，执行 PHP 脚本代码；
-   pool 进程处理完 PHP 代码后，会直接将结果返回给客户端。
```

```md [nginx代理转发]
> 具体流程如下：

1. Nginx master 进程：当有新的请求到来时，
   master 进程会将其分配给一个 worker 进程来处理。
2. Nginx worker 进程：如果请求是静态资源，则直接返回给客户端；如果请求是 PHP 文件，
   则通过 `PHP-FPM pool` 进程的 sock 文件将请求转发给 PHP-FPM 进行处理。
3. PHP-FPM master 进程：当收到 `PHP-FPM pool` 进程的 sock 文件传递的请求时，
   master 进程会将其分配给一个 pool 进程来处理。
4. PHP-FPM pool 进程：pool 进程执行和处理 PHP 代码，
   并将返回结果发送给对应的 sock 文件
5. 返回结果：Nginx 的 worker 进程，接收 sock 文件的响应内容，
   再将处理后的动态内容返回给客户端。
```

```md [套接字权限]
> nginx 站点代理转发 php 请求时：

-   Nginx 主进程用户不需要对 PHP-FPM 的 socket 文件拥有任何权限，
    处理请求的是工作进程；
-   Nginx 工作进程用户需要对 PHP-FPM 的 socket 文件具有 `读+写` 权限；

    1. `r` 权限：Nginx 工作进程需要读取 socket 文件以发送请求到 PHP-FPM。
    2. `w` 权限：Nginx 工作进程也需要写入权限，以便接收来自 PHP-FPM 的响应。

-   PHP-FPM 主进程用户需要对 sock 文件具有全部权限；

    1. 创建/删除 pool 进程的 sock 文件。
    2. 监听指定的端口或 Unix 套接字文件，以便接收来自 Web 服务器（如 Nginx）的请求。

> PHP-FPM 的套接字文件：

-   pool 进程的 sock 文件监听用户: `php-fpm`；
-   pool 进程的 sock 文件监听用户组: `nginx`；
-   pool 进程的 sock 文件监听权限: `0660`；
-   用户 php-fpm 的附属用户组增加 `nginx`。
```

```md [TCP/IP权限]
> 使用 tcp/ip 的方式建立代理转发通道时，nginx 站点代理转发 php 请求时：

-   Nginx 的主进程和工作进程均不需要拥有 php-fpm 相关的权限；
-   PHP-FPM 主进程和工作进程同样不需要拥有 nginx 相关的权限。
```

:::

### 用户职责

-   `用户 nginx` 是 Nginx worker 进程的 Unix 用户
-   `用户 php-fpm` 是 PHP-FPM 子进程的 Unix 用户
-   `用户 emad` 是开发者操作项目资源、文件的用户

### 用户权限

::: code-group

```md [nginx]
1. 对静态文件需提供 `读` 的权限
2. 对 php-fpm 的 `unix socket` 文件提供了读写的权限
```

```md [客户端]
客户端（如浏览器）使用 nginx 用户浏览网站：

1. 加载静态文件;
2. php-fpm 的 `unix socket` 文件传输
    - 使用 socket 转发，nginx 用户可作为 FPM 的监听用户，
      如：监听 socket、连接 web 服务器，权限设为 `660`
    - 使用 `TCP/IP` 转发是，PHP-FPM 无需监听用户
```

```md [php-fpm]
1. FPM 进程运行的 Unix 用户，对 php 脚本、php 所需的配置文件需要 `读` 的权限；
2. 当 php 需要操作文件或目录时，需要提供 `读+写` 权限：
    - 如：框架中记录运行时日志、缓存的 runtime 目录，就需要 `读+写` 全新
3. 除此以外，php-fpm 用户通常不需要其他权限
```

```md [emad]
-   开发环境：需要对 php 文件、静态文件有 `读+写` 的权限;
-   部署环境：平时可以不提供任何权限，因为该用户与服务没有关联;
-   部署环境：对需要变动的文件，需要具有`读+写`的权限;

> 说明：emad 泛指开发者账户，你可以取其它名字
```

:::

::: tip
新版 lnmpp 将采用 `TCP/IP` 代理转发方式，所以 socket 文件权限不需要过多考虑
:::
