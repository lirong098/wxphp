<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
use think\Request;
use think\Db;
use think\Config;

//查询日期的星期几？
function weekinfo($moderndate) {
    $week = Db::query("select dayofweek('$moderndate') as week;");
    switch ($week['0']['week']) {
        case 1: //当前是星期天
            $weekinfo = "星期日";
            break;
        case 2: //当前是星期一
            $weekinfo = "星期一";
            break;
        case 3: //当前是星期二
            $weekinfo = "星期二";
            break;
        case 4: //当前是星期三
            $weekinfo = "星期三";
            break;
        case 5: //当前是星期四
            $weekinfo = "星期四";
            break;
        case 6: //当前是星期五
            $weekinfo = "星期五";
            break;
        case 7: //当前是星期六
            $weekinfo = "星期六";
            break;
    }
    return $weekinfo;
}

/*
    string $url:抓取的url，
    string $method:get还是post方法请求，默认get,如果是post那么最后一个参数$arr就要设置
    string $type  :json格式传输，也可以用xml（暂时没写(∩_∩)）
    array  $arr   :第二个参数是post，那么就要设置，默认为空
 */

function httpPost($url, $data_string) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json; charset=utf-8',
            'Content-Length: ' . strlen($data_string))
    );
    ob_start();
    curl_exec($ch);
    $return_content = ob_get_contents();
    ob_end_clean();

    $return_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    return $return_content;
}

function http_code_curl($url, $method, $type = 'json', $arr = '') {
    //初始化curl
    $curlHandle = curl_init();
    curl_setopt($curlHandle, CURLOPT_URL, $url);
    curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
    // curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array('Content-Type:image/jpg'));
    if ($method == 'post') {
        curl_setopt($curlHandle, CURLOPT_POST, 1);
        curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $arr);
    }
    // 采集
    $output = curl_exec($curlHandle);

//    dump($output);
//    $img = file_get_contents($output);
//    // 网络显示图片扩展名不是必须的，只不过在windows中无法识别
//    file_put_contents('img', $img);
//    echo '<img src="img">';
//    $output = json_decode($output, true);
//    dump($output);//把json变成数组访问

    if ($type == 'json') {
        if (curl_errno($curlHandle)) {
            return curl_error($curlHandle);
        } else {
            return json_decode($output, true);//把json变成数组访问
        }
    }
    //关闭curl
    curl_close($curlHandle);
}

//获取到acces_stoken,并放入session中，首先判断其值是否存在或过期，若果过期了就重新获取，并将过期时间设为当前时间加上7000ms，
// function getAccessToken($type = 2) {
//     if (Config::get('model') == 'online') {
//         if ($type == 1) { //教练版
//             $appid = Config::get('appid');
//             $secret = Config::get('appsecret');
//             $id = 1000;
//         }
//         if ($type == 2) { //2学员版
//             $appid = Config::get('appid2');
//             $secret = Config::get('appsecret2');
//             $id = 1001;
//         }
//     } else if (Config::get('model') == 'dev' || Config::get('model') == 'test') {
//         $appid = Config::get('appid3');
//         $secret = Config::get('appsecret3');
//         $id = 1002;
//     }

//     $token_data = Db::name("access_token")->where("id=" . $id)->find();
//     if ($token_data['accesstoken'] && intval($token_data['expire_time']) > time()) {//表示access_token没有过期
//         return $token_data['accesstoken'];
//     } else {//第一次取access_token或者access_token已经过期了
//         $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$secret";
//         $res = http_code_curl($url, 'get', 'json');
//         $accesstoken = $res["access_token"];
//         $expire_time = time() + 7000;
//         Db::name("access_token")->where("id", "=", $id)->update(["accesstoken" => $accesstoken, "expire_time" => $expire_time]);
//         return $res["access_token"];
//     }
// }

//获取网络的图片
function http_get_data($url) {

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_URL, $url);
    ob_start();
    curl_exec($ch);
    $return_content = ob_get_contents();
    ob_end_clean();

    $return_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    return $return_content;

}

// 发送取消课程的模板消息
function sendCancelMsg($data) {
    // 获取公众号$access_token
    $access_token = getToken();
    $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=$access_token";
    httpPost($url, $data);
}

// 发送学员版模板消息
function sendTraineeMsg($data) {
    // 获取公众号$access_token
    $access_token = getTokenTrainee();
    $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=$access_token";
    httpPost($url, $data);
}

function httpGet($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 500);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_URL, $url);

    $res = curl_exec($curl);
    curl_close($curl);

    return $res;
}

function getToken() {
    $geturl = "https://19622916.ledaka.cn/server/getToken.php";
    $data = json_decode(httpGet($geturl));
    $access_token = '';
    if ($data->expire_time < time()) {
        $tmp = json_decode(httpGet($geturl . "?type=1"));
        $access_token = $tmp->access_token;
    } else {
        $access_token = $data->access_token;
    }
    return $access_token;
}

function getTokenTrainee() {
    $geturl = "https://19622916.ledaka.cn/server/member/getToken.php";
    $data = json_decode(httpGet($geturl));
    $access_token = '';
    if ($data->expire_time < time()) {
        $tmp = json_decode(httpGet($geturl . "?type=1"));
        $access_token = $tmp->access_token;
    } else {
        $access_token = $data->access_token;
    }
    return $access_token;
}
//求当前时间的 年月
function yearmonth($moderndate) {
    $yearmonth = Db::query("select DATE_FORMAT(str_to_date(FROM_UNIXTIME($moderndate,'%Y-%m-%d %h:%i:%s'), '%Y-%m-%d %h:%i:%s'),'%Y-%m') as yearmonth ");
    $v = $yearmonth['0']['yearmonth'];
    return $v;
}

//数组排序
function sortArrByField(&$array, $field, $sort = 'asc') {
    $fieldArr = array();
    foreach ($array as $k => $v) {
        $fieldArr[$k] = $v[$field];
    }
    $type = $sort == 'asc' ? SORT_ASC : SORT_DESC;
    array_multisort($fieldArr, $type, $array);
}

//中文截取多少个字
function mbStrcut($text, $index) {
    $len = strlen($text);
    if ($len > $index) {
        $text = mb_strcut($text, 0, $index, "utf-8") . "...";
    }
    return $text;
}


function array_sort($array, $row, $type) {
    $array_temp = array();
    $i = 0;
    foreach ($array as $v) {
        $array_temp[$v[$row] . $i] = $v;
        $i++;
    }
    if ($type == 'asc') {
        ksort($array_temp);
    } elseif ($type = 'desc') {
        krsort($array_temp);
    } else {
    }
    return $array_temp;
}

function my_sort($arrays, $sort_key, $sort_order = SORT_ASC, $sort_type = SORT_NUMERIC) {
    if (is_array($arrays)) {
        foreach ($arrays as $array) {
            if (is_array($array)) {
                $key_arrays[] = $array[$sort_key];
            } else {
                return false;
            }
        }
    } else {
        return false;
    }
    array_multisort($key_arrays, $sort_order, $sort_type, $arrays);
    return $arrays;
}

function getFirstCharter($str) {
    if (empty($str)) {
        return '';
    }
    $fchar = ord($str{0});
    if ($fchar >= ord('A') && $fchar <= ord('z')) return $str{0};
    $s1 = iconv('UTF-8', 'gb2312', $str);
    $s2 = iconv('gb2312', 'UTF-8', $s1);
    $s = $s2 == $str ? $s1 : $str;
    $asc = ord($s{0}) * 256 + ord($s{1}) - 65536;
    if ($asc >= -20319 && $asc <= -20284) return 'A';
    if ($asc >= -20283 && $asc <= -19776) return 'B';
    if ($asc >= -19775 && $asc <= -19219) return 'C';
    if ($asc >= -19218 && $asc <= -18711) return 'D';
    if ($asc >= -18710 && $asc <= -18527) return 'E';
    if ($asc >= -18526 && $asc <= -18240) return 'F';
    if ($asc >= -18239 && $asc <= -17923) return 'G';
    if ($asc >= -17922 && $asc <= -17418) return 'H';
    if ($asc >= -17417 && $asc <= -16475) return 'J';
    if ($asc >= -16474 && $asc <= -16213) return 'K';
    if ($asc >= -16212 && $asc <= -15641) return 'L';
    if ($asc >= -15640 && $asc <= -15166) return 'M';
    if ($asc >= -15165 && $asc <= -14923) return 'N';
    if ($asc >= -14922 && $asc <= -14915) return 'O';
    if ($asc >= -14914 && $asc <= -14631) return 'P';
    if ($asc >= -14630 && $asc <= -14150) return 'Q';
    if ($asc >= -14149 && $asc <= -14091) return 'R';
    if ($asc >= -14090 && $asc <= -13319) return 'S';
    if ($asc >= -13318 && $asc <= -12839) return 'T';
    if ($asc >= -12838 && $asc <= -12557) return 'W';
    if ($asc >= -12556 && $asc <= -11848) return 'X';
    if ($asc >= -11847 && $asc <= -11056) return 'Y';
    if ($asc >= -11055 && $asc <= -10247) return 'Z';
    return null;
}