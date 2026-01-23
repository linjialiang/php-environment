#!/usr/bin/env bash

FuncRunPower(){
    local DirPerm filePerm
    # 判断权限
    if [[ "$2" == "root" ]]; then
        DirPerm=755
        filePerm=644
    else
        DirPerm=750
        filePerm=640
    fi
    chown "$2":"$2" -R "$1"
    find "$1" -type d -exec chmod $DirPerm {} \;
    find "$1" -type f -exec chmod $filePerm {} \;
    echo "目录 '$1' 权限已设置为 目录:$DirPerm 文件:$filePerm"
}

rootItems=(
    '/server'
    '/server/default'
);

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
for item in "${rootItems[@]}"; do
    FuncRunPower "$item" 'root'
done
for item in "${wwwItems[@]}"; do
    FuncRunPower "$item" 'emad'
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