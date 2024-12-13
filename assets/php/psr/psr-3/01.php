<?php

/**
 * 用上下文信息替换记录信息中的占位符
 */
function interpolate(string $message, array $context = [])
{
    // 构建一个花括号包含的键名的替换数组
    $replace = [];
    foreach ($context as $key => $val) {
        // 检查该值是否可以转换为字符串
        if (!is_array($val) && (!is_object($val) || method_exists($val, '__toString'))) {
            $replace['{' . $key . '}'] = $val;
        }
    }

    // 替换记录信息中的占位符，最后返回修改后的记录信息。
    return strtr($message, $replace);
}

// 含有带花括号占位符的记录信息。
$message = "User {username} created";

// 带有替换信息的上下文数组，键名为占位符名称，键值为替换值。
$context = ['username' => 'bolivar'];

// 输出 "User bolivar created"
echo interpolate($message, $context);
