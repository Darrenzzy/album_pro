<?php
/**
 * Created by PhpStorm.
 * User: CNDNS-JK
 * Date: 2018/1/19 0019
 * Time: 17:00
 */

namespace app\home\controller;

use think\Controller;
use org\Verify;
use think\Db;
use app\home\model\MemberModel;
class Login extends Controller
{

    /**
     * 登录首页
     * @return mixed
     */
    public function index(){


        redirect('/home/Weixin/get_authorize_url');


//        return $this->fetch();
    }


    /**
     * 登录操作
     * @return \think\response\Json
     */
    public function doLogin(){
        $username = input("param.username");
        $password = input("param.password");
        $code = input("param.code");
        $verify = new Verify();
        if (!$code) {
            return json(['code' => -1, 'url' => '', 'msg' => '请输入验证码']);
        }
        if (!$verify->check($code)) {
            return json(['code' => -2, 'url' => '', 'msg' => '验证码错误','id'=>'#codeError']);
        }
        $hasUser = Db::name('member')->where('enterprise_name', $username)->find();
        if(empty($hasUser)){
            return json(['code' => -3, 'url' => '', 'msg' => '管理员不存在','id'=>'#usernameError']);
        }

        if(md5(md5($password) . config('auth_key')) != $hasUser['password']){
            return json(['code' => -4, 'url' => '', 'msg' => '账号或密码错误','id'=>'#passwordError']);
        }
        //更新管理员状态
        $param = [
            'login_num' => $hasUser['login_num'] + 1,
        ];

        Db::name('member')->where('id', $hasUser['id'])->update($param);
        return json(['code' => 1, 'url' => url('index/index'), 'msg' => '登录成功！']);
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


    // ==============================点击跳过====================================================//
    public function wxlogin(){		//新注册的用户
        $data['wxopenid']=input('wxopenid');//微信id
        $data['username']=input('wxnickname');//微信名称
        $data['wxsex']=input('wxsex');//性别
        $data['wxcity']=input('wxcity');//省
        $data['wxcountry']=input('wxcountry');//国
        $data['head']=input('wxheadimgurl');//微信头像
        $data['dt']=date('Y-m-d H:i:s',time());//注册时间
        $data['update_at']=date('Y-m-d H:i:s',time());//登录时间
        $data['login_ip']=get_client_ip();
        $wxopenid=input('wxopenid');//微信id
        if ($wxopenid!='') {

            $map['wxopenid'] = $wxopenid;
            $member = new MemberModel();
            $info = $member->getOnesMember($map);// 根据条件获取单条会员信息

            if ($info!='') {//已经登录过了
                $data1['update_at']=date('Y-m-d H:i:s',time());//登录时间
                $data1['login_ip']=get_client_ip();
               $data1['id']=$info['manid'];
                $member->editMember($data1);//保存登录信息
                session_start();

                session('home',$info);


//                echo "<script>location.href='".Url('index/personal')."'</script>";
//            	echo "<script>location.href='".session('wx_dangqian_url')."'</script>";
            }else{
                $info1 = $member->insertMember($data);//保存登录信息
                $map['wxopenid'] = $wxopenid;
                $info2 = $member->getOnesMember($map);// 根据条件获取单条会员信息
                if ($info1['code']==1) {
                    session_start();
                    session('home',$info2);

//                    echo "<script>location.href='".Url('index/personal')."'</script>";
                }
            }

            $lujing = $_SESSION['think']['history'];
            $denglulujing = 'index/personal';
            if ($lujing == '') {
                echo "<script>location.href='" . $denglulujing . "'</script>";
            }else{
                echo "<script>location.href='" . $lujing . "'</script>";

            }


        }

    }

    /**
     * 退出登录
     * @return
     */
    public function loginOut()
    {
        session(null);
        cache('db_config_data',null);//清除缓存中网站配置信息
        $this->redirect('/index');
    }



}