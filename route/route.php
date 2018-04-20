<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

Route::get('think', function () {
    return 'hello,ThinkPHP5!';
});
Route::get('hello/:name', 'index/hello');

Route::get('/', 'index/index/index');
Route::get('message', 'index/index/message');
Route::get('login', 'index/login/login');
Route::post('login/doLogin', 'index/login/doLogin');
Route::get('logout', 'index/login/logout');

Route::get('user/list', 'index/userIm/userList');
Route::get('user/add', 'index/userIm/userAdd');
Route::post('user/save', 'index/userIm/userSave');
Route::get('user/edit', 'index/userIm/userEdit');
Route::post('user/update', 'index/userIm/userUpdate');
Route::get('user/delete', 'index/userIm/userDel');

Route::get('push/show', 'index/push/index');
Route::get('push/push', 'index/push/push');

Route::get('start/show', 'index/start/index');
Route::get('start/push', 'index/start/push');
Route::post('start/link', 'index/start/socketUrl');

return [

];
