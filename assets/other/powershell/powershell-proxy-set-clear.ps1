# NOTE: registry keys for IE 8, may vary for other versions
$regPath = 'HKCU:\Software\Microsoft\Windows\CurrentVersion\Internet Settings'

function ClearProxy
{
    # 这是针对 浏览器
    Set-ItemProperty -Path $regPath -Name ProxyEnable -Value 0
    Set-ItemProperty -Path $regPath -Name ProxyServer -Value ''
    Set-ItemProperty -Path $regPath -Name ProxyOverride -Value ''

    # 这是针对 终端
    [Environment]::SetEnvironmentVariable('http_proxy', $null, 'User')
    [Environment]::SetEnvironmentVariable('https_proxy', $null, 'User')
}

function SetProxy
{
    $proxy = 'http://127.0.0.1:1080'

    # 这是针对 浏览器
    # Set-ItemProperty -Path $regPath -Name ProxyEnable -Value 1
    # Set-ItemProperty -Path $regPath -Name ProxyServer -Value $proxy
    # Set-ItemProperty -Path $regPath -Name ProxyOverride -Value '<local>'

    # 这是针对 终端
    [Environment]::SetEnvironmentVariable('http_proxy', $proxy, 'User')
    [Environment]::SetEnvironmentVariable('https_proxy', $proxy, 'User')
}

# 执行代理清理
ClearProxy

# 执行代理设置
# SetProxy
