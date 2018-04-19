<?php
namespace app\index\controller;

use app\common\BaseController;

class Start extends BaseController
{
    public function index()
    {
		$this->assign('user_name', $this->sessionInfo['user_name']);
		return $this->fetch();
    }
	
    public function push()
    {
		$sUid = 'aaa';
		$msg = '这是一条测试数据!';
		$date = date('Y-m-d H:i:s', time());
		
		// 已进程ID和线程ID为标识推送
		// $data = ['type' => 'event', 'uid' => $sUid, 'content' => $msg, 'time' => $date, 'to_worker_id' => 3, 'to_connection_id' => 2];
		
		// 已uid为标识推送
		$data = ['type' => 'event', 'uid' => $sUid, 'content' => $msg, 'time' => $date];
		
		// 广播
		// $data = ['type' => 'event', 'content' => $msg, 'time' => $date];
		
		$url = 'http://im.hezongli.com:4238';
		// $url = 'http://im.hezongli.com:4238/?content=这是一条测试数据';
		// $url = 'http://im.hezongli.com:4238/?to_worker_id={$worker_id}&to_connection_id={$connection_id}&content=这是一条测试数据';
		
		$result = u_curl($url, 'POST', $data); // post方式发送
		return json($result);
    }
	
}
