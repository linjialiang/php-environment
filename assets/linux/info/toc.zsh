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
drwxr-x---     - php      │   │   └── 85
drwxr-x---     - php      │   │       ├── php-fpm.d
drwxr-x---     - php      │   │       └── tools
drwxr-x---     - postgres │   ├── postgres
drwx------     - postgres │   │   └── tls
.rw-------  1.6k postgres │   │       ├── client-admin.crt
.rw-------  1.7k postgres │   │       ├── client-admin.key
.rw-------  1.6k postgres │   │       ├── client-replication.crt
.rw-------  1.7k postgres │   │       ├── client-replication.key
.rw-------  1.6k postgres │   │       ├── client.crt
.rw-------  1.7k postgres │   │       ├── client.key
.rw-------   163 postgres │   │       ├── openssl.cnf
.rw-------  1.5k postgres │   │       ├── pgsql.crt
.rw-------   428 postgres │   │       ├── pgsql.dh
.rw-------  1.7k postgres │   │       ├── pgsql.key
.rw-------  1.9k postgres │   │       ├── root.crt
.rw-------  3.3k postgres │   │       ├── root.key
.rw-------    39 postgres │   │       ├── root.txt
.rw-------  1.6k postgres │   │       ├── server.crt
.rw-------  1.7k postgres │   │       └── server.key
drwxr-x---     - redis    │   └── redis
drwxr-x---     - redis    │       ├── config
drwxr-x---     - redis    │       │   ├── custom
.rw-r-----   109 redis    │       │   │   ├── 01-network.conf
.rw-r-----   423 redis    │       │   │   ├── 02-tls.conf
.rw-r-----   251 redis    │       │   │   ├── 03-general.conf
.rw-r-----   193 redis    │       │   │   ├── 04-rdb.conf
.rw-r-----     0 redis    │       │   │   ├── 05-replication.conf
.rw-r-----     0 redis    │       │   │   ├── 06-keys-tracking.conf
.rw-r-----    32 redis    │       │   │   ├── 07-acl.conf
.rw-r-----     0 redis    │       │   │   ├── 08-client.conf
.rw-r-----     0 redis    │       │   │   ├── 09-memory-management.conf
.rw-r-----   153 redis    │       │   │   ├── 10-lazy-freeing.conf
.rw-r-----     0 redis    │       │   │   ├── 11-io.conf
.rw-r-----    47 redis    │       │   │   ├── 12-oom.conf
.rw-r-----    15 redis    │       │   │   ├── 13-thp.conf
.rw-r-----   262 redis    │       │   │   ├── 14-aof.conf
.rw-r-----     0 redis    │       │   │   ├── 15-shutdown.conf
.rw-r-----     0 redis    │       │   │   ├── 16-long-blocking.conf
.rw-r-----     0 redis    │       │   │   ├── 17-long-cluster.conf
.rw-r-----     0 redis    │       │   │   ├── 18-long-cluster-support.conf
.rw-r-----    49 redis    │       │   │   ├── 19-slow-log.conf
.rw-r-----    28 redis    │       │   │   ├── 20-latency.conf
.rw-r-----    25 redis    │       │   │   ├── 21-event-notification.conf
.rw-r-----   566 redis    │       │   │   ├── 22-advanced-config.conf
.rw-r-----    22 redis    │       │   │   └── 23-active-defragmentation.conf
.rw-r-----  1.5k redis    │       │   ├── redis.conf
.rw-r-----   13k redis    │       │   ├── source-full.conf
.rw-r-----  114k redis    │       │   └── source.conf
drwx------     - redis    │       └── tls
.rw-------  1.9k redis    │           ├── ca.crt
.rw-------  3.3k redis    │           ├── ca.key
.rw-------    41 redis    │           ├── ca.txt
.rw-------  1.6k redis    │           ├── client.crt
.rw-------  1.7k redis    │           ├── client.key
.rw-------   163 redis    │           ├── openssl.cnf
.rw-------  1.5k redis    │           ├── redis.crt
.rw-------   428 redis    │           ├── redis.dh
.rw-------  1.7k redis    │           ├── redis.key
.rw-------  1.6k redis    │           ├── server.crt
.rw-------  1.7k redis    │           └── server.key
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
.rw-------  2.8k postgres │   │   ├── postgres-10.json
.rw-------   165 postgres │   │   ├── postgres-10.log
drwxr-x---     - postgres │   │   └── wal_archive
.rw-------   17M postgres │   │       ├── 000000010000000000000001
.rw-------   17M postgres │   │       ├── 000000010000000000000002
.rw-------   17M postgres │   │       ├── 000000010000000000000003
.rw-------   17M postgres │   │       ├── 000000010000000000000004
.rw-------   17M postgres │   │       ├── 000000010000000000000005
.rw-------   17M postgres │   │       └── 000000010000000000000006
drwxr-x---     - redis    │   └── redis
drwxr-xr-x     - redis    │       ├── rdbData
.rw-r-----   102 redis    │       │   └── dump.rdb
.rw-r-----  5.5k redis    │       ├── redis-server.log
.rw-r-----   10k redis    │       └── redis-server.log-20260208.1770554537
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
.rw-------   17M postgres │   │   ├── 000000010000000000000008
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
