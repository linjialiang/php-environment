<?php
declare(strict_types=1);

/**
 * 超高性能 PHP 探针 - 专为 PHP 8.4+ 深度优化
 */

// 设置字符编码和时区
header('Content-Type: text/html; charset=UTF-8');
header('X-Powered-By: PHP-Probe/3.0');
date_default_timezone_set('Asia/Shanghai');

// 定义探针版本和常量
define('PROBE_VERSION', '3.0');
define('PROBE_START_TIME', hrtime(true));

// 预定义颜色常量
const COLOR_SUCCESS = '#28a745';
const COLOR_DANGER = '#dc3545';
const COLOR_WARNING = '#ffc107';
const COLOR_INFO = '#17a2b8';
const COLOR_PRIMARY = '#2c3e50';
const COLOR_SECONDARY = '#4ca1af';

// 使用枚举定义探针状态 (PHP 8.1+)
enum ProbeStatus {
    case SUCCESS;
    case ERROR;
    case WARNING;
    case INFO;
}

// 使用 readonly 类存储配置 (PHP 8.2+)
readonly class ProbeConfig {
    public function __construct(
        public string $timezone = 'Asia/Shanghai',
        public string $version = '3.0',
        public int $benchmarkIterations = 500000
    ) {}
}

final class PHPProbe {
    private static ?ProbeConfig $config = null;
    private static array $cache = [];

    public static function init(ProbeConfig $config): void {
        self::$config = $config;
    }

    // 获取客户端IP (使用 match 表达式)
    public static function getClientIP(): string {
        return match(true) {
            !empty($_SERVER['HTTP_X_FORWARDED_FOR']) => $_SERVER['HTTP_X_FORWARDED_FOR'],
            !empty($_SERVER['HTTP_CLIENT_IP']) => $_SERVER['HTTP_CLIENT_IP'],
            default => $_SERVER['REMOTE_ADDR'] ?? 'Unknown'
        };
    }

    // 格式化字节大小
    public static function formatBytes(int $bytes, int $precision = 2): string {
        static $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        if ($bytes <= 0) return '0 B';

        $base = log($bytes) / log(1024);
        $pow = min((int)$base, count($units) - 1);
        $bytes /= (1024 ** $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    // 检测扩展支持 (使用枚举返回状态)
    public static function checkExtension(string $ext): ProbeStatus {
        return extension_loaded($ext) ? ProbeStatus::SUCCESS : ProbeStatus::ERROR;
    }

    // 获取状态HTML
    public static function getStatusHTML(ProbeStatus $status, string $text): string {
        $class = match($status) {
            ProbeStatus::SUCCESS => 'success',
            ProbeStatus::ERROR => 'danger',
            ProbeStatus::WARNING => 'warning',
            ProbeStatus::INFO => 'info'
        };

        $icon = match($status) {
            ProbeStatus::SUCCESS => '✓',
            ProbeStatus::ERROR => '✗',
            ProbeStatus::WARNING => '⚠',
            ProbeStatus::INFO => 'ℹ'
        };

        return sprintf('<span class="%s">%s %s</span>', $class, $icon, $text);
    }

    // 高性能基准测试
    public static function benchmark(): string {
        $start = hrtime(true);

        // 优化的计算测试 - 使用更有效的算法
        $result = 0;
        $iterations = self::$config->benchmarkIterations;

        for ($i = 0; $i < $iterations; $i++) {
            $result += $i ** 0.5;
        }

        $elapsed = (hrtime(true) - $start) / 1e6;
        return number_format($elapsed, 2) . ' ms';
    }

    // 获取系统信息 (使用静态缓存)
    public static function getSystemInfo(): array {
        if (!empty(self::$cache['system_info'])) {
            return self::$cache['system_info'];
        }

        $info = [
            '服务器时间' => date('Y-m-d H:i:s'),
            '服务器域名' => $_SERVER['SERVER_NAME'] ?? 'N/A',
            '服务器IP' => $_SERVER['SERVER_ADDR'] ?? gethostbyname($_SERVER['SERVER_NAME'] ?? 'localhost'),
            '服务器端口' => $_SERVER['SERVER_PORT'] ?? 'N/A',
            '服务器操作系统' => php_uname('s') . ' ' . php_uname('r'),
            '服务器架构' => php_uname('m'),
            'Web服务器' => $_SERVER['SERVER_SOFTWARE'] ?? 'N/A',
            '客户端IP' => self::getClientIP(),
            '请求时间' => date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME'] ?? time()),
            '运行用户' => get_current_user() ?: (function_exists('posix_getpwuid') ? posix_getpwuid(posix_geteuid())['name'] ?? 'Unknown' : 'Unknown'),
        ];

        // 尝试获取更多系统信息
        if (function_exists('sys_getloadavg')) {
            $load = sys_getloadavg();
            $info['系统负载'] = implode(', ', array_map(fn($v) => number_format($v, 2), $load));
        }

        return self::$cache['system_info'] = $info;
    }

    // 获取PHP配置信息
    public static function getPHPInfo(): array {
        if (!empty(self::$cache['php_info'])) {
            return self::$cache['php_info'];
        }

        $info = [
            'PHP版本' => PHP_VERSION,
            'PHP运行方式' => php_sapi_name(),
            'Zend引擎版本' => zend_version(),
            'PHP安装路径' => PHP_BINDIR,
            'PHP配置文件' => php_ini_loaded_file() ?: '未找到',
            '当前包含路径' => get_include_path(),
            '最大内存限制' => ini_get('memory_limit'),
            '最大执行时间' => ini_get('max_execution_time') . '秒',
            '最大上传文件大小' => ini_get('upload_max_filesize'),
            '最大POST数据大小' => ini_get('post_max_size'),
            '时区设置' => date_default_timezone_get(),
            'OPCache状态' => extension_loaded('Zend OPcache') && ini_get('opcache.enable') ? '启用' : '禁用',
            'JIT状态' => ini_get('opcache.jit') ?: '禁用',
            'PHP接口类型' => PHP_SAPI,
            'PHP整数大小' => PHP_INT_SIZE * 8 . '位',
            'PHP最大整数值' => PHP_INT_MAX,
        ];

        return self::$cache['php_info'] = $info;
    }

    // 获取性能信息
    public static function getPerformanceInfo(): array {
        if (!empty(self::$cache['performance_info'])) {
            return self::$cache['performance_info'];
        }

        $info = [
            '探针执行时间' => number_format((hrtime(true) - PROBE_START_TIME) / 1e6, 2) . ' ms',
            'CPU性能测试' => self::benchmark(),
            '内存使用峰值' => self::formatBytes(memory_get_peak_usage(true)),
            '当前内存使用' => self::formatBytes(memory_get_usage(true)),
            '真实内存使用' => self::formatBytes(memory_get_usage(false)),
            'PHP进程ID' => getmypid(),
            '内存限制使用率' => self::getMemoryUsagePercent() . '%',
        ];

        return self::$cache['performance_info'] = $info;
    }

    // 获取数据库支持信息
    public static function getDatabaseInfo(): array {
        if (!empty(self::$cache['database_info'])) {
            return self::$cache['database_info'];
        }

        $info = [];

        // PDO支持
        $info['PDO支持'] = extension_loaded('pdo')
            ? self::getStatusHTML(ProbeStatus::SUCCESS, '已启用')
            : self::getStatusHTML(ProbeStatus::ERROR, '未启用');

        // PDO驱动
        if (extension_loaded('pdo')) {
            $drivers = PDO::getAvailableDrivers();
            $info['PDO驱动'] = $drivers ? implode(', ', $drivers) : '无';
        }

        // MySQLi支持
        $info['MySQLi支持'] = extension_loaded('mysqli')
            ? self::getStatusHTML(ProbeStatus::SUCCESS, '已启用')
            : self::getStatusHTML(ProbeStatus::ERROR, '未启用');

        return self::$cache['database_info'] = $info;
    }

    // 获取PHP 8.4+ 特性支持信息
    public static function getPHP85Features(): array {
        if (!empty(self::$cache['php85_features'])) {
            return self::$cache['php85_features'];
        }

        $features = [];

        // 检查新特性支持
        $features['JIT编译器'] = ini_get('opcache.jit') ? '启用' : '禁用';
        $features['纤程(Fiber)支持'] = class_exists('Fiber') ? '支持' : '不支持';
        $features['枚举支持'] = function_exists('enum_exists') ? '支持' : '不支持';
        $features['属性注解'] = PHP_VERSION_ID >= 80000 ? '支持' : '不支持';
        $features['匹配表达式'] = PHP_VERSION_ID >= 80000 ? '支持' : '不支持';
        $features['空安全操作符'] = PHP_VERSION_ID >= 80000 ? '支持' : '不支持';
        $features['命名参数'] = PHP_VERSION_ID >= 80000 ? '支持' : '不支持';
        $features['联合类型'] = PHP_VERSION_ID >= 80000 ? '支持' : '不支持';

        return self::$cache['php85_features'] = $features;
    }

    // 获取扩展支持信息
    public static function getExtensionsInfo(): array {
        if (!empty(self::$cache['extensions_info'])) {
            return self::$cache['extensions_info'];
        }

        $extensions = [
            'GD库支持' => 'gd',
            'OpenSSL支持' => 'openssl',
            'JSON支持' => 'json',
            'MBString支持' => 'mbstring',
            'CURL支持' => 'curl',
            'ZIP支持' => 'zip',
            'XML支持' => 'xml',
            'Redis支持' => 'redis',
            'Memcached支持' => 'memcached',
            'MongoDB支持' => 'mongodb',
            'PDO支持' => 'pdo',
            'MySQLi支持' => 'mysqli',
            'SQLite支持' => 'sqlite3',
            'PostgreSQL支持' => 'pgsql',
            'ODBC支持' => 'odbc',
            'SOAP支持' => 'soap',
            'GD图像处理' => 'gd',
            'IMAP支持' => 'imap',
            'LDAP支持' => 'ldap',
            'FTP支持' => 'ftp',
            'Socket支持' => 'sockets',
            'Exif支持' => 'exif',
            'Gettext支持' => 'gettext',
            'Iconv支持' => 'iconv',
            'Intl支持' => 'intl',
            'PCRE支持' => 'pcre',
            'PDO MySQL支持' => 'pdo_mysql',
            'PDO SQLite支持' => 'pdo_sqlite',
            'PDO PostgreSQL支持' => 'pdo_pgsql',
            'PDO ODBC支持' => 'pdo_odbc',
            'APCu支持' => 'apcu',
            'XDebug支持' => 'xdebug',
            'Zend OPcache支持' => 'Zend OPcache',
        ];

        $result = [];
        foreach ($extensions as $name => $ext) {
            $result[$name] = self::checkExtension($ext) === ProbeStatus::SUCCESS
                ? self::getStatusHTML(ProbeStatus::SUCCESS, '支持')
                : self::getStatusHTML(ProbeStatus::ERROR, '不支持');
        }

        return self::$cache['extensions_info'] = $result;
    }

    // 计算内存使用百分比
    private static function getMemoryUsagePercent(): float {
        $limit = ini_get('memory_limit');
        $usage = memory_get_usage(true);

        if ($limit == -1) return 0; // 无限制

        $limitBytes = self::convertToBytes($limit);
        return round(($usage / $limitBytes) * 100, 2);
    }

    // 将内存限制字符串转换为字节
    private static function convertToBytes(string $value): int {
        $unit = strtolower(substr($value, -1));
        $bytes = (int)substr($value, 0, -1);

        return match($unit) {
            'g' => $bytes * 1024 * 1024 * 1024,
            'm' => $bytes * 1024 * 1024,
            'k' => $bytes * 1024,
            default => (int)$value
        };
    }

    // 获取所有已加载扩展
    public static function getLoadedExtensions(): array {
        if (!empty(self::$cache['loaded_extensions'])) {
            return self::$cache['loaded_extensions'];
        }

        $extensions = get_loaded_extensions();
        sort($extensions);
        return self::$cache['loaded_extensions'] = $extensions;
    }

    // 获取扩展信息统计
    public static function getExtensionStats(): array {
        $extensions = self::getLoadedExtensions();

        return [
            '总扩展数' => count($extensions),
            'Zend扩展' => count(array_filter($extensions, fn($ext) => extension_loaded($ext) && str_starts_with($ext, 'Zend'))),
            'PHP核心扩展' => count(array_filter($extensions, fn($ext) => extension_loaded($ext) && in_array($ext, ['standard', 'Core', 'date', 'pcre', 'reflection', 'SPL']))),
        ];
    }

    // 清理缓存
    public static function clearCache(): void {
        self::$cache = [];
    }
}

// 初始化配置
PHPProbe::init(new ProbeConfig());

try {
    // 收集所有信息
    $systemInfo = PHPProbe::getSystemInfo();
    $phpInfo = PHPProbe::getPHPInfo();
    $performanceInfo = PHPProbe::getPerformanceInfo();
    $databaseInfo = PHPProbe::getDatabaseInfo();
    $php85Features = PHPProbe::getPHP85Features();
    $extensionsInfo = PHPProbe::getExtensionsInfo();
    $loadedExtensions = PHPProbe::getLoadedExtensions();
    $extensionStats = PHPProbe::getExtensionStats();

    // 计算总扩展数
    $totalExtensions = count($loadedExtensions);

    // 先处理phpinfo的请求
    if (isset($_GET['phpinfo'])) {
        $phpinfoType = (int)$_GET['phpinfo'];
        match ($phpinfoType) {
            1 => phpinfo(),
            2 => phpinfo(INFO_CONFIGURATION | INFO_MODULES),
            default => header('Location: ' . $_SERVER['PHP_SELF']),
        };
        exit(0);
    }
} catch (Throwable $e) {
    // 错误处理
    header('HTTP/1.1 500 Internal Server Error');
    echo "<h1>探针错误</h1><p>{$e->getMessage()}</p>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>超高性能 PHP 探针 v<?= PROBE_VERSION ?> - PHP <?= PHP_VERSION ?></title>
    <style>
        :root {
            --primary: <?= COLOR_PRIMARY ?>;
            --secondary: <?= COLOR_SECONDARY ?>;
            --success: <?= COLOR_SUCCESS ?>;
            --danger: <?= COLOR_DANGER ?>;
            --warning: <?= COLOR_WARNING ?>;
            --info: <?= COLOR_INFO ?>;
            --light: #f8f9fa;
            --dark: #343a40;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            line-height: 1.6;
            color: #333;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            padding: 20px;
            min-height: 100vh;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 25px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            transform: rotate(30deg);
        }

        h1 {
            font-size: 2.8rem;
            margin-bottom: 10px;
            font-weight: 700;
            position: relative;
        }

        .version {
            font-size: 1.3rem;
            opacity: 0.9;
            margin-bottom: 15px;
        }

        .php-version {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            padding: 8px 20px;
            border-radius: 25px;
            font-weight: bold;
            backdrop-filter: blur(5px);
            position: relative;
        }

        .content {
            padding: 25px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .card {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .card h2 {
            color: var(--primary);
            border-bottom: 2px solid var(--secondary);
            padding-bottom: 15px;
            margin-bottom: 20px;
            font-size: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .toggle {
            background: var(--secondary);
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.85rem;
            transition: background 0.3s;
        }

        .toggle:hover {
            background: var(--primary);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        th {
            background-color: var(--light);
            font-weight: 600;
            width: 35%;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover {
            background-color: rgba(0, 0, 0, 0.01);
        }

        .success { color: var(--success); }
        .danger { color: var(--danger); }
        .warning { color: var(--warning); }
        .info { color: var(--info); }

        .extensions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 12px;
            margin-top: 15px;
        }

        .extension-item {
            background: var(--light);
            padding: 10px 15px;
            border-radius: 6px;
            font-size: 0.9rem;
            transition: background 0.3s;
        }

        .extension-item:hover {
            background: #e9ecef;
        }

        footer {
            text-align: center;
            padding: 25px;
            background: var(--primary);
            color: white;
            font-size: 0.95rem;
        }

        @media (max-width: 768px) {
            .grid {
                grid-template-columns: 1fr;
            }

            h1 {
                font-size: 2.2rem;
            }

            .content {
                padding: 15px;
            }

            th, td {
                padding: 10px;
                display: block;
                width: 100%;
            }

            th {
                background: none;
                font-weight: 700;
                padding-bottom: 5px;
            }

            tr {
                display: block;
                margin-bottom: 15px;
                border-bottom: 1px solid rgba(0, 0, 0, 0.1);
                padding-bottom: 15px;
            }

            tr:last-child {
                border-bottom: none;
                margin-bottom: 0;
                padding-bottom: 0;
            }
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-left: 8px;
        }

        .badge-success {
            background: var(--success);
            color: white;
        }

        .badge-danger {
            background: var(--danger);
            color: white;
        }

        .badge-info {
            background: var(--info);
            color: white;
        }

        .extensions-check-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .extension-check-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            border-radius: 6px;
            background: var(--light);
        }

        .extension-name {
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>高性能 PHP 探针</h1>
            <div class="version">版本 v<?= PROBE_VERSION ?> - 专为 PHP 8.4+ 优化</div>
            <div class="php-version">PHP <?= PHP_VERSION ?></div>
        </header>

        <div class="content">
            <!-- 系统信息 -->
            <div class="card">
                <h2>
                    系统信息
                    <button class="toggle" onclick="toggleSection('system-info')">显示/隐藏</button>
                </h2>
                <div id="system-info" style="display: none;">
                    <table>
                        <?php foreach ($systemInfo as $key => $value): ?>
                        <tr>
                            <th><?= $key ?></th>
                            <td><?= htmlspecialchars((string)$value) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>

            <!-- PHP配置 -->
            <div class="card">
                <h2>
                    PHP配置信息
                    <button class="toggle" onclick="toggleSection('php-info')">显示/隐藏</button>
                </h2>
                <div id="php-info" style="display: none;">
                    <table>
                        <?php foreach ($phpInfo as $key => $value): ?>
                        <tr>
                            <th><?= $key ?></th>
                            <td><?= htmlspecialchars((string)$value) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>

            <!-- 性能信息 -->
            <div class="card">
                <h2>
                    性能信息
                    <button class="toggle" onclick="toggleSection('performance-info')">显示/隐藏</button>
                </h2>
                <div id="performance-info" style="display: none;">
                    <table>
                        <?php foreach ($performanceInfo as $key => $value): ?>
                        <tr>
                            <th><?= $key ?></th>
                            <td><?= $value ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>

            <div class="grid">
                <!-- 数据库支持 -->
                <div class="card">
                    <h2>数据库支持</h2>
                    <table>
                        <?php foreach ($databaseInfo as $key => $value): ?>
                        <tr>
                            <th><?= $key ?></th>
                            <td><?= $value ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>

                <!-- PHP 8.4+ 特性 -->
                <div class="card">
                    <h2>PHP 8.4+ 特性</h2>
                    <table>
                        <?php foreach ($php85Features as $key => $value): ?>
                        <tr>
                            <th><?= $key ?></th>
                            <td>
                                <?= $value ?>
                                <?php if (strpos($value, '支持') !== false): ?>
                                <span class="badge badge-success">NEW</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>

            <!-- 扩展支持 -->
            <div class="card">
                <h2>
                    扩展支持检测
                    <button class="toggle" onclick="toggleSection('extensions-info')">显示/隐藏</button>
                </h2>
                <div id="extensions-info" style="display: none;">
                    <div class="extensions-check-grid">
                        <?php foreach ($extensionsInfo as $key => $value): ?>
                        <div class="extension-check-item">
                            <span class="extension-name"><?= $key ?></span>
                            <span><?= $value ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- 已加载扩展 -->
            <div class="card">
                <h2>
                    已加载扩展 <span class="badge badge-info"><?= $totalExtensions ?></span>
                    <button class="toggle" onclick="toggleSection('loaded-extensions')">显示/隐藏</button>
                </h2>
                <div id="loaded-extensions" style="display: none;">
                    <div class="extensions-grid">
                        <?php foreach ($loadedExtensions as $ext): ?>
                        <div class="extension-item"><?= $ext ?></div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- 更多信息 -->
            <div class="card">
                <h2>更多信息</h2>
                <p>
                    <a href="?phpinfo=1" target="_blank" class="btn">查看完整的 phpinfo()</a> |
                    <a href="?phpinfo=2" target="_blank" class="btn">查看配置信息</a>
                </p>
            </div>
        </div>

        <footer>
            高性能 PHP 探针 v<?= PROBE_VERSION ?> - 生成时间: <?= date('Y-m-d H:i:s') ?> (北京时间)
        </footer>
    </div>

    <script>
        // 切换章节显示/隐藏
        function toggleSection(id) {
            const element = document.getElementById(id);
            if (element.style.display === 'none') {
                element.style.display = 'block';
            } else {
                element.style.display = 'none';
            }
        }

        // 添加打印功能
        function printProbe() {
            window.print();
        }

        // 添加复制功能
        function copyProbeInfo() {
            const content = document.documentElement.outerHTML;
            navigator.clipboard.writeText(content)
                .then(() => alert('探针信息已复制到剪贴板'))
                .catch(err => console.error('复制失败:', err));
        }
    </script>
</body>
</html>