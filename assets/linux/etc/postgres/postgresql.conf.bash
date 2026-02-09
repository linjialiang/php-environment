cp /server/pgData/postgresql.conf{,.bak}
# 追加
cat >> /server/pgData/postgresql.conf << 'EOF'
#
# - basic
listen_addresses = '127.0.0.1,192.168.66.254'
#port = 5432
external_pid_file = '/run/postgres/process.pid'
unix_socket_directories = '/run/postgres'

# - TLS
ssl = on
ssl_ca_file = '/server/etc/postgres/tls/root.crt'
ssl_cert_file = '/server/etc/postgres/tls/server.crt'
#ssl_crl_file = ''
#ssl_crl_dir = ''
ssl_key_file = '/server/etc/postgres/tls/server.key'
#openssl>=3.0以后，安全性得到提升，通常不配置此项
# 如果需要更高的安全性或特定的兼容性要求，并且服务器资源允许，那么可以配置此项
#ssl_dh_params_file = '/server/etc/postgres/tls/pgsql.dh'

# - WAL
# 启用复制至少是 replica
wal_level = replica
archive_mode = on
# 把 WAL 片段拷贝到目录 /server/logs/postgres/wal_archive/
archive_command = 'test ! -f /server/logs/postgres/wal_archive/%f && cp %p /server/logs/postgres/wal_archive/%f'

# - LOG
log_destination = 'jsonlog'
logging_collector = on
log_directory = '/server/logs/postgres'
log_file_mode = 0600
log_truncate_on_rotation = on
log_filename = 'postgres-%d.log'
log_rotation_age = 1d
log_rotation_size = 0
EOF
