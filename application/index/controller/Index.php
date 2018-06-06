<?php

namespace app\index\controller;

use think\Controller;
use think\Request;
use think\Db;
use app\index\model\Users;

class Index extends Controller {
    public function index() {
        return $this->fetch();
    }
    public function moderndate() {
        // print_r('fdsfssadad');
        $userCount = Db::table('users')->where('open_id','oc4AK0cgXau3VH_x8wguaDdIdeRw')->count();
        $cm = new Users;
        $cm->open_id = 'oc4AK0cgXau3VH_x8wguaDdIdeRw';
        $cm->nickname = '李荣';
        $cm->user_name = '李荣';
        $cm->avatar = 'touxiang.jpg';
        $cm->sex = '0';
        $cm->unionid = '';
        $cm->save();
        $id = $cm->id;
        $userCount = Db::table('users')->where('open_id','oc4AK0cgXau3VH_x8wguaDdIdeRw')->count();
        var_dump($userCount, $id);
        return json(date('Y/m/d', time()));
    }
    public function test()
    {
      phpinfo();
    }
}