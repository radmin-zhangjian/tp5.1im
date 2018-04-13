<?php
namespace app\index\controller;

use think\Controller;

class Index extends Controller
{
    public function index()
    {
        // session('userList', ['aaa', 'sss', 'fff']);
		$userList = session('userList') ? session('userList') : [];
        $this->assign('userList', $userList);
		return $this->fetch();
    }

}
