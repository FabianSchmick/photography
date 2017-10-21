fastcgi_split_path_info ^(.+\.php)(/.+)$;
# A handy function that became available in 0.7.31 that breaks down
# The path information based on the provided regex expression
# This is handy for requests such as file.php/some/paths/here/

fastcgi_param  PATH_INFO          $fastcgi_path_info;
fastcgi_param  PATH_TRANSLATED    $document_root$fastcgi_path_info;

fastcgi_param  QUERY_STRING       $query_string;
fastcgi_param  REQUEST_METHOD     $request_method;
fastcgi_param  CONTENT_TYPE       $content_type;
fastcgi_param  CONTENT_LENGTH     $content_length;

fastcgi_param  SCRIPT_NAME        $fastcgi_script_name;
fastcgi_param  REQUEST_URI        $request_uri;
fastcgi_param  DOCUMENT_URI       $document_uri;
fastcgi_param  SERVER_PROTOCOL    $server_protocol;

# needed because of PHP5.5's OpCache Module (http://stackoverflow.com/a/23904770)
fastcgi_param  SCRIPT_FILENAME    $realpath_root$fastcgi_script_name;
fastcgi_param  DOCUMENT_ROOT      $realpath_root;

fastcgi_param  GATEWAY_INTERFACE  CGI/1.1;
fastcgi_param  SERVER_SOFTWARE    nginx;

fastcgi_param  REMOTE_ADDR        $remote_addr;
fastcgi_param  REMOTE_PORT        $remote_port;
fastcgi_param  SERVER_ADDR        $server_addr;
fastcgi_param  SERVER_PORT        $server_port;
fastcgi_param  SERVER_NAME        $server_name;

fastcgi_param  SSL_PROTOCOL       $ssl_protocol;
fastcgi_param  SSL_CIPHER         $ssl_cipher;
fastcgi_param  SSL_SESSION_ID     $ssl_session_id;
fastcgi_param  SSL_CLIENT_VERIFY  $ssl_client_verify;

fastcgi_param  HTTPS              $https if_not_empty;

fastcgi_hide_header               X-Powered-By;

fastcgi_pass                      unix:/var/run/php5-fpm.sock;
fastcgi_index                     index.php;