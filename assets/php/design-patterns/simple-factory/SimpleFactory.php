<?php
// +----------------------------------------------------------------------
// | 简单工厂类
// +----------------------------------------------------------------------
// | Copyright (c) 2023-2024 linjialiang All rights reserved.
// +----------------------------------------------------------------------
// | Author: linjialiang <linjialiang@163.com>
// +----------------------------------------------------------------------
// | CreateTime: 2023-09-12 16:15:02
// +----------------------------------------------------------------------
declare(strict_types=1);

include __DIR__ . '/Apple.php';
include __DIR__ . '/Huawei.php';

class SimpleFactory
{
    public static function createProduct(string $type): Phone
    {
        return match ($type) {
            'apple' => new Apple(),
            'huawei' => new Huawei(),
            default => null,
        };
    }
}