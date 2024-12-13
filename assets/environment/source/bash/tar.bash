#!/usr/bin/env bash

# 获取当前脚本的路径
SCRIPT_PATH="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

for i in `ls *.tar.gz`;
do
    echo "gz解压" $i
    tar -xzf $i
done

for i in `ls *.tar.bz2`;
do
    echo "bz2解压" $i
    tar -xjf $i
done

for i in `ls *.tar.xz`;
do
    echo "xz解压" $i
    tar -xJf $i
done

if [ -d "${SCRIPT_PATH}/php_ext" ]; then
    EXT_DIR="${SCRIPT_PATH}/php_ext"
    cd ${EXT_DIR}
    for i in `ls *.tgz`;
    do
        echo "tgz解压" $i
        tar -xzf $i
    done

    cd ${EXT_DIR}
    rm package.xml
fi
