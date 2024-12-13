<?php
// +----------------------------------------------------------------------
// | 短信服务器商工厂类
// +----------------------------------------------------------------------
// | Copyright (c) 2023-2024 linjialiang All rights reserved.
// +----------------------------------------------------------------------
// | Author: linjialiang <linjialiang@163.com>
// +----------------------------------------------------------------------
// | CreateTime: 2023-09-12 17:03:07
// +----------------------------------------------------------------------
declare(strict_types=1);

namespace sms;

include __DIR__ . '/Ali.php';
include __DIR__ . '/Tx.php';
include __DIR__ . '/Baidu.php';

class MessageFactory
{
    public static function createFactory($type): Baidu|Ali|Tx|null
    {
        return match ($type) {
            'ali' => new Ali(),
            'baidu' => new Baidu(),
            'tx' => new Tx(),
            default => null,
        };
    }
}