<?php
//此处配置你的信息
$corpid = "ww5010288bc1e80010";
$secret = "Oeu7t2SkFuyZeg8PJM_QY14WhZKYiYntBXevI3-HPcQ";
$agentid = 1000004;



$data = [
    'touser' => '@all',
    'agentid' => $agentid,
];

// sqlite


class MyDB extends SQLite3
{
    function __construct()
    {
        $this->open('db.db');
    }
}


// 请求参数

$params =  $_REQUEST;
// 详情页面
$detail = $_REQUEST['detail'];

if (!get_magic_quotes_gpc()) {
    $lastname = addslashes($detail);
}

// echo $detail;

$detail = htmlspecialchars($detail, ENT_QUOTES);


$db = new MyDB();
$sql = <<<EOF
INSERT INTO msg (type,title,msg,detail)
VALUES ('{$params["type"]}', '{$params["title"]}','{$params["msg"]}', "{$detail}");
EOF;

$ret = $db->exec($sql);
global $dbid;
$dbid = $db->lastInsertRowID();

$db->close();


//防跨域
header('Access-Control-Allow-Origin:*');

switch ($params['type']) {
    case 'text':
        $data['msgtype'] = $params['type'];
        $data['text'] = [
            'content' => $params['msg'],
        ];
        break;
    case 'textcard':
        $data['msgtype'] = $params['type'];
        $data['textcard'] = [
            'title' => $params['title'],
            'description' => $params['msg'],
            'url' => "http://{$_SERVER['HTTP_HOST']}/detail.php?id={$dbid}",
        ];
        break;
    default:
        break;
}
//获取access_token
$access_token = getCacheToken();

$messageUrl = "https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token={$access_token}";
$message = https_request($messageUrl, 'post', $data);

if ($message['errcode'] != 0) {
    # code...
    getCacheToken(true);
    $messageUrl = "https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token={ $access_token}";
    $message = https_request($messageUrl, 'post', $data);
}

$sql = <<<EOF
UPDATE  msg SET value = "{$message['msgid']}" WHERE key="msgid";
EOF;


echo json_encode($message);

//获取access_token
function getToken()
{
    global $corpid;
    global $secret;
    //获取access_token https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=ID&corpsecret=SECRET
    $url = 'https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=' . $corpid . '&corpsecret=' . $secret;
    $token = https_request($url);
    return $token;
}


//把token写入文件  加个时间戳
function getCacheToken($force = false)
{
    $realtoken = null;
    $db = new MyDB();
    if ($force) {
        # code...
        $token = getToken();
        $token['expire_time'] = time() + $token['expires_in'];

        $sql = <<<EOF
        UPDATE  config SET value = "{$token['access_token']}" WHERE key="access_token";
        UPDATE  config SET value = "{$token['expire_time']}" WHERE key="expire_time";
EOF;
        $ret = $db->exec($sql);
    }

    $sql = <<<EOF
    SELECT * from config;
EOF;

    $ret = $db->query($sql);
    $dbtoken = null;
    $dbexpire_time = null;

    while ($row = $ret->fetchArray(SQLITE3_ASSOC)) {
        if ($row['key'] == "access_token") {
            $dbtoken = $row['value'];
        }
        if ($row['key'] == "expire_time") {
            $dbexpire_time = $row['value'];
        }
    }

    if ((int)$dbexpire_time > time()) {
        $realtoken = $dbtoken;
    } else {
        $token = getToken();
        $token['expire_time'] = time() + $token['expires_in'];
        $sql = <<<EOF
        UPDATE  config SET value = "{$token['access_token']}" WHERE key="access_token";
        UPDATE  config SET value = "{$token['expire_time']}" WHERE key="expire_time";
EOF;
        $ret = $db->exec($sql);
        $realtoken = $token['access_token'];
    }
    $db->close();
    return $realtoken;
}

// CURL
function https_request($url, $format = 'get', $data = null)
{
    $headerArray = array("Content-type:application/json;", "Accept:application/json");
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    if ($format == 'post') {
        curl_setopt($curl, CURLOPT_POST, 1);
        if ($data) {
            $data  = json_encode($data);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headerArray);
    $data = json_decode(curl_exec($curl), true);
    // $data=curl_exec($curl);
    curl_close($curl);
    return $data;
}
