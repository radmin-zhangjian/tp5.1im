<?php
namespace app\push\controller;

use think\worker\Server;
use think\worker\Lib\Timer;
use think\worker\Protocols\Http;
use Workerman\Worker;
use Channel\Server as ChannelServer;
use Channel\Client as ChannelClient;

class start extends Server
{
	// socket 地址
    // protected $socket = 'websocket://www.workerman.com:4239';
    // protected $sip = 'www.workerman.com';
    // protected $wip = 'www.workerman.com';
	protected $socket = 'websocket://123.56.192.63:4239';
    protected $sip = 'im.hezongli.com';
    protected $wip = 'im.hezongli.com';
	
	// 进程数
	protected $processes = 4;
	// 新增加一个属性，用来保存uid到connection的映射(uid是用户id或者客户端唯一标识)
	protected $uidConnections = array();
	
	/**
     * 架构函数
     * @access public
     */
	public function __construct()
	{
		// 初始化一个Channel服务端
		$this->channel_server = new ChannelServer($this->sip, 2206);
		
		// 实例化 Worker
		// $this->worker = new Worker($this->socket);
		parent::__construct();
		// 初始化 Worker
		// $this->init();
	}
	
	protected function init()
	{
		$this->worker->count = $this->processes;
		$this->worker->name = 'pusher';
		
		// 设置回调
        // foreach (['init', 'onWorkerStart', 'onConnect', 'onMessage', 'onClose', 'onError', 'onBufferFull', 'onBufferDrain', 'onWorkerStop', 'onWorkerReload'] as $event) {
            // if (method_exists($this, $event)) {
                // $this->worker->$event = [$this, $event];
            // }
        // }
		
		// 用来处理http请求，向任意客户端推送数据，需要传workerID和connectionID
		$this->http_worker = new Worker('http://'.$this->sip.':4238');
		$this->http_worker->name = 'publisher';
		
		$this->http_worker->onWorkerStart = function()
		{
			ChannelClient::connect($this->wip, 2206);
		};
		$this->http_worker->onMessage = function($connection, $data)
		{
			$eventData = $_POST;
			$eventData['content'] = trim($eventData['content']);
			$connection->send('ok');
			if(empty($eventData['content'])) return;
			
			// 向某个worker进程中某个连接推送数据
			if (isset($eventData['to_worker_id']) && isset($eventData['to_connection_id'])) {
				$event_name = $eventData['to_worker_id'];
				$to_connection_id = $eventData['to_connection_id'];
				ChannelClient::publish($event_name, array(
				   'to_connection_id' => $to_connection_id,
				   'content'          => json_encode($eventData)
				));
			}
			// 向某个 UID 推送
			elseif (isset($eventData['uid'])) {
				$this->sendMessageByUid($eventData['uid'], json_encode($eventData));
			}
			// 全局广播数据
			else {
				$this->broadcast(json_encode($eventData));
			}
		};
		
		// Run worker
        // Worker::runAll();
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
					$message = json_encode(['type' => 'login', 'content' => '用户['.$connection->uid.'] 链接成功']);
					$this->sendMessageByUid($connection->uid, $message); // 给自己
					
					
					// 为每个 UID 订阅独立信息 ======
					$worker = $this->worker;
					// Channel客户端连接到Channel服务端
					ChannelClient::connect($this->wip, 2206);
					// 以uid为事件名称
					$event_name = $connection->uid;
					// 订阅uid事件并注册事件处理函数
					ChannelClient::on($event_name, function($event_data)use($worker) {
						$uid = $event_data['uid'];
						$message = $event_data['content'];
						$to_connection = $worker->uidConnections[$uid];
						$to_connection->send($message);
					});
					
			    }
				break;

            case 'say':
				
				if (empty($message_data['content'])) {
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
				$msg = $message_data['content'];
				$date = date('Y-m-d H:i:s', time());
				$message = json_encode(['type' => 'say', 'uid' => $sUid, 'content' => $msg, 'time' => $date]);
				
				// Channel客户端连接到Channel服务端
				ChannelClient::connect($this->wip, 2206);
				
				if ($recvUid == 'all') {
					// 全局广播
					$this->broadcast($message);
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
		$msg = "workerID:{$this->worker->id} connectionID:{$connection->id} connected\n";
		echo $msg;
		$message = json_encode(['type' => 'login', 'content' => $msg . '连线成功...']);
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
		// Channel客户端连接到Channel服务端
		ChannelClient::connect($this->wip, 2206);
		// 以自己的进程id为事件名称
		$event_name = $worker->id;
		// 订阅worker->id事件并注册事件处理函数
		ChannelClient::on($event_name, function($event_data)use($worker) {
			$to_connection_id = $event_data['to_connection_id'];
			$message = $event_data['content'];
			if (!isset($worker->connections[$to_connection_id])) {
				echo "connection not exists\n";
				return;
			}
			$to_connection = $worker->connections[$to_connection_id];
			$to_connection->send($message);
		});

		// 订阅广播事件
		$event_name = 'all';
		// 收到广播事件后向当前进程内所有客户端连接发送广播数据
		ChannelClient::on($event_name, function($event_data)use($worker) {
			$message = $event_data['content'];
			foreach ($worker->connections as $connection) {
				$connection->send($message);
			}
		});
    }
	
	/**
     * 向所有验证的用户推送数据
     * @param $message
     */
	function broadcast($message)
	{
		$event_name = 'all';
		ChannelClient::publish($event_name, array(
		   'content'          => $message
		));
		
		return true;
	}
	
	/**
     * 针对uid推送数据
     * @param $uid
     * @param $message
     */
	function sendMessageByUid($uid, $message)
	{
		$event_name = $uid;
		ChannelClient::publish($event_name, array(
		   'uid'         	  => $uid,
		   'content'          => $message
		));
	}
}
