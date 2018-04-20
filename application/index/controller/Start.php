<?php
namespace app\index\controller;

use app\common\BaseController;

class Start extends BaseController
{
	private $url = null;
	
	public function __construct()
	{
		parent::__construct();
		$this->url = 'http://im.hezongli.com:4238';
		// $this->url = 'http://im.hezongli.com:4238/?content=这是一条测试数据';
		// $this->url = 'http://im.hezongli.com:4238/?to_worker_id={$worker_id}&to_connection_id={$connection_id}&content=这是一条测试数据';
	}
	
    public function index()
    {
		$this->assign('user_name', $this->sessionInfo['user_name']);
		return $this->fetch();
    }
	
    public function push()
    {
		$result = ['status'=>0, 'message'=>'失败'];
		
		$request = $this->request->param();
		$to_worker_id = isset($request['to_worker_id']) ?trim($request['to_worker_id']):'';
		$to_connection_id = isset($request['to_connection_id']) ?trim($request['to_connection_id']):'';
		$sUid = isset($request['id']) ?trim($request['id']):'';
		$content = isset($request['content']) ?trim($request['content']):'';
		$date = date('Y-m-d H:i:s', time());
		
		if (empty($content)) return json($result);
		
		if (!empty($to_worker_id) && !empty($to_connection_id)) {
			// 已进程ID和链接ID为标识推送
			$data = ['type' => 'event', 'content' => $content, 'time' => $date, 'to_worker_id' => $to_worker_id, 'to_connection_id' => $to_connection_id];
		} elseif (!empty($sUid)) {
			// 已uid为标识推送
			$data = ['type' => 'event', 'uid' => $sUid, 'content' => $content, 'time' => $date];
		} else {
			// 广播
			$data = ['type' => 'event', 'content' => $content, 'time' => $date];
		}
		
		$result = u_curl($this->url, 'POST', $data); // post方式发送
		return json($result);
    }
	
}
