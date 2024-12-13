<?php
// 「副作用」：修改 ini 配置
ini_set('error_reporting', E_ALL);

// 「副作用」：引入文件
include "file.php";

// 「副作用」：生成输出
echo "<html>\n";

// 声明函数
function foo()
{
    // function body
}
