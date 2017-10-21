<IfModule mod_fastcgi.c>
    AddHandler php5-fcgi .php
    Action php5-fcgi /php5-fcgi
    Alias /php5-fcgi /usr/lib/cgi-bin/php5-fcgi
    FastCgiExternalServer /usr/lib/cgi-bin/php5-fcgi -socket /var/run/php5-fpm.sock -idle-timeout 900 -pass-header Authorization
    <Directory /usr/lib/cgi-bin>
        Options ExecCGI FollowSymLinks
        SetHandler fastcgi-script
        Require all granted
    </Directory>
</IfModule>
