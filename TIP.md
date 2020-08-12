# homestead更改nginx配置，解决413 Request Entity Too Large
# 更改网站 xxxx.test 的 nginx 配置文件
sudo vi /etc/nginx/sites-enabled/xxxx.test

# 允许客户端提交 100M 的表单内容
client_max_body_size 100m;

# 测试 nginx 配置文件是否正确
sudo nginx -t
nginx: the configuration file /etc/nginx/nginx.conf syntax is ok
nginx: configuration file /etc/nginx/nginx.conf test is successful

# 重新加载 nginx 配置
sudo service nginx reload

# 使用这些模型上的 pivot 属性访问中间表
