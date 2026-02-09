cp /server/pgData/pg_hba.conf{,.bak}
# 覆盖
cat > /server/pgData/pg_hba.conf << 'EOF'
# PostgreSQL Client Authentication Configuration File
# ===================================================
#
# TYPE     DATABASE     USER      IP-ADDRESS(IP-mask)  AUTH-METHOD    AUTH-OPTIONS
local      all          postgres                       ident          map=emadMapPostgres
local      all          all                            reject
host       replication  repl_user 192.168.66.253/32    scram-sha-256
hostssl    all          admin     192.168.0.0/16       scram-sha-256  clientcert=verify-full
hostnossl  all          admin     127.0.0.1/32         scram-sha-256
EOF
