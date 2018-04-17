<?php
namespace app\index\controller;

use app\common\BaseController;

class Index extends BaseController
{
    public function index()
    {
		$userList = session('userList') ? session('userList') : [];
		$this->assign('userList', $userList);
		$this->assign('user_name', $this->sessionInfo['user_name']);
		$this->assign('user_id', $this->sessionInfo['id']);
		return $this->fetch();
    }
	
    public function message()
    {
		$userList = session('userList') ? session('userList') : [];
		$this->assign('userList', $userList);
		$this->assign('user_name', $this->sessionInfo['user_name']);
		$this->assign('user_id', $this->sessionInfo['id']);
		return $this->fetch();
    }
	
	public function index222()
    {
		$userAdmin = 'hezonglicanyin';
		$userPass = 'hzlcy123456';
		
		$auth = session('sAuth');
		// list($_SERVER['PHP_AUTH_USER'], $_SERVER['|']) = explode(':' , base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
		// $user = $_SERVER['PHP_AUTH_USER'];
		// $pass = $_SERVER['PHP_AUTH_PW'];
		
		if (isset($_SERVER["PHP_AUTH_USER"]) && empty($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW']) && empty($_SERVER['PHP_AUTH_PW'])) {
			var_dump($_SERVER);
			if ($_SERVER['PHP_AUTH_USER'] == $userAdmin && $_SERVER['PHP_AUTH_PW'] == $userPass) {
				session('sAuth', 'heozongli');
				$auth = true;
			}
		}
		
		// if (!$auth) {
			// Header('WWW-Authenticate: Basic realm="身份验证功能"');
			// Header('HTTP/1.0 401 Unauthorized');
			// echo '身份验证失败!';
			// exit();
		// } else {
			$userList = session('userList') ? session('userList') : [];
			$this->assign('userList', $userList);
			return $this->fetch();
		// }
    }
}
