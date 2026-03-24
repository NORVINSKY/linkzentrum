!!!!- CHMOD -!!!!

/XML - 0777
/data - 0777
/data/cache - 0777

=============================
Пароль: A123456a
=============================

PHP 5.4+

short_open_tag 1
max_input_vars 5000 (в зависимости от кол-ва доноров)
open_basedir none (для работы RollingCurl)
memory_limit 256M+ (при сильной нагрузке, память лишней не будет)

=============================

!!!!- РЕКОМЕНДУЕМЫЕ НАСТРОЙКИ MYSQL -!!!!
#значение innodb_buffer_pool_size ~70-80% от имеющейся оперативной памяти на сервере

[mysqld]
datadir=/var/lib/mysql
socket=/var/lib/mysql/mysql.sock
user=mysql
symbolic-links=0
max_connections=1000
max_user_connections=0

#My options
innodb_buffer_pool_size=11G
innodb_flush_method = O_DSYNC
innodb_flush_log_at_trx_commit=0
thread_cache_size=32
query_cache_size = 26M
innodb_additional_mem_pool_size=24

wait_timeout = 300
interactive_timeout = 300
table_open_cache = 450

#Log long queries
log_slow_queries = 1
slow_query_log_file = /tmp/mysql-bin.log
long_query_time = 4
log-queries-not-using-indexes

#innodb_use_native_aio = 0
innodb_file_per_table

[mysqld_safe]
log-error=/var/log/mysqld.log
pid-file=/var/run/mysqld/mysqld.pid