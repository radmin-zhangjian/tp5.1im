<?php
namespace app\common;

use think\Controller;

class BaseController extends Controller
{
	// session
	protected $sessionInfo = null;
	
    public function initialize()
    {
		$this->sessionInfo = session(md5('user_id'));
		if (isset($this->sessionInfo) && $this->sessionInfo['id']) {
			
		} else {
			header("Location:" . '/login');
    		exit();
		}
    }
	
}
