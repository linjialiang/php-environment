-- /server/etc/mysql/init.sql
-- 在MySQL 8.0.18之前，每条语句必须另起一行，并且无法使用注释
-- 运行 UPDATE 和 DELETE 语句时，如果未指定约束(如 where)也未提供子句(如 limit)，则会发生错误
set global sql_safe_updates=1;
-- 在没有指定 `LIMIT` 时， SELECT 结果集限制为 `1000` 行
set global sql_select_limit=1000;
-- 用于控制使用utf8mb4字符集时的默认排序规则
-- set global default_collation_for_utf8mb4=utf8mb4_unicode_ci;
