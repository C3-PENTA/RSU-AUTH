#!/bin/bash

cat > /etc/apache2/sites-available/000-default.conf << EOF
ServerName CertTool2016
<VirtualHost *:80>
    ServerAdmin certTool
    DocumentRoot /var/www/scms2016gui/public
    <Directory /var/www/scms2016gui/public>
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

