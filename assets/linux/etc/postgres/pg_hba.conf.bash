cp /server/pgData/pg_hba.conf{,.bak}
# 覆盖
cat > /server/pgData/pg_hba.conf << 'EOF'

# TYPE  DATABASE        USER            ADDRESS                 METHOD

# "local" is for Unix domain socket connections only
local   all             all                                     trust
# IPv4 local connections:
# host    all             all             127.0.0.1/32            trust
# IPv6 local connections:
# host    all             all             ::1/128                 trust
# Allow replication connections from localhost, by a user with the
# replication privilege.
local   replication     all                                     trust

# 局域网和外网需要使用 ssl认证+密码认证 建立连接
hostssl   all   admin   192.168.0.0/16    scram-sha-256   clientcert=verify-full
# 本地仅需要通过 密码认证 建立连接
hostnossl all   admin   127.0.0.1/32      scram-sha-256
EOF
