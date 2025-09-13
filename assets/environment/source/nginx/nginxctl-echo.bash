echo "#\!/usr/bin/env bash

# ==============================================
# nginxctl - Nginx 站点管理工具
# 功能: 启用/禁用 Nginx 站点配置
# 作者: 地上马
# ==============================================

# 定义路径
AVAILABLE_DIR=\"/server/sites/available\"
ENABLED_DIR=\"/server/sites/enabled\"

# 颜色定义
RED='\\\033[0;31m'
GREEN='\\\033[0;32m'
YELLOW='\\\033[1;33m'
BLUE='\\\033[0;34m'
NC='\\\033[0m' # No Color

# 打印带格式的消息函数
print_success() {
    echo -e \"\${GREEN}[SUCCESS] \$1\${NC}\"
}

print_error() {
    echo -e \"\${RED}[ERROR] \$1\${NC}\"
}

print_info() {
    echo -e \"\${BLUE}[INFO] \$1\${NC}\"
}

print_warning() {
    echo -e \"\${YELLOW}[WARNING] \$1\${NC}\"
}

# 检查必要目录是否存在
check_directories() {
    if [ ! -d \"\$AVAILABLE_DIR\" ]; then
        print_error \"目录不存在: \$AVAILABLE_DIR\"
        exit 1
    fi

    if [ ! -d \"\$ENABLED_DIR\" ]; then
        print_error \"目录不存在: \$ENABLED_DIR\"
        exit 1
    fi
}

# 执行启用操作
enable_site() {
    local SITE_NAME=\$1
    local CONF_FILE=\"\${SITE_NAME}.nginx\"
    local AVAILABLE_FILE=\"\${AVAILABLE_DIR}/\${CONF_FILE}\"
    local ENABLED_FILE=\"\${ENABLED_DIR}/\${CONF_FILE}\"

    # 检查可用配置文件是否存在
    if [ ! -f \"\$AVAILABLE_FILE\" ]; then
        print_error \"配置文件 \${AVAILABLE_FILE} 不存在\"
        exit 1
    fi

    # 检查是否已启用
    if [ -L \"\$ENABLED_FILE\" ]; then
        print_warning \"站点 \${SITE_NAME} 已启用，无需重复操作\"
        exit 0
    fi

    # 创建软链接
    if ! ln -s \"\$AVAILABLE_FILE\" \"\$ENABLED_FILE\"; then
        print_error \"创建软链接失败\"
        exit 1
    fi

    print_success \"已启用站点 \${SITE_NAME}\"

    # 测试Nginx配置
    print_info \"测试Nginx配置...\"
    if ! nginx -t; then
        print_error \"Nginx 配置测试失败，请检查配置\"
        exit 1
    fi

    # 重载nginx
    print_info \"重载Nginx配置...\"
    if ! systemctl reload nginx; then
        print_error \"重载Nginx失败\"
        exit 1
    fi

    print_success \"Nginx已成功重载\"
}

# 执行禁用操作
disable_site() {
    local SITE_NAME=\$1
    local CONF_FILE=\"\${SITE_NAME}.nginx\"
    local ENABLED_FILE=\"\${ENABLED_DIR}/\${CONF_FILE}\"

    # 检查是否已启用
    if [ ! -L \"\$ENABLED_FILE\" ]; then
        print_warning \"站点 \${SITE_NAME} 未启用，无需操作\"
        exit 0
    fi

    # 删除软链接
    if ! rm \"\$ENABLED_FILE\"; then
        print_error \"删除软链接失败\"
        exit 1
    fi

    print_success \"已禁用站点 \${SITE_NAME}\"

    # 测试Nginx配置
    print_info \"测试Nginx配置...\"
    if ! nginx -t; then
        print_error \"Nginx 配置测试失败，请检查配置\"
        exit 1
    fi

    # 重载nginx
    print_info \"重载Nginx配置...\"
    if ! systemctl reload nginx; then
        print_error \"重载Nginx失败\"
        exit 1
    fi

    print_success \"Nginx已成功重载\"
}

# 显示帮助信息
show_help() {
    echo -e \"\${BLUE}nginxctl - Nginx站点管理工具\${NC}\"
    echo \"\"
    echo -e \"\${BLUE}用法:\${NC}\"
    echo -e \"  nginxctl enable <site_name>   \${GREEN}启用指定站点\${NC}\"
    echo -e \"  nginxctl disable <site_name>  \${RED}禁用指定站点\${NC}\"
    echo -e \"  nginxctl help                 \${BLUE}显示帮助信息\${NC}\"
    echo \"\"
    echo -e \"\${BLUE}示例:\${NC}\"
    echo -e \"  \${GREEN}nginxctl enable demo\${NC}\"
    echo -e \"  \${RED}nginxctl disable demo\${NC}\"
    echo \"\"
    echo -e \"\${BLUE}说明:\${NC}\"
    echo -e \"  站点配置文件应位于 \${AVAILABLE_DIR}/ 目录中\"
    echo -e \"  启用后会在 \${ENABLED_DIR}/ 创建软链接\"
}

# 主程序逻辑
main() {
    # 检查必要目录是否存在
    check_directories

    # 检查参数数量
    if [ \$# -lt 1 ]; then
        show_help
        exit 1
    fi

    ACTION=\$1
    SITE_NAME=\$2

    # 根据输入参数执行相应操作
    case \"\$ACTION\" in
        enable)
            if [ \$# -ne 2 ]; then
                print_error \"启用站点需要指定站点名称\"
                echo \"用法: nginxctl enable <site_name>\"
                exit 1
            fi
            enable_site \"\$SITE_NAME\"
            ;;
        disable)
            if [ \$# -ne 2 ]; then
                print_error \"禁用站点需要指定站点名称\"
                echo \"用法: nginxctl disable <site_name>\"
                exit 1
            fi
            disable_site \"\$SITE_NAME\"
            ;;
        help|--help|-h)
            show_help
            ;;
        *)
            print_error \"无效操作 '\${ACTION}'\"
            echo \"可用操作: enable, disable\"
            echo \"使用 'nginxctl help' 查看完整帮助\"
            exit 1
            ;;
    esac
}

# 执行主程序
main \"\$@\"
" > /usr/local/bin/nginxctl.bash