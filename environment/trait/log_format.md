### 10. 访问日志模板

nginx 的 `ngx_http_log_module` 模块中有个 `log_format` 指令，它可以自定义访问日志格式。

#### 1. 指令语法：

`log_format 日志模板名 [escape=default|json|none];`

| `escape`值 | 说明                                   |
| ---------- | -------------------------------------- |
| `default`  | 默认的转义方式                         |
| `json`     | 使用 JSON 标准的转义方式               |
| `none`     | 禁用转义，性能稍好，但可能破坏日志格式 |

#### 2. 默认模板：

```nginx
log_format combined '$remote_addr - $remote_user [$time_local] '
                    '"$request" $status $body_bytes_sent '
                    '"$http_referer" "$http_user_agent"';
```

#### 3. 指令位置：也叫配置上下文

`log_format` 指令只能放置在 `http { ... }` 配置块中

#### 4. 可用变量：有两类变量可用：

1. **Common variables**：nginx 通用变量，如：$uri，$args 等
2. **Log-writing-only variables**：日志记录专用变量，如：$request_time, $bytes_sent 等

#### 5. 如何记录响应头：

-   格式：`$sent_http_<header_name>`
-   `<header_name>` 需要转换为小写，并且任何连字符 `-` 需要转换为下划线 `_`
-   例如：

    ```md
    -   记录 `Content-Type` 响应头，使用 `$sent_http_content_type`
    -   记录 `X-Request-Id` 响应头，使用 `$sent_http_x_request_id`
    ```

#### 6. 常用变量

::: details 一、核心请求信息变量
这些变量描述了 HTTP 请求最基本的信息。

| 变量名           | 中文说明                                       | 示例                              |
| ---------------- | ---------------------------------------------- | --------------------------------- |
| $remote_addr     | 客户端 IP 地址                                 | 192.168.1.100                     |
| $remote_user     | 客户端用户名（用于 HTTP 基础认证）             | zhangsan（如果未认证，则为 -）    |
| $request_method  | HTTP 请求方法                                  | GET, POST, PUT, DELETE            |
| $request_uri     | 完整的原始请求 URI（包含参数）                 | /api/v1/users?id=123              |
| $uri             | 当前请求的 URI（不含参数，可能经过规范化重写） | /api/v1/users                     |
| $args            | 请求中的参数（即查询字符串）                   | id=123&name=test                  |
| $query_string    | 同 $args                                       | id=123&name=test                  |
| $server_protocol | HTTP 协议版本                                  | HTTP/1.1, HTTP/2.0                |
| $request         | 完整的原始请求行                               | GET /api/v1/users?id=123 HTTP/1.1 |
| $request_length  | 请求长度（包括请求行、请求头和请求体）         | 456（单位：字节）                 |
| $request_body    | 请求体内容（通常用于 POST 请求）               | username=admin&password=123       |

:::
::: details 二、响应信息变量

这些变量描述了 Nginx 返回给客户端的响应信息。

| 变量名                    | 中文说明                                 | 示例                    |
| ------------------------- | ---------------------------------------- | ----------------------- |
| $status                   | HTTP 响应状态码                          | 200, 404, 500, 302      |
| $body_bytes_sent          | 发送给客户端的响应体字节数（不含响应头） | 1024（单位：字节）      |
| $bytes_sent               | 发送给客户端的总字节数（包括响应头）     | 1256（单位：字节）      |
| $sent_http_content_type   | 响应头：内容类型                         | application/json        |
| $sent_http_content_length | 响应头：内容长度                         | 1024                    |
| $sent_http_location       | 响应头：重定向位置（用于 3xx）           | https://new.example.com |
| $sent_http_cache_control  | 响应头：缓存控制                         | max-age=3600            |

:::
::: details 三、时间相关变量

这些变量用于记录请求处理过程中的时间点或耗时。

| 变量名        | 中文说明                                             | 示例                          |
| ------------- | ---------------------------------------------------- | ----------------------------- |
| $time_iso8601 | ISO 8601 标准格式的本地时间（推荐）                  | 2023-10-27T15:30:22+08:00     |
| $time_local   | 通用日志格式的本地时间                               | 27/Oct/2023:15:30:22 +0800    |
| $msec         | 当前时间戳（秒+毫秒，日志写入时的时间）              | 1698391822.123                |
| $request_time | 请求处理总耗时（从读到第一个字节到发完最后一个字节） | 0.156（单位：秒，精度到毫秒） |

:::
::: details 四、连接相关变量

这些变量描述了当前连接的状态和信息。

| 变量名               | 中文说明                                           | 示例                             |
| -------------------- | -------------------------------------------------- | -------------------------------- |
| $connection          | 连接序列号                                         | 123456789                        |
| $connection_requests | 当前连接上发出的请求数量（对于 HTTP/1.1 持久连接） | 5（这是该连接处理的第 5 个请求） |
| $pipe                | 请求是否通过管道（pipeline）处理                   | p（是 pipeline），.（否）        |
| $server_addr         | 服务器接收请求的 IP 地址                           | 172.16.0.10                      |
| $server_port         | 服务器接收请求的端口                               | 80, 443                          |

:::
::: details 五、客户端信息变量

这些变量描述了客户端（通常是浏览器或上游代理）发送的信息。

| 变量名                | 中文说明                                         | 示例                                                         |
| --------------------- | ------------------------------------------------ | ------------------------------------------------------------ |
| $http_user_agent      | 客户端浏览器标识（UA）                           | Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 |
| $http_referer         | 请求来源页面的 URL（从哪个链接跳转来的）         | `https://www.google.com/ `                                   |
| $http_host            | 请求头中的 Host 字段（通常是域名）               | api.example.com                                              |
| $http_x_forwarded_for | 客户端原始 IP（当 Nginx 前有代理或负载均衡器时） | 203.0.113.45, 10.0.0.1（最左边的是原始客户端 IP）            |
| $http_cookie          | 客户端发送的 Cookie                              | sessionid=abc123; username=admin                             |
| $http_x_request_id    | 自定义头：X-Request-Id                           | abc-123-def-456                                              |
| $http_accept_language | 请求头：接受语言                                 | zh-CN,zh;q=0.9,en;q=0.8                                      |

:::
::: details 六、代理与上游（Upstream）变量

这些变量在 Nginx 作为反向代理或负载均衡器时特别重要。

| 变量名                  | 中文说明                                     | 示例                                                         |
| ----------------------- | -------------------------------------------- | ------------------------------------------------------------ |
| $upstream_addr          | 上游（后端）服务器的 IP 地址和端口           | 10.0.1.20:8080                                               |
| $upstream_status        | 上游服务器返回的 HTTP 状态码                 | 200, 502                                                     |
| $upstream_response_time | 上游服务器处理请求的耗时                     | 0.102（单位：秒，精度到毫秒）                                |
| $upstream_connect_time  | 与上游服务器建立连接所花费的时间             | 0.005                                                        |
| $upstream_header_time   | 从上游服务器接收第一个响应头字节所花费的时间 | 0.108                                                        |
| $upstream_cache_status  | 响应缓存状态                                 | HIT（命中）, MISS（未命中）, BYPASS（绕过）, EXPIRED（过期） |
| $proxy_host             | proxy_pass 指令中指定的代理目标主机名        | backend_server                                               |

:::

#### 7. 日志模板案例

::: code-group

<<< @/assets/environment/source/etc/example/nginx/custom/log_format#json_log_common{nginx} [通用]
<<< @/assets/environment/source/etc/example/nginx/custom/log_format#json_log_assets{nginx} [静态资源]
<<< @/assets/environment/source/etc/example/nginx/custom/log_format#json_log_api{nginx} [代理]
<<< @/assets/environment/source/etc/example/nginx/custom/log_format#json_log_lb{nginx} [负载均衡]
<<< @/assets/environment/source/etc/example/nginx/custom/log_format#json_log_debug{nginx} [调试]

:::
