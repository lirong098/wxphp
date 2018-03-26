<?php
namespace app\index\controller;

use think\Controller;
use app\index\model\Users;
use think\Request;
use think\Db;
use think\Config;

class User extends Controller {
    //根据openid获取用户id
    public function user() {
        $open_id = input("open_id");
        $userData = Db::table('users')->where('open_id',$open_id)->find();
        return json($userData);
    }
}
