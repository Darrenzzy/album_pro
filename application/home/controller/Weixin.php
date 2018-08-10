<?php

namespace app\home\controller;

/**
 * 微信授权相关接口
 *
 * @link http://www.phpddt.com
 */
class Weixin
{

//高级功能-》开发者模式-》获取
    private $app_id = 'wx6e7b55f48c8b5878'; //公众号appid
    private $app_secret = '941cd769e8622aff204cc0b7786e6bac'; //公众号app_secret
    private $redirect_uri = 'http://www.jinguwan.wang/home/weixin/index'; //授权之后跳转地址
    private $url = 'http://www.jinguwan.wang/home/kehu/index'; //获取用户信息之后跳转地址

    //获取微信用户信息
    public function index()
    {
        $code = $_GET['code'];
        $state = $_GET['state'];

        //换成自己的接口信息
        $appid = 'wx6e7b55f48c8b5878';
        $appsecret = '941cd769e8622aff204cc0b7786e6bac';
        if (empty($code)) $this->error('授权失败');
        $token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $appid . '&secret=' . $appsecret . '&code=' . $code . '&grant_type=authorization_code';

        //替换打开url保存字符串方式  利用判断！
        if(function_exists('file_get_contents')) {
            $file_contents = file_get_contents($token_url);
        } else {
            $ch = curl_init();
            $timeout = 5;
            curl_setopt ($ch, CURLOPT_URL, $token_url);
            curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $file_contents = curl_exec($ch);
            curl_close($ch);
        }
        $token = json_decode($file_contents);
//        $token = json_decode(file_get_contents($token_url));
//        dump($token);exit;
        if (isset($token->errcode)) {
            echo '<h1>错误1：</h1>' . $token->errcode;
            echo '<br/><h2>错误信息：</h2>' . $token->errmsg;
            exit;
        }
        $access_token_url = 'https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=' . $appid . '&grant_type=refresh_token&refresh_token=' . $token->refresh_token;
        //转成对象
        $access_token = json_decode(file_get_contents($access_token_url));
        if (isset($access_token->errcode)) {
            echo '<h1>错误2：</h1>' . $access_token->errcode;
            echo '<br/><h2>错误信息：</h2>' . $access_token->errmsg;
            exit;
        }
        $user_info_url = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $access_token->access_token . '&openid=' . $access_token->openid . '&lang=zh_CN';
//	 dump($user_info_url);exit;
        //转成对象
        $user_info = json_decode(file_get_contents($user_info_url));
        if (isset($user_info->errcode)) {
            echo '<h1>错误3：</h1>' . $user_info->errcode;
            echo '<br/><h2>错误信息：</h2>' . $user_info->errmsg;
            exit;
        }

        /*
         * 示例
         *  ["openid"] => string(28) "oks_uwvlBCSqKyfhGv-1H9G9pvSk"
  ["nickname"] => string(9) "路人乙"
  ["sex"] => int(1)
  ["language"] => string(5) "zh_CN"
  ["city"] => string(6) "南阳"
  ["province"] => string(6) "河南"
  ["country"] => string(6) "中国"
  ["headimgurl"] => string(124) "http://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTI9X140JXPuaF6rTygls9RsNeJ8CicVdeeomA2ibdWqKR3xXHv1cYdgz3mRpm5jB47pzp4wMKqtDg7w/132"
  ["privilege"] => array(0) {
  }
  ["unionid"] => string(28) "oztoZsy6q4c2_bdjvCy7_Q4I7Vto"

         * */
        $wxunionid = $user_info->unionid;//唯一标识码
        $wxopenid = $user_info->openid;//微信id
        $wxnickname = $user_info->nickname;//微信名称
        $wxsex = $user_info->sex;//性别
        $wxcity = $user_info->city;//省
        $wxcountry = $user_info->country;//国
        $wxheadimgurl = $user_info->headimgurl;//微信头像
//        dump($user_info);die;

//        state用来区别用户和代理商  目前都一样
        if ($state == 1) {

            echo "<script>location.href='http://www.jinguwan.wang/home/Login/wxlogin?wxheadimgurl=" . $wxheadimgurl . "&wxopenid=" . $wxopenid . "&wxnickname=" . $wxnickname . "&wxsex=" . $wxsex . "&wxcity=" . $wxcity . "&wxcountry=" . $wxcountry . "'</script>";
            exit;
        } else if ($state == 2) {

            echo "<script>location.href='http://www.jinguwan.wang/home/Login/wxlogin?wxheadimgurl=" . $wxheadimgurl . "&wxopenid=" . $wxopenid . "&wxnickname=" . $wxnickname . "&wxsex=" . $wxsex . "&wxcity=" . $wxcity . "&wxcountry=" . $wxcountry . "'</script>";
//            echo "<script>location.href='http://wxsc.aqitai.com/Wechat/BangdingDaili/index?wxheadimgurl=" . $wxheadimgurl . "&wxopenid=" . $wxopenid . "&wxnickname=" . $wxnickname . "&wxsex=" . $wxsex . "&wxcity=" . $wxcity . "&wxcountry=" . $wxcountry . "'</script>";
            exit;
        }


    }



    /**
     * 获取微信授权链接
     *
     * @param string $redirect_uri 跳转地址
     * @param mixed $state 参数
     */
    public function get_authorize_url($state=1)
    {
        $redirect_uri = urlencode($this->redirect_uri);
        echo "<script>location.href='https://open.weixin.qq.com/connect/oauth2/authorize?appid={$this->app_id}&redirect_uri={$redirect_uri}&response_type=code&scope=snsapi_userinfo&state={$state}#wechat_redirect'</script>";exit;
//        return "";
    }

    /**
     * 获取授权token
     *
     * @param string $code 通过get_authorize_url获取到的code
     */
    public function get_access_token($code)
    {
        $token_url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$this->app_id}&secret={$this->app_secret}&code={$code}&grant_type=authorization_code";
        $token_data = $this->http($token_url,'POST');


        if ($token_data[0] == 200) {
            return json_decode($token_data[1], TRUE);
        }

        return FALSE;
    }

    /**
     * 获取授权后的微信用户信息
     *
     * @param string $access_token
     * @param string $open_id
     */
    public function get_user_info($access_token, $open_id)
    {
        if ($access_token && $open_id) {
            $info_url = "https://api.weixin.qq.com/sns/userinfo?access_token={$access_token}&openid={$open_id}&lang=zh_CN";
            $info_data = $this->http($info_url,'POST');

            if ($info_data[0] == 200) {
                return json_decode($info_data[1], TRUE);
            }
        }

        return FALSE;
    }

    public function http($url, $method, $postfields = null, $headers = array(), $debug = false)
    {
        $ci = curl_init();
        /* Curl settings */
        curl_setopt($ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ci, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ci, CURLOPT_TIMEOUT, 30);
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);

        switch ($method) {
            case 'POST':
                curl_setopt($ci, CURLOPT_POST, true);
                if (!empty($postfields)) {
                    curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
                    $this->postdata = $postfields;
                }
                break;
        }
        curl_setopt($ci, CURLOPT_URL, $url);
        curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ci, CURLINFO_HEADER_OUT, true);

        $response = curl_exec($ci);
        $http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);

        if ($debug) {
            echo "=====post data======\r\n";
            var_dump($postfields);

            echo '=====info=====' . "\r\n";
            print_r(curl_getinfo($ci));

            echo '=====$response=====' . "\r\n";
            print_r($response);
        }
        curl_close($ci);
        return array($http_code, $response);
    }

}