<?php

// 以下是一个可处理多命名空间的类实现示例
namespace Example;

/**
 * 一个多用途的示例实现，包括了
 * 允许多个基本目录用于单个
 * 命名空间前缀的可选功能
 *
 * 下述示例给出了一个 foo-bar 类包，系统中路径结构如下……
 *
 *     /path/to/packages/foo-bar/
 *         src/
 *             Baz.php             # Foo\Bar\Baz
 *             Qux/
 *                 Quux.php        # Foo\Bar\Qux\Quux
 *         tests/
 *             BazTest.php         # Foo\Bar\BazTest
 *             Qux/
 *                 QuuxTest.php    # Foo\Bar\Qux\QuuxTest
 *
 * ……添加路径到  \Foo\Bar\  命名空间前缀的类文件中
 * 如下所示：
 *
 *      <?php
 *      // 实例化加载器
 *      $loader = new \Example\Psr4AutoloaderClass;
 *
 *      // 注册加载器
 *      $loader->register();
 *
 *      // 为命名空间前缀注册基本路径
 *      $loader->addNamespace('Foo\Bar', '/path/to/packages/foo-bar/src');
 *      $loader->addNamespace('Foo\Bar', '/path/to/packages/foo-bar/tests');
 *
 * 下述语句会让自动加载器尝试从
 * /path/to/packages/foo-bar/src/Qux/Quux.php
 * 中加载  \Foo\Bar\Qux\Quux 类
 *
 *      <?php
 *      new \Foo\Bar\Qux\Quux;
 *
 *  下述语句会让自动加载器尝试从
 *   /path/to/packages/foo-bar/tests/Qux/QuuxTest.php
 * 中加载 \Foo\Bar\Qux\QuuxTest 类：
 *
 *      <?php
 *      new \Foo\Bar\Qux\QuuxTest;
 */
class Psr4AutoloaderClass
{
    /**
     * 关联数组，键名为命名空间前缀，键值为一个基本目录数组。
     *
     * @var array
     */
    protected $prefixes = array();

    /**
     * 通过 SPL 自动加载器栈注册加载器
     *
     * @return void
     */
    public function register()
    {
        spl_autoload_register(array($this, 'loadClass'));
    }

    /**
     * 为命名空间前缀添加一个基本目录
     *
     * @param string $prefix 命名空间前缀。
     * @param string $base_dir 命名空间下类文件的基本目录
     * @param bool $prepend 如果为真，预先将基本目录入栈
     * 而不是后续追加；这将使得它会被首先搜索到。
     * @return void
     */
    public function addNamespace($prefix, $base_dir, $prepend = false)
    {
        // 规范化命名空间前缀
        $prefix = trim($prefix, '\\') . '\\';

        // 规范化尾部文件分隔符
        $base_dir = rtrim($base_dir, DIRECTORY_SEPARATOR) . '/';

        // 初始化命名空间前缀数组
        if (isset($this->prefixes[$prefix]) === false) {
            $this->prefixes[$prefix] = array();
        }

        // 保留命名空间前缀的基本目录
        if ($prepend) {
            array_unshift($this->prefixes[$prefix], $base_dir);
        } else {
            array_push($this->prefixes[$prefix], $base_dir);
        }
    }

    /**
     * 加载给定类名的类文件
     *
     * @param string $class 合法类名
     * @return mixed 成功时为已映射文件名，失败则为 false
     */
    public function loadClass($class)
    {
        // 当前命名空间前缀
        $prefix = $class;

        // 通过完整的命名空间类名反向映射文件名
        while (false !== $pos = strrpos($prefix, '\\')) {

            // 在前缀中保留命名空间分隔符
            $prefix = substr($class, 0, $pos + 1);

            // 其余的是相关类名
            $relative_class = substr($class, $pos + 1);

            // 尝试为前缀和相关类加载映射文件
            $mapped_file = $this->loadMappedFile($prefix, $relative_class);
            if ($mapped_file) {
                return $mapped_file;
            }

            // 删除 strrpos() 下一次迭代的尾部命名空间分隔符
            $prefix = rtrim($prefix, '\\');
        }

        // 找不到映射文件
        return false;
    }

    /**
     * 为命名空间前缀和相关类加载映射文件。
     *
     * @param string $prefix 命名空间前缀
     * @param string $relative_class 相关类
     * @return mixed Boolean 无映射文件则为false，否则加载映射文件
     */
    protected function loadMappedFile($prefix, $relative_class)
    {
        // 命名空间前缀是否存在任何基本目录
        if (isset($this->prefixes[$prefix]) === false) {
            return false;
        }

        // 通过基本目录查找命名空间前缀
        foreach ($this->prefixes[$prefix] as $base_dir) {

            // 用基本目录替换命名空间前缀
            // 用目录分隔符替换命名空间分隔符
            // 给相关的类名增加 .php 后缀
            $file = $base_dir
                . str_replace('\\', '/', $relative_class)
                . '.php';

            // 如果映射文件存在，则引入
            if ($this->requireFile($file)) {
                // 搞定了
                return $file;
            }
        }

        // 找不到
        return false;
    }

    /**
     * 如果文件存在从系统中引入进来
     *
     * @param string $file 引入文件
     * @return bool 文件存在则 true 否则 false
     */
    protected function requireFile($file)
    {
        if (file_exists($file)) {
            require $file;
            return true;
        }
        return false;
    }
}
