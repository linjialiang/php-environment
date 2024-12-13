<?php

namespace Psr\Log;

/**
 * 描述一个日志记录器实例
 *
 * 该消息必须实现一个__toString()的字符串或者对象.
 *
 * 该消息可能包含以下形式的占位符: {foo}
 * foo 将会被关键词 "foo"中的上下文数据替换.
 *
 * 上下文数组可以包含任意数据, 我们只能假设代码实现者
 * 如果给出一个生成堆栈跟踪的异常实例, 那么它的键名
 * 必须为 "exception"。
 *
 * 请前往 https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md
 * 查看完整的接口规范.
 */
interface LoggerInterface
{
    /**
     * 系统无法使用。
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function emergency($message, array $context = array());

    /**
     * 必须立即采取行动。
     *
     * 例如: 整个网站宕机了，数据库挂了，等等。 这应该
     * 发送短信通知警告你.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function alert($message, array $context = array());

    /**
     * 临界条件。
     *
     * 例如: 应用组件不可用，意外的异常。
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function critical($message, array $context = array());

    /**
     * 运行时错误不需要马上处理，
     * 但通常应该被记录和监控。
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function error($message, array $context = array());

    /**
     * 例外事件不是错误。
     *
     * 例如: 使用过时的API，API使用不当，不合理的东西不一定是错误。
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function warning($message, array $context = array());

    /**
     * 正常但重要的事件.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function notice($message, array $context = array());

    /**
     * 有趣的事件.
     *
     * 例如: 用户登录，SQL日志。
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function info($message, array $context = array());

    /**
     * 详细的调试信息。
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function debug($message, array $context = array());

    /**
     * 可任意级别记录日志。
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return void
     */
    public function log($level, $message, array $context = array());
}
