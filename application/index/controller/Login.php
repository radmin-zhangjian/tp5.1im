<?php
namespace app\index\controller;

use think\Controller;

class Login extends Controller
{
	public function login()
    {
		if (session(md5('user_id'))) {
			header('Location:/');
			exit;
		}
		return $this->fetch();
    }
	
	public function doLogin()
    {
		$result = ['code'=>0, 'message'=>'登录失败'];
		$request = $this->request->post();
		if ($request['user_name'] && $request['user_pwd']) {
			$map[] = ['user_name', '=', $request['user_name']];
			$aUserIm = db('hzl_user_im')->where($map)->find();
			if ($aUserIm && $aUserIm['user_pwd'] == md5($request['user_pwd'])) {
				$sInfo['id'] = $aUserIm['id'];
				$sInfo['user_name'] = $aUserIm['user_name'];
				session(md5('user_id'), $sInfo);
				$result = ['code'=>1, 'message'=>'登录成功'];
			}
		}
		return json($result);
    }
	
	public function logout()
    {
		session(md5('user_id'), null);
		session(null);
		$this->redirect('/login');
    }
}
