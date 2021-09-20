# wx-push

个人利用企业微信应用推送消息，实现推送消息服务。
我的博客：http://yjqz.cc/
欢迎加入群聊：992779054

# 搭建须知

填写好 index.php 里的配置

```
$corpid = "xxxx";
$secret = "xxxx";
$agentid = 1000004;
```

自行配置 token.json 禁止外网访问到。
eg：nginx 可添加配置为：

```
    location /token.json
    {
        return 404;
    }
```

# 接口

http://127.0.0.1/index.php

## GET 请求

type: 消息类型 textcard/text
title: 标题
msg: 消息

eg:

`http://127.0.0.1/index.php?type=textcard&msg=123&title=推送`

![](https://cdn.jsdelivr.net/gh/evrstr/img@master/yjqz/1632149313238S10920-22433795.png)
