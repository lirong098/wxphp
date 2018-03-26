<?php

namespace app\index\controller;

use think\Controller;
use think\Request;
use think\Db;

class Index extends Controller {
    public function index() {
        return $this->fetch();
    }
    public function moderndate() {
        // print_r('fdsfssadad');
        return json(date('Y/m/d', time()));
    }
    public function test()
    {
      phpinfo();
    }
}