# ThinkPHP 5 解密微信小程序用户信息及获取openId
主要接口路径: application/index/controller/Login.php
```javascript
<?php
namespace app\index\controller;

use think\Controller;
use app\index\model\Users;
use think\Request;
use think\Db;
use think\Config;

class Login extends Controller {
    public function get_open_id() {
        //开发者使用登陆凭证 code 获取 session_key 和 openid
        $APPID = Config::get('appid');
        $AppSecret = Config::get('appsecret');

        $code = $_GET['code'];
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid=" . $APPID . "&secret=" . $AppSecret . "&js_code=" . $code . "&grant_type=authorization_code";
        $arr = $this->vget($url);// 一个使用curl实现的get方法请求
        $arr = json_decode($arr, true);
        return json($arr);
    }
    // login 接受的参数 {openid，session_key， rawData， signature，iv， encryptedData}
    public function login() {
        $open_id = input('openid');
        $session_key = input('session_key');
        $rawData = json_decode($_GET['rawData'], true);
        $rawData['nickName'] = preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', $rawData['nickName']);
        $date = $this->decrypt_date($session_key, $_GET['encryptedData'], $_GET['iv']);
        if (!empty($date['unionId'])) {
            $unionid = $date['unionId'];
        } else {
            $unionid = '';
        }
        $userCount = Db::table('users')->where('open_id',$open_id)->count();
        if ($userCount > 0) {
            $userData = Db::table('users')->where('open_id',$open_id)->find();
            if (!empty($date['avatarUrl'])) {
                $b = Db::name("users")->where("open_id", "=", $open_id)->update(['avatar' => $date['avatarUrl']]);
            }
            if (empty($userData['unionid'])) {
                $b = Db::name("users")->where("open_id", "=", $open_id)->update(['unionid' => $unionid]);
            }
        } else {
            $cm = new Users;
            $cm->open_id = $open_id;
            $cm->nickname = $rawData['nickName'];
            $cm->user_name = $rawData['nickName'];
            $cm->avatar = $rawData['avatarUrl'];
            $cm->sex = $rawData['gender'];
            $cm->unionid = $unionid;
            $cm->save();
            $id = $cm->id;
            if ($id >= 1) {
                $userData = array('open_id' => $open_id, 'nickname' => $rawData['nickName'], 'user_name' => $rawData['nickName'], 'avatar' => $rawData['avatarUrl'], 'sex' => $rawData['gender'], 'unionid' => $unionid);
            }
        }
        $arrayinfo = array('userData' => $userData);
        return json($arrayinfo);
        // var_dump($arrayinfo);
    }

    public function vget($url) {
        $info = curl_init();
        curl_setopt($info, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($info, CURLOPT_HEADER, 0);
        curl_setopt($info, CURLOPT_NOBODY, 0);
        curl_setopt($info, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($info, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($info, CURLOPT_URL, $url);
        $output = curl_exec($info);
        curl_close($info);
        return $output;
    }

    //根据openid获取用户id
    public function user() {
        $open_id = input("open_id");
        $userData = Db::table('users')->where('open_id',$open_id)->find();
        return json($userData);
    }

    //数据解密
    public function decrypt_date($session_key, $encryptedData, $iv) {
        //开发者如需要获取敏感数据，需要对接口返回的加密数据( encryptedData )进行对称解密
        include_once __DIR__ . "/libs/wxBizDataCrypt.php";
        $appid = Config::get('appid');
        $sessionKey = $session_key;
        $encryptedData = $encryptedData;
        $iv = $iv;
        $pc = new \WXBizDataCrypt($appid, $sessionKey);
        $errCode = $pc->decryptData($encryptedData, $iv, $data);
        if ($errCode != 0) {
            $data = '';
        }
        $data = json_decode($data, true);
        return $data;
    }
}
```
