#!/usr/bin/env bash

# mkdir.bash
func_create(){
    mkdir $1
}

server_array=(
    "/www"
    "/server"
    "/server/default"
    "/server/logs"
    "/server/etc"

    "/server/sqlite"

    "/server/redis"
    "/server/redis/rdbData"
    "/server/logs/redis"
    "/server/etc/redis"
    "/server/etc/redis/tls"
    "/server/etc/redis/config"
    "/server/etc/redis/config/custom"

    "/server/postgres"
    "/server/pgData"
    "/server/logs/postgres"
    "/server/logs/postgres/wal_archive"
    "/server/etc/postgres"
    "/server/etc/postgres/tls"

    "/server/mysql"
    "/server/data"
    "/server/logs/mysql"
    "/server/logs/mysql/binlog"
    "/server/etc/mysql"

    "/server/php"
    "/server/php/84"
    "/server/php/tools"
    "/server/logs/php"

    "/server/nginx"
    "/server/logs/nginx"
    "/server/logs/nginx/access"
    "/server/logs/nginx/error"
    "/server/etc/nginx"
    "/server/etc/nginx/custom"

    "/server/sites"
    "/server/sites/ssl"
    "/server/sites/available"
    "/server/sites/enabled"
)

echo "-----开始创建server目录-----"
for((i=0;i<${#server_array[*]};i++));
do
   echo ${server_array[i]}
   func_create ${server_array[i]}
done
echo "-----server目录创建结束 -----"
