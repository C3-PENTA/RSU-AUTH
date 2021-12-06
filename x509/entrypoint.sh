#!/bin/sh


export LDAP_ROOTPASS=flatron
export LDAP_ORGANISATION=pentasecurity
export LDAP_DOMAIN=pentasecurity.com

################# configure ldap
set -eu



status () {
  echo "---> ${@}" >&2
}

# set -x
: LDAP_ROOTPASS=${LDAP_ROOTPASS}
: LDAP_DOMAIN=${LDAP_DOMAIN}
: LDAP_ORGANISATION=${LDAP_ORGANISATION}

if [ ! -e /var/lib/ldap/docker_bootstrapped ]; then
  status "configuring slapd for first run"

  cat <<EOF | debconf-set-selections
slapd slapd/internal/generated_adminpw password ${LDAP_ROOTPASS}
slapd slapd/internal/adminpw password ${LDAP_ROOTPASS}
slapd slapd/password2 password ${LDAP_ROOTPASS}
slapd slapd/password1 password ${LDAP_ROOTPASS}
slapd slapd/dump_database_destdir string /var/backups/slapd-VERSION
slapd slapd/domain string ${LDAP_DOMAIN}
slapd shared/organization string ${LDAP_ORGANISATION}
slapd slapd/backend string MDB
slapd slapd/purge_database boolean true
slapd slapd/move_old_database boolean false
slapd slapd/allow_ldap_v2 boolean false
slapd slapd/no_configuration boolean false
slapd slapd/dump_database select when needed
EOF

  dpkg-reconfigure -f noninteractive slapd

  touch /var/lib/ldap/docker_bootstrapped

  status "Starting slapd"
  service slapd start

  status "Create rootdn"
  ldapmodify -Y EXTERNAL -H ldapi:/// -f /etc/ldap/slapd.d/rootdn.ldif
  ldapadd -x -D cn=admin,c=kr -w flatron -f /etc/ldap/slapd.d/kr.ldif

else
  status "found already-configured slapd"

  status "Starting slapd"
  service slapd start
fi



sleep 1

################# configure mysql
if [ ! -e /var/lib/mysql/docker_bootstrapped ]; then
  status "configuring mariadb for first run"

  mysql_install_db --user=mysql > /dev/null

  cat > /tmp/sql <<EOF
CREATE DATABASE IF NOT EXISTS webca;
USE mysql;
INSERT INTO user(Host, User, Password) values ('%', 'root', PASSWORD('flatron'));
FLUSH PRIVILEGES;
GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' WITH GRANT OPTION;
FLUSH PRIVILEGES;
UPDATE user SET Password=PASSWORD("flatron") WHERE User='root';
FLUSH PRIVILEGES;
INSERT INTO user(Host, User, Password) values ('localhost', 'debian-sys-maint', PASSWORD('flatron'));
GRANT ALL PRIVILEGES ON *.* TO 'debian-sys-maint'@'localhost' IDENTIFIED BY 'flatron';
FLUSH PRIVILEGES;
INSERT INTO user VALUES ('%','webca', PASSWORD('penta@webca!'),'Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','N','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','','','','',0,0,0,0,'','','N','N');
INSERT INTO user VALUES ('localhost','webca', PASSWORD('penta@webca!'),'Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','N','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','','','','',0,0,0,0,'','','N','N');
UPDATE user SET select_priv='N' WHERE user='webca';
DROP DATABASE IF EXISTS webca;
CREATE DATABASE webca DEFAULT CHARSET utf8;
GRANT ALL PRIVILEGES ON webca.* TO webca@localhost IDENTIFIED BY 'penta@webca!' WITH GRANT OPTION;
GRANT ALL PRIVILEGES ON webca.* TO 'webca'@'%' IDENTIFIED BY 'penta@webca!' WITH GRANT OPTION;
flush privileges;
EOF

  mysqld --bootstrap --verbose=0 < /tmp/sql

  status "Start Mysql"
  service mysql stop
  service mysql start
else
  status "found already-configured mariadb"

  status "Start Mysql"
  service mysql stop
  service mysql start
fi


sleep 1
status "Start Tomcat"

export JAVA_OPTS="-Djava.library.path=/opt/tomcat8/shared"
export LD_LIBRARY_PATH="/opt/tomcat8/shared"

/opt/tomcat8/bin/startup.sh

if [ ! -e /var/lib/mysql/docker_bootstrapped ]; then
  status "Starting x509 SERVER. Wait a minute..."
  sleep 30

  cat > /tmp/create_partition <<EOF
call create_partition
EOF

  mysql -uroot -pflatron webca < /tmp/create_partition

  touch /var/lib/mysql/docker_bootstrapped

  echo "127.0.0.1	authentica.io" >> /etc/hosts
  
  status "Complete."
else
  status "Starting x509 SERVER. Wait a minute..."
  sleep 30
  status "Complete."
fi


################# OpenAPI Compile
status "OpenAPI Compile"
mkdir -p /opt/tomcat8/openapi/classes
export CLASSPATH=/opt/tomcat8/openapi/lib/PTCA_OAC.jar:/opt/tomcat8/openapi/lib/bcpkix-jdk15on-154.jar:/opt/tomcat8/openapi/lib/bcprov-jdk15on-154.jar:/opt/tomcat8/openapi/lib/jna-4.2.2.jar:/opt/tomcat8/openapi/lib/json-simple-1.1.1.jar:/opt/tomcat8/openapi/Classes
sleep 1
find /opt/tomcat8/openapi/src -name *.java | xargs javac -encoding UTF-8 -d /opt/tomcat8/openapi/classes -cp /opt/tomcat8/openapi/lib/PTCA_OAC.jar:/opt/tomcat8/openapi/lib/bcpkix-jdk15on-154.jar:/opt/tomcat8/openapi/lib/bcprov-jdk15on-154.jar:/opt/tomcat8/openapi/lib/jna-4.2.2.jar:/opt/tomcat8/openapi/lib/json-simple-1.1.1.jar
status "Complete."


sleep 1
################# Configure REST API
cat > /etc/apache2/sites-available/000-default.conf << EOF
ServerName x509-REST-API
<VirtualHost *:80>
    ServerAdmin authentica@pentasecurity.com
    DocumentRoot /opt/restapi/public
    <Directory /opt/restapi/public>
        DirectoryIndex index.php index.html
        Options FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    ErrorLog ${APACHE_LOG_DIR}/error.log
    LogLevel warn
</VirtualHost>
EOF

service apache2 start
status "Start REST-API"

mkdir -p /opt/cert
chmod -R 777 /opt/cert


status "Complete All Server"
/bin/bash
