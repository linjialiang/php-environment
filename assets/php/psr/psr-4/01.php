<?php

/**
 * 一个具体项目实现的示例。
 *
 * 在注册自动加载函数后，下面这行代码将引发程序
 * 尝试从 /path/to/project/src/Baz/Qux.php
 * 加载 \Foo\Bar\Baz\Qux 类：
 *
 *      new \Foo\Bar\Baz\Qux;
 *
 * @param string $class 完全标准的类名。
 * @return void
 */
spl_autoload_register(function ($class) {

    // 具体项目的命名空间前缀
    $prefix = 'Foo\\Bar\\';

    // 命名空间前缀对应的基础目录
    $base_dir = __DIR__ . '/src/';

    // 该类使用了此命名空间前缀？
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // 否，交给下一个已注册的自动加载函数
        return;
    }

    // 获取相对类名
    $relative_class = substr($class, $len);

    // 命名空间前缀替换为基础目录，
    // 将相对类名中命名空间分隔符替换为目录分隔符，
    // 附加 .php
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // 如果文件存在，加载它
    if (file_exists($file)) {
        require $file;
    }
});
