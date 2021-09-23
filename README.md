# wx-push

个人利用企业微信应用推送消息，实现推送消息服务。
教程：https://yjqz.cc/archives/wx-push.html
欢迎加入群聊：992779054

**配套 typecho 插件请看：https://yjqz.cc/archives/plugin-wxpush.html**

# 搭建须知

填写好 index.php 里的配置

```
$corpid = "xxxx";
$secret = "xxxx";
$agentid = 1000004;
```

自行配置 db.db 禁止外网访问到。
eg：nginx 可添加配置为：

```
    location /db.db
    {
        deny all;
    }
```

# 接口

http://127.0.0.1/index.php

## GET 请求

type: 消息类型 textcard/text
title: 标题
msg: 消息
detail: 卡片消息详情界面的 html 代码字符串(使用此字段请尽量用 post 请求)

eg:
`http://127.0.0.1/index.php?type=textcard&msg=123&title=推送&detail=<p%20align="center"%20style="color:red;">测试html</p>`

![](https://cdn.jsdelivr.net/gh/evrstr/img@master/yjqz/1632149313238S10920-22433795.png)

# 更新历史

v 1.1.0

- 增加 sqlite 数据库支持，存储历史消息。
- 可以自定义详情页面
- 新增 detail 字段（html 字符串），尽量使用 post 方式进行请求，防止使用 get 超出限制长度
- 支持 post 请求，确保请求头为`Content-type:application/x-www-form-urlencoded;`
