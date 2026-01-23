#!/usr/bin/env bash

items=(
    "/www"
    "/server"

    "/server/default"
    "/server/logs"
    "/server/etc"

    "/server/sqlite3"

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
    "/server/php/85"
    "/server/logs/php"
    "/server/etc/php"
    "/server/etc/php/85"
    "/server/etc/php/85/tools"
    "/server/etc/php/85/php-fpm.d"

    "/server/nginx"
    "/server/logs/nginx"
    "/server/logs/nginx/access"
    "/server/logs/nginx/error"
    "/server/etc/nginx"
    "/server/etc/nginx/custom"

    "/server/sites"
    "/server/sites/tls"
    "/server/sites/available"
    "/server/sites/enabled"
)

# 主循环
for item in "${items[@]}"; do
    mkdir $item
done
