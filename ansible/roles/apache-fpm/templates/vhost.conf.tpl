<VirtualHost *:80>
    ServerAdmin webmaster@localhost
    DocumentRoot /vagrant/web
{% set servernames = servername.split() %}
{% for servername in servernames %}
{% if loop.first %}
    ServerName {{ servername }}
{% else %}
    ServerAlias {{ servername }}
{% endif %}
{% endfor %}
    <Directory /vagrant/web>
        EnableSendfile Off
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
