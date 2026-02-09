cp /server/pgData/pg_hba.conf{,.bak}
# 覆盖
cat > /server/pgData/pg_hba.conf << 'EOF'

# "local" is for Unix domain socket connections only
# 套接字连接部署环境推荐使用 指定用户+scram-sha-256 密码认证
# Allow replication connections from localhost, by a user with the
# replication privilege.
# 局域网和外网需要使用 ssl认证+密码认证 建立连接
# 本地仅需要通过 密码认证 建立连接

# TYPE     DATABASE     USER      IP-ADDRESS(IP-mask)  AUTH-METHOD    AUTH-OPTIONS
local      all          postgres                       ident
local      all          all                            reject
host       replication  repl_user 192.168.66.253/32    scram-sha-256
hostssl    all          admin     192.168.0.0/16       scram-sha-256  clientcert=verify-full
hostnossl  all          admin     127.0.0.1/32         scram-sha-256
EOF
