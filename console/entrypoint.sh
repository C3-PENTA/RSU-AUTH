#!/bin/bash

cat > /etc/apache2/sites-available/000-default.conf << EOF
ServerName AuthentiCA
<VirtualHost *:80>
    ServerAdmin authentica@pentasecurity.com
    DocumentRoot /var/www/authentica/public
    <Directory /var/www/authentica/public>
        DirectoryIndex index.php index.html
        Options FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    ErrorLog ${APACHE_LOG_DIR}/error.log
    LogLevel warn
</VirtualHost>
EOF

apache2ctl -k stop > /dev/null
echo "Console Webserver START";
apache2ctl -D FOREGROUND
