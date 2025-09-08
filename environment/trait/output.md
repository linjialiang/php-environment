### 二、输出重定向说明

在 Linux 系统中，输出重定向是一个非常重要的功能，它允许你将命令的输出内容（包括标准输出和标准错误输出）重定向到文件、其他命令或丢弃，而不是默认显示在终端上。掌握输出重定向可以帮助你更好地控制命令的输出，记录日志，调试程序等。

#### 基本概念

在 Linux 中，每个命令通常有以下三种输出流：

| 流类型       | 文件描述符 | 含义           | 默认输出位置 | 英文别名                  |
| ------------ | :--------: | -------------- | ------------ | ------------------------- |
| 标准输出     |     1      | 正常输出结果   | 终端         | Standard Output，`stdout` |
| 标准错误输出 |     2      | 错误或警告信息 | 终端         | Standard Error，`stderr`  |
| 标准输入     |     0      | 输入来源       | 键盘输入     | Standard Input，`stdin`   |

输出重定向主要涉及 `stdout`（1）和 `stderr`（2），通过重定向符号可以将这些输出流重定向到文件或其他地方。

#### 常用输出重定向符号

|  符号  | 含义                                     | 示例                       |
| :----: | ---------------------------------------- | -------------------------- |
|  `>`   | 将 stdout 重定向到文件（覆盖）           | command `>` file           |
|  `>>`  | 将 stdout 重定向到文件（追加）           | command `>>` file          |
|  `2>`  | 将 stderr 重定向到文件（覆盖）           | command `2>` file          |
| `2>>`  | 将 stderr 重定向到文件（追加）           | command `2>>` file         |
|  `&>`  | 将 stdout 和 stderr 重定向到文件（覆盖） | command `&>` file          |
| `&>>`  | 将 stdout 和 stderr 重定向到文件（追加） | command `&>>` file         |
| `2>&1` | 将 stderr 重定向到 stdout（覆盖）        | command `2>&1` file        |
|  `\|`  | 管道符号，传递信息给另 1 个命令 ​​       | `ls -l / \| grep 'server'` |
| `tee`  | 将数据同时写入到文件和终端               | command `\|` `tee` file    |

#### 案例展示

::: code-group

```bash [仅显示stderr]
# 终端输出错误和警告，文件记录正常信息
cmake \
-DWITH_DEBUG=ON \
-DCMAKE_INSTALL_PREFIX=/server/mysql \
-DWITH_SYSTEMD=ON \
-DFORCE_COLORED_OUTPUT=ON \
-DWITH_MYSQLX=OFF \
-DWITH_UNIT_TESTS=OFF \
-DINSTALL_MYSQLTESTDIR= \
.. > stdout.log
```

```bash [仅记录stderr]
# 终端输出正常信息，文件记录错误和警告
cmake \
-DWITH_DEBUG=ON \
-DCMAKE_INSTALL_PREFIX=/server/mysql \
-DWITH_SYSTEMD=ON \
-DFORCE_COLORED_OUTPUT=ON \
-DWITH_MYSQLX=OFF \
-DWITH_UNIT_TESTS=OFF \
-DINSTALL_MYSQLTESTDIR= \
.. 2> stdout.log
```

:::
