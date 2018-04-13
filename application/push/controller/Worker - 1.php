<?php
namespace app\push\controller;

use think\worker\Server;

class Worker extends Server
{
    protected $socket = 'websocket://www.workerman.com:2346';
    protected $userId = 0;
	
    public function initialize()
    {
        parent::initialize();
		// ====这里进程数必须必须必须设置为1====
		$this->worker->count = 1;
		// 新增加一个属性，用来保存uid到connection的映射(uid是用户id或者客户端唯一标识)
		$this->worker->uidConnections = array();
    }

    /**
     * 收到信息
     * @param $connection
     * @param $data
     */
    public function onMessage($connection, $data)
    {
        // 其它逻辑，针对某个uid发送 或者 全局广播
		// 假设消息格式为 uid:message 时是对 uid 发送 message
		// uid 为 all 时是全局广播
		list($recvUid, $message) = explode(':', $data);
		// 全局广播
		if($recvUid == 'all')
		{
			$this->broadcast($message);
		}
		// 给特定uid发送
		else
		{
			$this->sendMessageByUid($recvUid, $message);
		}
		
		// foreach ($this->worker->connections as $conn) {
			// $conn->send("用户[{$connection->uid}] 说: $data");
		// }
    }

    /**
     * 当连接建立时触发的回调函数
     * @param $connection
     */
    public function onConnect($connection)
    {
		if(!isset($connection->uid))
		{
			// 没验证的话把第一个包当做uid（这里为了方便演示，没做真正的验证）
			$connection->uid = ++$this->userId;
			/* 保存uid到connection的映射，这样可以方便的通过uid查找connection，
			 * 实现针对特定uid推送数据
			 */
			$this->worker->uidConnections[$connection->uid] = $connection;
			// return $connection->send('login success, your uid is ' . $connection->uid);
			foreach ($this->worker->connections as $conn) {
				$conn->send("用户[{$connection->uid}] 已上线");
			}
		}
    }

    /**
     * 当连接断开时触发的回调函数
     * @param $connection
     */
    public function onClose($connection)
    {
		if(isset($connection->uid))
		{
			// 连接断开时删除映射
			unset($this->worker->uidConnections[$connection->uid]);
		}
		foreach ($this->worker->connections as $conn) {
			$conn->send("用户[{$connection->uid}] 已登出");
		}
    }

    /**
     * 当客户端的连接上发生错误时触发
     * @param $connection
     * @param $code
     * @param $msg
     */
    public function onError($connection, $code, $msg)
    {
        echo "error $code $msg\n";
    }

    /**
     * 每个进程启动
     * @param $worker
     */
    public function onWorkerStart($worker)
    {
		
    }
	
	/**
     * 向所有验证的用户推送数据
     * @param $message
     */
	function broadcast($message)
	{
	   foreach($this->worker->uidConnections as $connection)
	   {
			$connection->send($message);
	   }
	}
	
	/**
     * 针对uid推送数据
     * @param $uid
     * @param $message
     */
	function sendMessageByUid($uid, $message)
	{
		if(isset($this->worker->uidConnections[$uid]))
		{
			$connection = $this->worker->uidConnections[$uid];
			$connection->send($message);
		}
	}
}
