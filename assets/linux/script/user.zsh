#!/usr/bin/env bash

# 用户列表
users=(
  "2001:sqlite3"
  "2002:redis"
  "2003:postgres"
  "2005:php"
  "2006:nginx"
  "2007:mysql"
);

# 函数定义
funcCreateUser() {
  local uid username
  IFS=':' read -r uid username <<< "$1"

  echo "正在创建用户:  $username (UID: $uid)"

  groupadd -g $uid $username
  useradd -c "$username related process user" \
  -g $username -u $uid -s /sbin/nologin -m $username
  cp -r /root/{.oh-my-zsh,.zshrc} /home/$username
  chown $username:$username -R /home/$username/{.oh-my-zsh,.zshrc}
}

# 主循环
for user in "${users[@]}"; do
    funcCreateUser "$user"
done

# 新版本开始使用tcp/ip转发，并不需要考虑socket文件转发相关的权限问题
# php-fpm 主进程非特权用户时，需要考虑如下问题：
# nginx 如果是通过 sock 文件代理转发给 php-fpm，
#   php-fpm 主进程创建 sock 文件时需要确保 nginx 子进程用户有读写 sock 文件的权限
# 方式1：采用 sock 文件权限 php-fpm:nginx 660 (nginx 权限较少，php-fpm 权限较多)
# usermod -G nginx php-fpm
# 方式2：采用 sock 文件权限 php-fpm:php-fpm 660 (nginx 权限较多，php-fpm 权限较少)
# usermod -G php-fpm nginx

# php编译pgsql扩展，使用指定Postgres安装目录时，需提供读取libpg目录的权限
# php使用到SQLite3的pkgconfig时，需提供读取SQLite3头和库文件的权限
usermod -a -G postgres,sqlite php-fpm

# 部署环境注释，开发环境取消注释，开发用户追加附属组，其中emad指开发用户
# - 部署环境不需要开发用户，可直接使用 nginx 用户作为 ftp、ssh 等上传工具的用户
usermod -a -G emad nginx
usermod -a -G emad php-fpm
usermod -a -G sqlite,redis,postgres,php-fpm,nginx,mysql emad
