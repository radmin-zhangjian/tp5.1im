<?php
namespace app\push\controller;

use think\worker\Server;
use think\facade\Cache;

class Worker extends Server
{
    // protected $socket = 'websocket://www.workerman.com:2346';
    protected $socket = 'websocket://123.56.192.63:2346';
    protected $cnt = 0;
	
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
		// 客户端传递的是json数据
        $message_data = json_decode($data, true);
        if (!$message_data) {
            return ;
        }
		
		// 根据类型执行不同的业务
        switch ($message_data['type']) {
            // 客户端回应服务端的心跳
            case 'pong':

                return ;        
            
            // 客户端登录 message格式: {type:login, client_name:xx, room_id:1}
            case 'login':
            	
                // 判断当前客户端是否已经验证,即是否设置了uid
                if (!isset($connection->uid)) {
					// 绑定用户
					if ($message_data['client_name'] == '游客') {
						$connection->uid = '游客' . ++$this->cnt;
					} else {
						$connection->uid = $message_data['client_name'];
					}
					$this->worker->uidConnections[$connection->uid] = $connection;
					$message = json_encode(['type' => 'login', 'data' => $message_data['client_name'].'登陆成功']);
					$this->broadcast($message);
			    }
				break;

            case 'say':
				
				// 广播对象
				$name = $message_data['client_name'];
				if ($connection->uid == $name || empty($name) || $name == null || !$name) {
					$recvUid = 'all';
				} else {
					$recvUid = $message_data['client_name'];
				}
				// send 内容
				$message = json_encode(['type' => 'say', 'data' => $message_data['data']]);
				
				if ($recvUid == 'all') {
					// 全局广播
					$this->broadcast($message);
				} else if($recvUid == 'not') {
					// 除自己以外
					$this->broadcast($message, [$connection->uid]);
				} else {
					// 给特定uid发送
					$this->sendMessageByUid($recvUid, $message);
				}
				break;
        }
		
    }

    /**
     * 当连接建立时触发的回调函数
     * @param $connection
     */
    public function onConnect($connection)
    {
		// if(!isset($connection->uid))
		// {
			// // 没验证的话把第一个包当做uid（这里为了方便演示，没做真正的验证）
			// $connection->uid = ++$this->cnt;
			// /* 保存uid到connection的映射，这样可以方便的通过uid查找connection，
			 // * 实现针对特定uid推送数据
			 // */
			// $this->worker->uidConnections[$connection->uid] = $connection;
			// // return $connection->send('login success, your uid is ' . $connection->uid);
			// foreach ($this->worker->connections as $conn) {
				// $conn->send("用户[{$connection->uid}] 已上线");
			// }
		// }
		
		$message = json_encode(['type' => 'say', 'data' => '连线成功...']);
		$connection->send($message);
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
			$message = json_encode(['type' => 'say', 'data' => '用户['.$connection->uid.'] 已登出']);
			$conn->send($message);
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
     * @param $user
     */
	function broadcast($message, $user = [])
	{
		foreach($this->worker->uidConnections as $connection)
		{
			if (!in_array($connection->uid, $user)) {
				$connection->send($message);
			}
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
