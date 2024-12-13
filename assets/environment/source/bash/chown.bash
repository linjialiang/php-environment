#!/usr/bin/env bash
func_chown_nginx(){
    chown nginx:nginx -R $1
}

func_chown_phpFpm(){
    chown php-fpm:php-fpm -R $1
}

func_chown_postgres(){
    chown postgres:postgres -R $1
}

func_chown_www(){
    chown emad:emad -R $1
}

func_chown_redis(){
    chown redis:redis -R $1
}

func_chown_mysql(){
    chown mysql:mysql -R $1
}

func_chown_sqlite(){
    chown sqlite:sqlite -R $1
}

chown_nginx_array=(
    "/server/nginx"
    "/server/logs/nginx"
    "/server/sites"
);

chown_phpFpm_array=(
    "/server/php"
    "/server/logs/php"
);

chown_postgres_array=(
    "/server/postgres"
    "/server/pgData"
    "/server/logs/postgres"
);

chown_www_array=(
    "/www"
);

chown_redis_array=(
    "/server/redis"
    "/server/redis/rdbData"
    "/server/logs/redis"
);

chown_mysql_array=(
    "/server/mysql"
    "/server/data"
    "/server/logs/mysql"
    "/server/etc/mysql"
);

chown_sqlite_array=(
    "/server/sqlite"
);

echo "-----开始设置nginx用户权限目录-----"
for((i=0;i<${#chown_nginx_array[*]};i++));
do
   echo ${chown_nginx_array[i]}
   func_chown_nginx ${chown_nginx_array[i]}
done
echo "-----nginx用户权限目录设置结束-----"

echo "-----开始设置 php-fpm 用户权限目录-----"
for((i=0;i<${#chown_phpFpm_array[*]};i++));
do
   echo ${chown_phpFpm_array[i]}
   func_chown_phpFpm ${chown_phpFpm_array[i]}
done
echo "-----php-fpm 用户权限目录设置结束-----"

echo "-----开始设置 postgres 用户权限目录-----"
for((i=0;i<${#chown_postgres_array[*]};i++));
do
   echo ${chown_postgres_array[i]}
   func_chown_postgres ${chown_postgres_array[i]}
done
echo "-----postgres 用户权限目录设置结束-----"

echo "-----开始设置 开发者 用户权限目录-----"
for((i=0;i<${#chown_www_array[*]};i++));
do
   echo ${chown_www_array[i]}
   func_chown_www ${chown_www_array[i]}
done
echo "-----开发者 用户权限目录设置结束-----"

echo "-----开始设置 redis 用户权限目录-----"
for((i=0;i<${#chown_redis_array[*]};i++));
do
   echo ${chown_redis_array[i]}
   func_chown_redis ${chown_redis_array[i]}
done
echo "-----redis 用户权限目录设置结束-----"

echo "-----开始设置 mysql 用户权限目录-----"
for((i=0;i<${#chown_mysql_array[*]};i++));
do
   echo ${chown_mysql_array[i]}
   func_chown_mysql ${chown_mysql_array[i]}
done
echo "-----mysql 用户权限目录设置结束-----"

echo "-----开始设置 sqlite 用户权限目录-----"
for((i=0;i<${#chown_sqlite_array[*]};i++));
do
   echo ${chown_sqlite_array[i]}
   func_chown_sqlite ${chown_sqlite_array[i]}
done
echo "-----sqlite 用户权限目录设置结束-----"
