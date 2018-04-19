<?php
namespace app\push\controller;

use think\worker\Server;
use think\worker\Lib\Timer;
use think\worker\Protocols\Http;
use Workerman\Worker;

class Push extends Server
{
	// socket 地址
    protected $socket = 'websocket://www.workerman.com:1234';
    // protected $socket = 'websocket://123.56.192.63:1234';
	/*
	 * 注意这里进程数必须设置为1，否则会报端口占用错误
	 * (php 7可以设置进程数大于1，前提是 $inner_text_worker->reusePort=true)
	 */
	protected $processes = 1;
	// 新增加一个属性，用来保存uid到connection的映射(uid是用户id或者客户端唯一标识)
	protected $uidConnections = array();
	
	
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
			
            // 客户端登录 message格式: {type:login, uid:xx, room_id:room_1}
            case 'login':
            	
                // 判断当前客户端是否已经验证,即是否设置了uid
                if (!isset($connection->uid)) {
					
					// 绑定用户
					if ($message_data['uid'] == '游客' || $message_data['uid'] == '游客') {
						$connection->uid = $message_data['uid'] . ++$this->cnt;
					} else {
						$connection->uid = $message_data['uid'];
					}
					
					$this->worker->uidConnections[$connection->uid] = $connection;
					$message = json_encode(['type' => 'login', 'data' => '用户['.$connection->uid.'] 链接成功']);
					$this->sendMessageByUid($connection->uid, $message); // 给自己
					
			    }
				break;

            case 'say':
				
				if (empty($message_data['data'])) {
					return ;
				}
				
				// 广播对象
				$aUid = $message_data['uid'];
				if ($connection->uid == $aUid || empty($aUid) || $aUid == null || !$aUid) {
					$recvUid = 'all';
				} else {
					$recvUid = $aUid;
				}
				// send 内容
				$sUid = $connection->uid;
				$msg = $message_data['data'];
				$date = date('Y-m-d H:i:s', time());
				$message = json_encode(['type' => 'say', 'uid' => $sUid, 'data' => $msg, 'time' => $date]);
				
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
		$message = json_encode(['type' => 'login', 'data' => '连线成功...']);
		$connection->send($message);
    }

    /**
     * 当连接断开时触发的回调函数
     * @param $connection
     */
    public function onClose($connection)
    {
		if (isset($connection->uid)) {
			// 连接断开时删除映射
			unset($this->worker->uidConnections[$connection->uid]);
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
		// 开启一个内部端口，方便内部系统推送数据，Text协议格式 文本+换行符
		$inner_text_worker = new Worker('text://www.workerman.com:5678');
		$inner_text_worker->onMessage = function($connection, $buffer)
		{
			// $data数组格式，里面有uid，表示向那个uid的页面推送数据
			$data = json_decode($buffer, true);
			
			// 推送对象
			$userName = $data['uid'];
			if (empty($userName) || $userName == null || !$userName) {
				$recvUid = 'all';
			} else {
				$recvUid = $userName;
			}
			
			// send
			if ($recvUid == 'all') {
				// 全局广播
				$ret = $this->broadcast($buffer);
			} else {
				// 给特定uid发送
				$ret = $this->sendMessageByUid($recvUid, $buffer);
			}
			
			// 返回推送结果
			$connection->send($ret ? 'ok' : 'fail');
			
		};
		
		// ## 执行监听 ##
		$inner_text_worker->listen();
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
		return true;
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
			return true;
		}
		return false;
	}
}
