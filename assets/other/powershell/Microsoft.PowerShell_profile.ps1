# 初始化 oh-my-posh 并载入主题
oh-my-posh init pwsh --config "$env:POSH_THEMES_PATH\robbyrussell.omp.json" | Invoke-Expression
# 加载 post-git 模块
Import-Module posh-git
# 命令预测的来源设为历史记录
Set-PSReadLineOption -PredictionSource History
# 设置编辑器模式为Vi模式:
# Set-PSReadlineOption -EditMode Vi
# 如果想区分两种模式的光标
# Set-PSReadlineOption -ViModeIndicator Cursor
# Tab 按键兼容 oh-my-posh 和 Vi 模式
Set-PSReadlineKeyHandler -Key Tab -Function MenuComplete
