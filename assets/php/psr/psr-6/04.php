<?php

namespace Psr\Cache;

/**
 * 无效缓存参数的异常接口。
 *
 * 任何时候，一个无效参数传递到方法时，必须抛出一个实现了
 * Psr\Cache\InvalidArgumentException 的异常类。
 */
interface InvalidArgumentException extends CacheException
{
}
