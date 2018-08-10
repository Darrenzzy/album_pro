<?php
/**
 * Created by PhpStorm.
 * User: CNDNS-JK
 * Date: 2018/1/19 0019
 * Time: 16:47
 */

namespace app\home\controller;

use think\Controller;
use think\Db;
use org\Verify;
use think\Session;
use app\home\model\MemberModel;

/**
 * 注册类
 * Class Register
 * @package app\home\controller
 */
class Register extends Controller
{
    /**
     * 注册页面
     * @return mixed
     */
    public function index()
    {
        return $this->fetch();
    }

    /**
     * 用户注册操作
     * @return \think\response\Json
     */
    public function register()
    {
        $param = input('post.');
        Db::startTrans();//开启事务
        $param['password'] = md5(md5($param['password']) . config('auth_key'));
        if ($param['code'] !== '') {
            if ($param['code'] != session('phoneCode')) {
                Session::delete('phoneCode');
                return json(['code' => -3, 'data' => '', 'msg' => '短信验证码错误']);
            } else {
                $cModel = new MemberModel();
                $flag = $cModel->insertMember($param);
                if ($flag['code'] == 1) {
                    Db::commit();//提交事务
                    return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => '注册成功']);
                } else {
                    Db::rollback();//回滚事务
                    return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => '注册失败']);
                }

            }
        } else {
            Db::rollback();//回滚
            return json(['code' => -4, 'data' => '', 'msg' => '请输入验证码']);

        }
    }

    /**
     * 验证码
     * @return
     */
    public function checkVerify()
    {
        $verify = new Verify();
        $verify->imageH = 40;
        $verify->imageW = 100;
        $verify->codeSet = '0123456789';
        $verify->length = 4;
        $verify->useNoise = false;
        $verify->fontSize = 14;
        return $verify->entry();
    }

    /**
     * 验证验证码是否正确
     * @return \think\response\Json
     */
    function VerifyCode()
    {
        $code = input('param.code');//获取验证码
        $verify = new Verify();
        if (!$verify->check($code)) {
            return json(['code' => 0, 'url' => '', 'msg' => '验证码错误']);
        } else {
            return json(['code' => 1, 'url' => '', 'msg' => '验证码正确']);
        }
    }

    /**
     * 发送短信验证码
     */
    function sendSms()
    {
        $phone = input('param.phone');//获取手机号码
        if (!empty($phone)) {
            date_default_timezone_set('Asia/Shanghai');
            //-------------请填写以下信息--------------
            $options['username'] = 'sms243671';//短信系统平台用户名即管理名称
            $options['time'] = time() - 8 * 3600;//当前格林尼治时间戳
            $options['mobile'] = $phone;//收信人手机号码
            $password = 'qik7z5';//短信系统平台用户登录密码
            $key = '0702c6926f65029b5bca664f0e74df7f';//在接口触发页面可以获取
            $code = getRandomString(6, '0123456789');//生成6位验证码
            $content = "尊敬的用户,您的6位验证码是$code,请妥善保管,并在5分钟内完成验证。";//短信正文
            $suffix = '大汉体育';//短信后缀签名， 例：公司名称
            //-------------请填写以下信息--------------

            //短信内容 = 短信正文+短信签名
            $options['content'] = urlencode($content . "【" . $suffix . "】");//需要中文输入法的左右括号，签名字数要在3-8个字
            $options['authkey'] = md5($options['username'] . $options['time'] . md5($password) . $key);
            $url = 'http://sms.edmcn.cn/api/cm/trigger_mobile.php';//接口地址
            $flag = sendSms($url, $options);//返回1成功
            if ($flag == 1) {
                session('phoneCode', $code);
            }
        }
    }
}