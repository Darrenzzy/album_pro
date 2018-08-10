<?php

namespace app\home\controller;

use think\Controller;
use think\session;
use think\Db;

class Base extends Controller
{
    public function _initialize()
    {

//        获取当前url
        $re = request();
        $his = $re->url();
        session('history', $his);


////        暂时开启用户登录
//        if (!isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
//            //非移动设备
//            if(!Session::has('home')){
//                $date = Db::name('member')
//                    ->find(72);
//                session('home',$date);
//            }
//        }

        
//        $auth = new \com\Auth();
//        $module     = strtolower(request()->module());
//        $controller = strtolower(request()->controller());
//        $action     = strtolower(request()->action());
//        $url        = $module."/".$controller."/".$action;
//
//        //跳过检测以及主页权限
//        if(session('uid')!=1){
//            if(!in_array($url, ['admin/index/index','admin/index/indexpage','admin/upload/upload','admin/index/uploadface'])){
//                if(!$auth->check($url,session('uid'))){
//                    $this->error('抱歉，您没有操作权限');
//                }
//            }
//        }
        

        $config = cache('db_config_data');

//        dump($_SESSION);
        if(!$config){
            $config = load_config();                          
            cache('db_config_data',$config);
        }
        config($config); 

        if(config('web_site_close') == 0 && session('uid') !=1 ){
            $this->error('站点已经关闭，请稍后访问~');
        }

        if(config('admin_allow_ip') && session('uid') !=1 ){          
            if(in_array(request()->ip(),explode('#',config('admin_allow_ip')))){
                $this->error('403:禁止访问');
            }
        }

    }


    public function is_login(){
        if(!session('home')){
            $this->redirect('/home/Weixin/get_authorize_url');
        }
    }



    // 获取当页面的路径
    public function get_url() {
        $sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
        $php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
        $path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
        $relate_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : $path_info);
        $url=$sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relate_url;
        return $url;
    }

}