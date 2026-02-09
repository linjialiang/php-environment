cp /server/pgData/pg_ident.conf{,.bak}
# 覆盖
cat > /server/pgData/pg_ident.conf << 'EOF'
# PostgreSQL User Name Maps
# =========================
#
# MAPNAME        SYSTEM-USERNAME  DATABASE-USERNAME
emadMapPostgres  emad             postgres
emadMapAdmin     emad             admin
EOF
