h1. install

h2. java8
apt-get install -y vim wget curl zip unzip gnupg2

echo "deb http://ppa.launchpad.net/webupd8team/java/ubuntu trusty main" | tee /etc/apt/sources.list.d/webupd8team-java.list
echo "deb-src http://ppa.launchpad.net/webupd8team/java/ubuntu trusty main" | tee -a /etc/apt/sources.list.d/webupd8team-java.list
apt-key adv --keyserver keyserver.ubuntu.com --recv-keys EEA14886

apt-get update && apt-get install -y oracle-java8-installer


h2. tomcat
useradd -r tomcat --shell /bin/false
wget https://archive.apache.org/dist/tomcat/tomcat-8/v8.0.41/src/apache-tomcat-8.0.41-src.tar.gz -P /opt
cd /top
tar -zxvf apache-tomcat-8.0.41-src.tar.gz
mv apache-tomcat-8.0.41-src tomcat8
chown -hR tomcat: /opt/tomcat8
rm -rf apache-tomcat-8.0.41-src.tar.gz


h2. mariadb
apt-get install -y mariadb-server
# mysql password : root / flatron
# datadir : /var/lib/mysql
mysql_secure_install_db
service mysql start
cp .../database_manage.sh
chmod +x ~/database_manage.sh
./database_manage.sh


h3. openLDAP
apt-get install slapd
# slapd password : Administrator /flatron
# datadir : /var/lib/slapd
service slapd start


h3. DB partition 생성
call create_partition


h3. JAVA 컴파일
find . -name *.java | xargs javac -encoding UTF-8 -cp lib/PTCA_OAC.jar:lib/bcpkix-jdk15on-154.jar:lib/bcprov-jdk15on-154.jar:lib/jna-4.2.2.jar:lib/json-simple-1.1.1.jar


h3. JAVA 실행
java -cp lib/PTCA_OAC.jar:lib/bcpkix-jdk15on-154.jar:lib/bcprov-jdk15on-154.jar:lib/jna-4.2.2.jar:lib/json-simple-1.1.1.jar:./src -Djna.library.path=lib/CIS_LIB_64/ com.penta.openapi.test.OpenAPITest


h3. wca demo server
10.0.81.5:8080


h3. DB에 인덱스 삭제
MariaDB [webca]> show index from t_policy_config;
+-----------------+------------+---------------+--------------+-----------------+-----------+-------------+----------+--------+------+------------+---------+---------------+
| Table           | Non_unique | Key_name      | Seq_in_index | Column_name     | Collation | Cardinality | Sub_part | Packed | Null | Index_type | Comment | Index_comment |
+-----------------+------------+---------------+--------------+-----------------+-----------+-------------+----------+--------+------+------------+---------+---------------+
| t_policy_config |          0 | PRIMARY       |            1 | id              | A         |           3 |     NULL | NULL   |      | BTREE      |         |               |
| t_policy_config |          0 | name_UNIQUE   |            1 | name            | A         |           3 |     NULL | NULL   |      | BTREE      |         |               |
| t_policy_config |          0 | policy_UNIQUE |            1 | usage_signature | A         |           3 |     NULL | NULL   |      | BTREE      |         |               |
| t_policy_config |          0 | policy_UNIQUE |            2 | usage_encrypt   | A         |           3 |     NULL | NULL   |      | BTREE      |         |               |
| t_policy_config |          0 | policy_UNIQUE |            3 | ca_cert_id      | A         |           3 |     NULL | NULL   | YES  | BTREE      |         |               |
| t_policy_config |          0 | policy_UNIQUE |            4 | key_algo        | A         |           3 |     NULL | NULL   |      | BTREE      |         |               |
| t_policy_config |          0 | policy_UNIQUE |            5 | noextension     | A         |           3 |     NULL | NULL   | YES  | BTREE      |         |               |
| t_policy_config |          0 | policy_UNIQUE |            6 | usage_ssl       | A         |           3 |     NULL | NULL   |      | BTREE      |         |               |
+-----------------+------------+---------------+--------------+-----------------+-----------+-------------+----------+--------+------+------------+---------+---------------+
alter table t_policy_config drop index policy_UNIQUE;


