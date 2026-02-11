root ➜ ~ lt5 /server -l --no-time
Permissions Size User     Name
drwxr-xr-x     - root     /server
drwxr-x---     - mysql    ├── data
drwxr-xr-x     - root     ├── default
drwxr-xr-x     - root     ├── etc
drwxr-x---     - mysql    │   ├── mysql
drwxr-x---     - nginx    │   ├── nginx
drwxr-x---     - nginx    │   │   └── custom
drwxr-x---     - php      │   ├── php
drwxr-x---     - php      │   │   ├── 85
drwxr-x---     - php      │   │   │   ├── php-fpm.d
drwxr-x---     - php      │   │   │   │   ├── default.conf
drwxr-x---     - php      │   │   │   │   ├── ...
drwxr-x---     - php      │   │   │   └── php-fpm.conf
drwxr-x---     - php      │   │   └── tools
drwxr-x---     - postgres │   ├── postgres
drwx------     - postgres │   │   └── tls
.rw-------  1.6k postgres │   │       ├── ...
drwxr-x---     - redis    │   └── redis
drwxr-x---     - redis    │       ├── config
drwxr-x---     - redis    │       │   ├── custom
.rw-r-----   109 redis    │       │   │   ├── ...
.rw-r-----  1.5k redis    │       │   ├── redis.conf
.rw-r-----   13k redis    │       │   ├── source-full.conf
.rw-r-----  114k redis    │       │   └── source.conf
drwx------     - redis    │       └── tls
.rw-------  1.9k redis    │           ├── ...
drwxr-xr-x     - root     ├── logs
drwxr-x---     - mysql    │   ├── mysql
drwxr-x---     - mysql    │   │   └── binlog
drwxr-x---     - nginx    │   ├── nginx
drwxr-x---     - nginx    │   │   ├── access
drwxr-x---     - nginx    │   │   └── error
drwxr-x---     - php      │   ├── php
drwxr-x---     - postgres │   ├── postgres
.rw-r-----   53k postgres │   │   ├── postgres-09.json
.rw-r-----  1.0k postgres │   │   ├── postgres-09.log
.rw-------  2.8k postgres │   │   ├── ...
drwxr-x---     - postgres │   │   └── wal_archive
.rw-------   17M postgres │   │       ├── ...
drwxr-x---     - redis    │   └── redis
drwxr-xr-x     - redis    │       ├── rdbData
.rw-r-----   102 redis    │       │   └── dump.rdb
.rw-r-----  5.5k redis    │       ├── redis-server.log
.rw-r-----  5.5k redis    │       ├── redis-server.log-20260208.1770554537
.rw-r-----  5.5k redis    │       ├── ...
drwxr-x---     - mysql    ├── mysql
drwxr-x---     - nginx    ├── nginx
drwxr-xr-x     - root     ├── ohmyzsh
drwxr-xr-x     - root     │   ├── ...
drwx------     - postgres ├── pgData
drwx------     - postgres │   ├── ...
.rw-------   723 postgres │   ├── pg_hba.conf
.rw-------  5.7k postgres │   ├── pg_hba.conf.bak
.rw-------   193 postgres │   ├── pg_ident.conf
.rw-------  2.7k postgres │   ├── pg_ident.conf.bak
.rw-------     3 postgres │   ├── PG_VERSION
drwx------     - postgres │   ├── pg_wal
.rw-------   17M postgres │   │   ├── 000000010000000000000007
.rw-------   17M postgres │   │   ├── ...
drwx------     - postgres │   │   ├── archive_status
drwx------     - postgres │   │   └── summaries
.rw-------    88 postgres │   ├── postgresql.auto.conf
.rw-------   35k postgres │   ├── postgresql.conf
.rw-------   33k postgres │   ├── postgresql.conf.bak
.rw-------    52 postgres │   ├── postmaster.opts
.rw-------    88 postgres │   └── postmaster.pid
drwxr-x---     - php      ├── php
drwxr-x---     - php      │   └── 85
drwxr-x---     - postgres ├── postgres
drwxr-x---     - postgres │   ├── ...
drwxr-x---     - redis    ├── redis
drwxr-x---     - redis    │   └── bin
.rwxr-x---  6.6M redis    │       ├── redis-benchmark
lrwxrwxrwx     - redis    │       ├── redis-check-aof -> redis-server
lrwxrwxrwx     - redis    │       ├── redis-check-rdb -> redis-server
.rwxr-x---  7.5M redis    │       ├── redis-cli
lrwxrwxrwx     - redis    │       ├── redis-sentinel -> redis-server
.rwxr-x---   20M redis    │       └── redis-server
drwxr-x---     - nginx    ├── sites
drwxr-x---     - nginx    │   ├── available
drwxr-x---     - nginx    │   ├── enabled
drwxr-x---     - nginx    │   └── tls
drwxr-x---     - sqlite3  ├── sqlite3
drwxr-x---     - sqlite3  │   ├── bin
.rwxr-x---  7.3M sqlite3  │   │   └── sqlite3
drwxr-x---     - sqlite3  │   ├── include
.rw-r-----  672k sqlite3  │   │   ├── sqlite3.h
.rw-r-----   39k sqlite3  │   │   └── sqlite3ext.h
drwxr-x---     - sqlite3  │   ├── lib
.rw-r-----  8.9M sqlite3  │   │   ├── libsqlite3.a
lrwxrwxrwx     - sqlite3  │   │   ├── libsqlite3.so -> libsqlite3.so.3.51.2
lrwxrwxrwx     - sqlite3  │   │   ├── libsqlite3.so.0 -> libsqlite3.so.3.51.2
.rw-r-----  4.9M sqlite3  │   │   ├── libsqlite3.so.3.51.2
drwxr-x---     - sqlite3  │   │   └── pkgconfig
.rw-r-----   275 sqlite3  │   │       └── sqlite3.pc
drwxr-x---     - sqlite3  │   └── share
drwxr-x---     - sqlite3  │       └── ...
.rw-r--r--     0 root     └── ...
