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

# 用户与组的关系
usermod -a -G postgres,sqlite3 php
usermod -a -G sqlite3,redis,postgres,php,nginx,mysql emad

# 开发者创建的文件 [nginx 工作进程用户] 和 [php 工作进程用户] 需要能读取
usermod -a -G emad nginx
usermod -a -G emad php
