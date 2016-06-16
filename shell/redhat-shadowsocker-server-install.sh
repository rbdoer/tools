#!/bin/sh

#install python
yum install python-setuptools && easy_install pip
#install shadowsocks
pip install shadowsocks

tee /etc/shadowsocks.json <<-'EOF'
{
    "server":"0.0.0.0",
    "server_port":8388,
    "local_address": "127.0.0.1",
    "local_port":8388,
    "password":"lrbels",
    "timeout":300,
    "method":"rc4-md5"
}
EOF

ssserver -c /etc/shadowsocks.json -d start

