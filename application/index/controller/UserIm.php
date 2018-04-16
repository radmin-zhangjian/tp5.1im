<?php
namespace app\index\controller;

use app\common\BaseController;

class UserIm extends BaseController
{
	public function initialize()
	{
		parent::initialize();
		if ($this->sessionInfo['id'] != 1 && $this->sessionInfo['user_name'] != 'admin') {
			session(null);
			header('Location: /login');
			exit;
		}
	}
	
	public function userList()
    {
		$userList = db('hzl_user_im')->where([])->paginate(15);
        // var_dump($userList->toArray()['data']);
		
        // 获取分页显示
        $page = $userList->render();
        // var_dump($page);
		
		// 获取总记录数
		$count = $userList->total();
		
        // 模板变量赋值
        $this->assign('userList', $userList);
        $this->assign('page', $page);
		
		// $userList = db('hzl_user_im')->where([])->select();
		// $this->assign('userList', $userList);
		return $this->fetch();
    }
	
	public function userAdd()
    {
		return $this->fetch();
    }
	
	public function userSave()
    {
		$request = $this->request->post();
		if (trim($request['user_name']) == '') {
			return json(['code'=>0, 'message'=>'用户名不能为空']);
		}
		if (trim($request['user_pwd']) == '') {
			return json(['code'=>0, 'message'=>'密码不能为空']);
		}
		if (trim($request['user_pwd']) != trim($request['user_pwd_qr'])) {
			return json(['code'=>0, 'message'=>'密码与确认密码不相符']);
		}
		
		$user = db('hzl_user_im')->where([['user_name', '=', $request['user_name']]])->count(1);
		if ($user) {
			return json(['code'=>0, 'message'=>'用户名已存在']);
		}
		
		$data['user_name'] = $request['user_name'];
		$data['user_pwd'] = md5($request['user_pwd']);
		$data['create_time'] = time();
		$res = db('hzl_user_im')->insert($data);
		if ($res) {
			return json(['code'=>1, 'message'=>'新增成功']);
		} else {
			return json(['code'=>0, 'message'=>'新增失败']);
		}
    }
	
	public function userEdit()
    {
		$request = $this->request->param();
		$showInfo = db('hzl_user_im')->where([['id', '=', intval($request['id'])]])->find();
		$this->assign('showInfo', $showInfo);
		return $this->fetch();
    }
	
	public function userUpdate()
    {
		$request = $this->request->post();
		if (trim($request['user_name']) == '') {
			return json(['code'=>0, 'message'=>'用户名不能为空']);
		}
		if (trim($request['user_pwd']) == '') {
			return json(['code'=>0, 'message'=>'密码不能为空']);
		}
		
		$user = db('hzl_user_im')->where([['user_name', '=', $request['user_name']]])->count(1);
		if (!$user) {
			return json(['code'=>0, 'message'=>'用户名不存在']);
		}
		$data['user_pwd'] = md5($request['user_pwd']);
		$res = db('hzl_user_im')->where([['id', '=', intval($request['id'])]])->update($data);
		if ($res) {
			return json(['code'=>1, 'message'=>'修改成功']);
		} else {
			return json(['code'=>0, 'message'=>'修改失败']);
		}
    }
	
	public function userDel()
    {
		$request = $this->request->param();
		if (intval($request['id']) == 0) {
			return json(['code'=>0, 'message'=>'用户不存在']);
		}
		if (intval($request['id']) == 1) {
			return json(['code'=>0, 'message'=>'管理员不允许删除']);
		}
		
		$res = db('hzl_user_im')->where([['id', '=', intval($request['id'])]])->delete();
		if ($res) {
			return json(['code'=>1, 'message'=>'删除成功']);
		} else {
			return json(['code'=>0, 'message'=>'删除失败']);
		}
    }
	
}
