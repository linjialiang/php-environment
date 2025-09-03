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

    "/server/nginx"
    "/server/logs/nginx"
    "/server/etc/nginx/custom"

    "/server/php"
    "/server/php/84"
    "/server/php/74"
    "/server/php/tools"
    "/server/logs/php"

    "/server/postgres"
    "/server/pgData"
    "/server/logs/postgres"
    "/server/logs/postgres/wal_archive"
    "/server/etc/postgres"
    "/server/etc/postgres/tls"

    "/server/sites"
    "/server/sites/ssl"

    "/server/redis"
    "/server/redis/rdbData"
    "/server/logs/redis"
    "/server/etc/redis"
    "/server/etc/redis/tls"
    "/server/etc/redis/config"
    "/server/etc/redis/config/custom"

    "/server/mysql"
    "/server/data"
    "/server/logs/mysql"
    "/server/etc/mysql"

    "/server/sqlite"
)

echo "-----开始创建server目录-----"
for((i=0;i<${#server_array[*]};i++));
do
   echo ${server_array[i]}
   func_create ${server_array[i]}
done
echo "-----server目录创建结束 -----"
