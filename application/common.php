<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

/**
 * @desc curl post请求
 * @param $url
 * @param $post
 * @return mixed
 */
function curl_post($url, $post)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	$data = curl_exec($ch);
	curl_close($ch);

	return $data;
}

function api_request($url, $data, $method = "GET")
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_URL, $url);
	// https
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	$method = strtoupper($method);
	if ($method == "POST") {
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type:application/json"));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	}
	$content = curl_exec($ch);
	curl_close($ch);
	return $content;
}

function u_curl( $url, $method, $params=array(), $header='')
{
    $curl = curl_init();//初始化CURL句柄  
    $timeout = 15;  
    curl_setopt($curl, CURLOPT_URL, $url);//设置请求的URL  
    curl_setopt($curl, CURLOPT_HEADER, false);// 不要http header 加快效率  
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,1); //设为TRUE把curl_exec()结果转化为字串，而不是直接输出  

    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);    // https请求 不验证证书和hosts  
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);  

    if($header==''){  
        $header [] = "Accept-Language: zh-CN;q=0.8";  
        curl_setopt ( $curl, CURLOPT_HTTPHEADER, $header );  
    }else{  
        curl_setopt ( $curl, CURLOPT_HTTPHEADER, $header );  
    }  

    curl_setopt ($curl, CURLOPT_CONNECTTIMEOUT, $timeout);//设置连接等待时间  
    switch ($method){  
        case "GET" :  
            curl_setopt($curl, CURLOPT_HTTPGET, true);break;  
        case "POST":  
            curl_setopt($curl, CURLOPT_POST, true);  
            curl_setopt($curl, CURLOPT_NOBODY, true);  
            curl_setopt($curl, CURLOPT_POSTFIELDS, $params);break;//设置提交的信息  
        case "PUT" :  
            curl_setopt ($curl, CURLOPT_CUSTOMREQUEST, "PUT"); 
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params));break;  
        case "DELETE":  
            curl_setopt ($curl, CURLOPT_CUSTOMREQUEST, "DELETE");  
            curl_setopt($curl, CURLOPT_POSTFIELDS, $params);break;  
    }  

    $data = curl_exec($curl);//执行预定义的CURL  
    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);//获取http返回值  
    curl_close($curl);  
    $res = $data; //var_dump($res);  
    return ['status' => $status, 'message' => $res];  
}