#!/bin/bash

if [ -z $MYSQL_ROOT_PASSWORD ]; then
  exit 1
fi

mysql_install_db --user mysql > /dev/null

cat > /tmp/sql <<EOF
CREATE DATABASE IF NOT EXISTS $MYSQL_DATABASE;
USE mysql;
INSERT INTO user(Host, User, Password) values ('%', 'root', PASSWORD('$MYSQL_ROOT_PASSWORD'));
FLUSH PRIVILEGES;
GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' WITH GRANT OPTION;
FLUSH PRIVILEGES;
UPDATE user SET Password=PASSWORD("$MYSQL_ROOT_PASSWORD") WHERE User='root';
FLUSH PRIVILEGES;
EOF

mysqld --bootstrap --verbose=0 < /tmp/sql
rm -rf /tmp/sql

mysqld
