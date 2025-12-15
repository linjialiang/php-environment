#!/usr/bin/env bash
# 创建 postgres 用户
groupadd -g 2003 postgres
useradd -c 'postgres service main process user' \
-g postgres -u 2003 -s /sbin/nologin -m postgres
cp -r /root/{.oh-my-zsh,.zshrc} /home/postgres
chown postgres:postgres -R /home/postgres/{.oh-my-zsh,.zshrc}

# 创建 redis 用户
groupadd -g 2005 redis
useradd -c 'redis service main process user' -g redis -u 2005 -s /sbin/nologin -m redis
cp -r /root/{.oh-my-zsh,.zshrc} /home/redis
chown redis:redis -R /home/redis/{.oh-my-zsh,.zshrc}

# 创建 php-fpm 用户
groupadd -g 2006 php-fpm
useradd -c 'php-fpm service main process user' \
-g php-fpm -u 2006 -s /sbin/nologin -m php-fpm
cp -r /root/{.oh-my-zsh,.zshrc} /home/php-fpm
chown php-fpm:php-fpm -R /home/php-fpm/{.oh-my-zsh,.zshrc}

# 创建 nginx 用户
groupadd -g 2007 nginx
useradd -c 'nginx service main process user' \
-g nginx -u 2007 -s /sbin/nologin -m nginx
cp -r /root/{.oh-my-zsh,.zshrc} /home/nginx
chown nginx:nginx -R /home/nginx/{.oh-my-zsh,.zshrc}

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
usermod -a -G postgres php-fpm

# 部署环境注释，开发环境取消注释，开发用户追加附属组，其中emad指开发用户
# - 部署环境不需要开发用户，可直接使用 nginx 用户作为 ftp、ssh 等上传工具的用户
usermod -a -G emad nginx
usermod -a -G emad php-fpm
usermod -a -G postgres,php-fpm,nginx emad
