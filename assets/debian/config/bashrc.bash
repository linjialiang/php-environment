# ~/.bashrc

# 结尾新增的内容
# MY_IP=`hostname -I`
# PS1='[${debian_chroot:+($debian_chroot)}\u@${MY_IP} \W]\$ ' 这是用户
PS1='[${debian_chroot:+($debian_chroot)}\u \W]\$ '

# eval "`dircolors`"
# export LS_OPTIONS='--color=auto'
# alias ls="ls ${LS_OPTIONS} -F"
# alias lsa="ls ${LS_OPTIONS} -aF"
# alias ll="ls ${LS_OPTIONS} -lF"
# alias lla="ls ${LS_OPTIONS} -laF"
alias l="exa -galFihH --sort=Name"
alias la="exa -aF"
alias bat='batcat'
alias cls='clear'
PATH=${PATH}:/usr/sbin:/usr/local/sbin:sbin
PATH=${PATH}:/server/nginx/sbin
PATH=${PATH}:/server/postgres/bin
PATH=${PATH}:/server/php/83/bin:/server/php/83/sbin
PATH=${PATH}:/server/node/bin
PATH=${PATH}:/server/mysql/bin
PATH=${PATH}:/server/redis/bin
PATH=${PATH}:/server/sqlite/bin
export PATH
