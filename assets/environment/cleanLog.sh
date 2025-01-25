#!/usr/bin/env bash

printf "\033c"
echo_cyan(){
  printf '\033[1;36m%b\033[0m\n' "$@"
}
echo_green(){
  printf '\033[1;32m%b\033[0m\n' "$@"
}
echo_red(){
  printf '\033[1;31m%b\033[0m\n' "$@"
}
echo_yellow(){
  printf '\033[1;33m%b\033[0m\n' "$@"
}

echo_cyan "是否清理zsh_history文件(1清理/默认不清理)："
read num
if [[ "$num" = "1" ]]; then
  echo_yellow "开始清理终端历史文件文件"
  rm /home/{redis,mysqld-84,postgres,php-fpm,nginx,emad}/.{zsh,bash}_history
  rm /home/{redis,mysqld-84,postgres,php-fpm,nginx,emad}/.{z,viminfo}
  rm /home/{redis,mysqld-84,postgres,php-fpm,nginx,emad}/.zcompdump-*
  rm /root/.{zsh,bash}_history
  rm /root/.{z,viminfo}
  rm /root/.zcompdump-*
  echo_yellow "清理终端历史文件结束"
else
  echo_yellow "不清理 .zsh_history 文件"
fi

echo_red "警告⚠️：请谨慎执行此脚本！！！"
echo_yellow "清理日志需停止服务，Postgres日志也会被删除，"
echo_cyan "是否清理lnpp日志(1清理/默认不清理)："
read num1
if [ "$num1" = "1" ]; then
  echo_green "先停止服务"
  systemctl stop {redis,mysqld-84,postgres,php74-fpm,php84-fpm,nginx}.service
  echo_green "开始清理lnpp日志"
  rm /server/logs/{nginx,php,redis}/*
  rm /server/logs/postgres/*.{json,log}
  rm /server/logs/postgres/wal_archive/*
  rm /server/logs/mysql/*
  rm /server/nginx/logs/*
  echo_green "清理lnpp日志完成"
else
  echo_yellow "不清理lnpp日志"
fi

echo_cyan "是否清理Redis本地存储(1清理/默认不清理)："
read num3
if [[ "$num3" = "1" ]]; then
  echo_yellow "开始清理Redis本地存储"
  rm /server/redis/rdbData/dump.rdb
  rm /server/redis/rdbData/appendonlydir/*
  echo_yellow "清理Redis本地存储结束"
else
  echo_yellow "不清理Redis本地存储"
fi

echo_cyan "是否启动服务(1启动/默认不启动)："
read num2
if [ "$num2" = "1" ]; then
  systemctl start {redis,mysqld-84,postgres,php74-fpm,php84-fpm,nginx}.service
  echo_green "服务已重新启动"
else
  echo_yellow "未重启服务，请手动启动"
fi
