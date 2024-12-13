<?php
// +----------------------------------------------------------------------
// | 阿里云短信服务
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

class Ali implements Message
{
    public function send(string $msg): void
    {
        echo "阿里云短信验证码：$msg\n";
    }
}