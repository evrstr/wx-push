<?php
//此处配置你的信息 start
$corpid = "xxxx";
$secret = "xxxx";
$agentid = 1000004;
//此处配置你的信息 end

//获取access_token
$access_token = getCacheToken();


$data = [
    'touser' => '@all',
    'agentid' => $agentid,
];

switch ($_GET['type']) {
    case 'text':
        $data['msgtype'] = $_GET['type'];
        $data['text'] = [
            'content' => $_GET['msg'],
        ];
        break;
    case 'textcard':
        $data['msgtype'] = $_GET['type'];
        $data['textcard'] = [
            'title' => $_GET['title'],
            'description' => $_GET['msg'],
            'url' => 'http://' . $_SERVER['HTTP_HOST'] . '/detail.php?title=' . $_GET['title'] . '&description=' . $_GET['msg'],
        ];
        break;
        //  markdown 企业微信客户端才能显示
    case 'markdown':
        $data['msgtype'] = $_GET['type'];
        $data['markdown'] = [
            'content' => $_GET['msg'],
        ];
        break;
    default:
        # code...
        break;
}


$messageUrl = 'https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token=' . $access_token;
$message = https_request($messageUrl, 'post', $data);

if ($message['errcode'] != 0) {
    # code...
    getCacheToken(true);
}

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
    if ($force) {
        # code...
        $token = getToken();
        $token['expire_time'] = time() + $token['expires_in'];
        file_put_contents('token.json', json_encode($token));
    }


    //判断文件是否存在  存在取读取token  在判断是否到期
    if (file_exists('token.json')) {
        $ken = file_get_contents('token.json');
        $arr_token = json_decode($ken, true);
        if (is_array($arr_token) && $arr_token['expire_time'] > time()) {
            $token['access_token'] = $arr_token['access_token'];
        } else {
            $token = getToken();
            $token['expire_time'] = time() +  $token['expires_in']; //7200;
            file_put_contents('token.json', json_encode($token));
        }
    } else {
        $token = getToken();
        $token['expire_time'] = time() + $token['expires_in']; //7200;
        file_put_contents('token.json', json_encode($token));
    }
    return $token['access_token'];
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
