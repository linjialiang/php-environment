<?php
declare(strict_types=1);

/**
 * 高性能 PHP 探针 - 专为 PHP 8.4+ 优化
 * 修复时区问题，提高执行效率
 */

// 设置字符编码和时区
header('Content-Type: text/html; charset=UTF-8');
header('X-Powered-By: PHP-Probe/2.0');
date_default_timezone_set('Asia/Shanghai');

// 定义探针版本
define('PROBE_VERSION', '2.1');
define('PROBE_START_TIME', microtime(true));

// 获取客户端IP (PHP 8.4+)
function get_client_ip(): string {
    return $_SERVER['HTTP_X_FORWARDED_FOR'] 
        ?? $_SERVER['HTTP_CLIENT_IP'] 
        ?? $_SERVER['REMOTE_ADDR'] 
        ?? 'Unknown';
}

// 格式化字节大小
function format_bytes(int $bytes, int $precision = 2): string {
    static $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    
    if ($bytes <= 0) return '0 B';
    
    $base = log($bytes) / log(1024);
    $pow = min((int)$base, count($units) - 1);
    $bytes /= (1024 ** $pow);
    
    return round($bytes, $precision) . ' ' . $units[$pow];
}

// 检测扩展支持
function check_extension(string $ext): string {
    return extension_loaded($ext) 
        ? '<span class="success">✓ 支持</span>' 
        : '<span class="danger">✗ 不支持</span>';
}

// 高性能基准测试
function benchmark(): string {
    $start = hrtime(true);
    
    // 优化的计算测试
    $result = 0;
    for ($i = 0; $i < 500000; $i++) {
        $result += $i ** 0.5;
    }
    
    $elapsed = (hrtime(true) - $start) / 1e6; // 转换为毫秒
    return number_format($elapsed, 2) . ' ms';
}

// 获取系统信息
function get_system_info(): array {
    static $info;
    
    if ($info !== null) return $info;
    
    $info = [
        '服务器时间' => date('Y-m-d H:i:s'),
        '服务器域名' => $_SERVER['SERVER_NAME'] ?? 'N/A',
        '服务器IP' => $_SERVER['SERVER_ADDR'] ?? gethostbyname($_SERVER['SERVER_NAME'] ?? 'localhost'),
        '服务器端口' => $_SERVER['SERVER_PORT'] ?? 'N/A',
        '服务器操作系统' => php_uname('s') . ' ' . php_uname('r'),
        '服务器架构' => php_uname('m'),
        'Web服务器' => $_SERVER['SERVER_SOFTWARE'] ?? 'N/A',
        '客户端IP' => get_client_ip(),
        '请求时间' => date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME'] ?? time()),
    ];
    
    return $info;
}

// 获取PHP配置信息
function get_php_info(): array {
    static $info;
    
    if ($info !== null) return $info;
    
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
        'OPCache状态' => extension_loaded('Zend OPcache') && (ini_get('opcache.enable') ? '启用' : '禁用'),
        'JIT状态' => ini_get('opcache.jit') ?: '禁用',
    ];
    
    return $info;
}

// 获取性能信息
function get_performance_info(): array {
    static $info;
    
    if ($info !== null) return $info;
    
    $info = [
        '探针执行时间' => number_format((microtime(true) - PROBE_START_TIME) * 1000, 2) . ' ms',
        'CPU性能测试' => benchmark(),
        '内存使用峰值' => format_bytes(memory_get_peak_usage(true)),
        '当前内存使用' => format_bytes(memory_get_usage(true)),
        '真实内存使用' => format_bytes(memory_get_usage(false)),
        'PHP进程ID' => getmypid(),
    ];
    
    return $info;
}

// 获取数据库支持信息
function get_database_info(): array {
    static $info;
    
    if ($info !== null) return $info;
    
    $info = [];
    
    // PDO支持
    $info['PDO支持'] = extension_loaded('pdo') 
        ? '<span class="success">✓ 已启用</span>' 
        : '<span class="danger">✗ 未启用</span>';
    
    // PDO驱动
    if (extension_loaded('pdo')) {
        $drivers = PDO::getAvailableDrivers();
        $info['PDO驱动'] = $drivers ? implode(', ', $drivers) : '无';
    }
    
    // MySQLi支持
    $info['MySQLi支持'] = extension_loaded('mysqli') 
        ? '<span class="success">✓ 已启用</span>' 
        : '<span class="danger">✗ 未启用</span>';
    
    return $info;
}

// 获取关键扩展信息
function get_extensions_info(): array {
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
    ];
    
    $result = [];
    foreach ($extensions as $name => $ext) {
        $result[$name] = check_extension($ext);
    }
    
    return $result;
}

// 获取所有已加载扩展
function get_loaded_extensions_list(): array {
    $extensions = get_loaded_extensions();
    sort($extensions);
    return $extensions;
}

// 获取PHP 8.4+ 特定功能支持
function get_php84_features(): array {
    $features = [];
    
    // 检查新特性支持
    $features['JIT编译器'] = ini_get('opcache.jit') ? '启用' : '禁用';
    $features['纤程(Fiber)支持'] = class_exists('Fiber') ? '支持' : '不支持';
    $features['枚举支持'] = function_exists('enum_exists') ? '支持' : '不支持';
    $features['属性注解'] = PHP_VERSION_ID >= 80000 ? '支持' : '不支持';
    $features['匹配表达式'] = PHP_VERSION_ID >= 80000 ? '支持' : '不支持';
    $features['空安全操作符'] = PHP_VERSION_ID >= 80000 ? '支持' : '不支持';
    
    return $features;
}

// 主程序
try {
    // 收集所有信息
    $systemInfo = get_system_info();
    $phpInfo = get_php_info();
    $performanceInfo = get_performance_info();
    $databaseInfo = get_database_info();
    $extensionsInfo = get_extensions_info();
    $php84Features = get_php84_features();
    $loadedExtensions = get_loaded_extensions_list();
    
    // 计算总扩展数
    $totalExtensions = count($loadedExtensions);
    
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
    <title>高性能 PHP 探针 v<?= PROBE_VERSION ?> - PHP <?= PHP_VERSION ?></title>
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #4ca1af;
            --success: #28a745;
            --danger: #dc3545;
            --warning: #ffc107;
            --info: #17a2b8;
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
                <div id="system-info">
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
                <div id="php-info">
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
                <div id="performance-info">
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
                        <?php foreach ($php84Features as $key => $value): ?>
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
                <div id="extensions-info">
                    <div class="grid">
                        <?php foreach ($extensionsInfo as $key => $value): ?>
                        <div class="card">
                            <h3><?= $key ?></h3>
                            <p><?= $value ?></p>
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
                <div id="loaded-extensions">
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
                <?php
                if (isset($_GET['phpinfo'])) {
                    if ($_GET['phpinfo'] == 1) {
                        phpinfo();
                        exit;
                    } elseif ($_GET['phpinfo'] == 2) {
                        phpinfo(INFO_CONFIGURATION | INFO_MODULES);
                        exit;
                    }
                }
                ?>
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