<?php
namespace app\index\controller;

use app\common\BaseController;

class Push extends BaseController
{
    public function index()
    {
		$this->assign('user_name', $this->sessionInfo['user_name']);
		return $this->fetch();
    }
	
    public function push()
    {
		// 建立socket连接到内部推送端口
		$client = stream_socket_client('tcp://www.workerman.com:5678', $errno, $errmsg, 1);
		// 推送的数据，包含uid字段，表示是给这个uid推送  all为广播信息
		$data = array('type' => 'say', 'uid' => 'aaa', 'data' => '我是测试推送内容', 'time' => date('Y-m-d H:i:s', time()));
		// 发送数据，注意5678端口是Text协议的端口，Text协议需要在数据末尾加上换行符
		fwrite($client, json_encode($data)."\n");
		// 读取推送结果
		echo fread($client, 8192);
    }
	
}
