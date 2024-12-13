<?php
// +----------------------------------------------------------------------
// | 设计模式 [简单工厂模式]
// +----------------------------------------------------------------------
// | Copyright (c) 2023-2024 linjialiang All rights reserved.
// +----------------------------------------------------------------------
// | Author: linjialiang <linjialiang@163.com>
// +----------------------------------------------------------------------
// | CreateTime: 2023-09-12 14:07:01
// +----------------------------------------------------------------------
declare(strict_types=1);

include __DIR__ . '/MessageFactory.php';

use sms\MessageFactory;

/**
 * 创建1个产品对象
 *  - 需要注意的是，php实例化类的时候，需要先加载类
 *  - 如果把实例化放在类前面就报错
 */
$sms = MessageFactory::createFactory('ali');
$sms->send('[123456]');
$sms = MessageFactory::createFactory('baidu');
$sms->send('[123456]');
$sms = MessageFactory::createFactory('tx');
$sms->send('[123456]');