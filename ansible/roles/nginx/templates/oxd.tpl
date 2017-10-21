server {
# use port 8888 and keep 80 for our apache…
# listen 8888 default_server;
# listen [::]:8888 default_server ipv6only=on;

root /vagrant/www
index index.php index.hh index.html index.htm;

# Make site accessible from http://localhost/
server_name localhost;

# OXID eShop Rewrite Regeln
set $script_name $fastcgi_script_name;
# PHP FastCGI ueber Socket
set $fcgi_php unix:/var/run/php5-fpm.sock;
# PHP ueber HHVM! laeuft aktuell ueber Port 9000, nicht ueber Socket …
# set $fcgi_php 127.0.0.1:9000;

# Im Admin und Setup wird kein Rewrite auf die oxseo.php benoetigt
location ~ ^/(setup|admin)/?$ {
set $script_name /$1/index.php;
include fastcgi_params.oxid;
}

# Zugriff auf die internen Dateien und Apache’s “.ht” Dateien verweigern.
location ~ (/\.ht|EXCEPTION_LOG\.txt|\.log$|\.tpl$|pkg.rev) {
deny all;
}
# OXID 4.5.2+ spezifisch
location ~ /out/pictures/.*(\.jpg|\.gif|\.png)$ {
try_files $uri /core/utils/getimg.php;
}

# Fuer”out” Verzeichnis sind keine Rewrite-Regeln anzuwenden.
location ~ /(core|export|modules|out|tmp|views)/ {
}
# OXID eShop Setup: Pruefung auf “mod_rewrite”.
location = /oxseo.php {
if ($args ~ “mod_rewrite_module_is=off”) {
rewrite /oxseo.php /oxseo.php?mod_rewrite_module_is=on? break;
}
set $script_name oxseo.php;
include fastcgi_params.oxid;
}

# PHP-Dateien ueber PHP-FastCGI ausfuehren
location ~ \.php$ {
# Zero-day exploit defense.
# http://forum.nginx.org/read.php?2,88845,page=3
try_files $uri =404;
include fastcgi_params.oxid;
}

# SEO URLs auf die oxseo.php leiten.
location / {
if (!-e $request_filename) {
set $script_name /oxseo.php;
}
include fastcgi_params.oxid;
}

# Anfragen auf das Root Dokument auf die index.php leiten.
location = / {
fastcgi_index index.php;
set $script_name $fastcgi_script_name;
include fastcgi_params.oxid;
}
}