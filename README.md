server {
        listen               80;
        listen       443 ssl;
        server_name bjjj.zhongchebaolian.com;
        ssl_certificate /home/xxx/conf/nginx/.well-known/bjjj.zhongchebaolian.com/certificate.crt;
        ssl_certificate_key /home/xxx/conf/nginx/.well-known/bjjj.zhongchebaolian.com/private.key;
        set $root /home/xxx/programs/bjjjjjj;
        root   $root;
        index  index.php;

        location ^~ /app_web/static_resources/js/homepage/move.js {
                root $root;
        }

        location ^~ /send.php {
                fastcgi_pass   php5;
                fastcgi_index  index.php;
                include        fastcgi_params;
                fastcgi_param  SLIM_ENVIRONMENT xxx;
                fastcgi_param  SCRIPT_FILENAME  $root/request.php;
        }

        location / {
            proxy_set_header Host bjjj.zhongchebaolian.com;
            proxy_set_header X-Real-IP $remote_addr;
            proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
            proxy_pass https://bjjj.zhongchebaolian.com;
            proxy_redirect default;
            proxy_set_header   Host $http_host;
        }

        access_log  logs/bjjjj.log  main;
}