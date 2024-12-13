<?php
// 声明函数
function foo()
{
    // 函数主体部分
}

// 条件声明 **不** 属于「副作用」
if (!function_exists('bar')) {
    function bar()
    {
        // 函数主体部分
    }
}
