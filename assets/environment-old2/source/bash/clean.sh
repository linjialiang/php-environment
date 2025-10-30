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

echo_cyan "删除/var/cache/apt/archives/下所有包文件"
apt clean
echo_cyan "仅删除过期的缓存文件"
apt autoclean
echo_cyan "删除不再被依赖的软件包"
apt autoremove --purge
echo_cyan "立即清空日志"
rm -rf /var/log/*

echo_cyan "是否清理终端历史文件(1清理/默认不清理)："
read num
if [[ "$num" = "1" ]]; then
  echo_yellow "开始清理终端历史文件"
  rm /home/emad/.{zsh,bash}_history
  rm /home/emad/.{z,viminfo}
  rm /home/emad/.zcompdump-*
  rm /root/.{zsh,bash}_history
  rm /root/.{z,viminfo}
  rm /root/.zcompdump-*
  echo_yellow "清理终端历史文件结束"
else
  echo_yellow "不清理终端历史文件"
fi
