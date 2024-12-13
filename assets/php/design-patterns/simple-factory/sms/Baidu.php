<?php
// +----------------------------------------------------------------------
// | 百度云短信服务
// +----------------------------------------------------------------------
// | Copyright (c) 2023-2024 linjialiang All rights reserved.
// +----------------------------------------------------------------------
// | Author: linjialiang <linjialiang@163.com>
// +----------------------------------------------------------------------
// | CreateTime: 2023-09-12 17:00:20
// +----------------------------------------------------------------------
declare(strict_types=1);

namespace sms;

include_once __DIR__ . '/Message.php';

class Baidu implements Message
{
    public function send(string $msg): void
    {
        echo "百度云短信验证码：$msg\n";
    }
}