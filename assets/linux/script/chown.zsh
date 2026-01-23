#!/usr/bin/env bash

FuncRunPower(){
    echo "----- 开始设置 $2 相关目录权限 -----"
    chown $2:$2 -R $1
    chmod 750 -R $1
    find $1 -type f -exec chmod 640 {} \;
}

wwwItems=(
    "/www"
);

sqlite3Items=(
    "/server/sqlite"
);

redisItems=(
    "/server/redis"
    "/server/redis/rdbData"
    "/server/logs/redis"
    "/server/etc/redis"
);

postgresItems=(
    "/server/postgres"
    "/server/pgData"
    "/server/logs/postgres"
    "/server/etc/postgres"
);

phpItems=(
    "/server/php"
    "/server/logs/php"
);

nginxItems=(
    "/server/nginx"
    "/server/logs/nginx"
    "/server/etc/nginx"
    "/server/sites"
);

mysqlItems=(
    "/server/mysql"
    "/server/data"
    "/server/logs/mysql"
    "/server/etc/mysql"
);

# 循环
for item in "${wwwItems[@]}"; do
    FuncRunPower "$item" 'www'
done
for item in "${sqlite3Items[@]}"; do
    FuncRunPower "$item" 'sqlite3'
done
for item in "${redisItems[@]}"; do
    FuncRunPower "$item" 'redis'
done
for item in "${postgresItems[@]}"; do
    FuncRunPower "$item" 'postgres'
done
for item in "${phpItems[@]}"; do
    FuncRunPower "$item" 'php'
done
for item in "${nginxItems[@]}"; do
    FuncRunPower "$item" 'nginx'
done
for item in "${mysqlItems[@]}"; do
    FuncRunPower "$item" 'mysql'
done