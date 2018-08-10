<?php
/**
 * Created by PhpStorm.
 * User: CNDNS-JK
 * Date: 2018/1/19 0019
 * Time: 17:11
 */

namespace app\admin\controller;

use app\home\model\MemberModel;
use think\Db;
use think\Session;

class Member extends Base
{

    public function index()
    {
        if(input('leibie',1)==1){
            $this->assign('leibie','会员');
            $this->assign('p',0);


        }else{
            $this->assign('leibie','专家');
            $this->assign('p',1);

        }

        return $this->fetch();
    }

    /**
     * 添加会员
     * @author [leo]
     */
    public function getMemberList()
    {
        $username = input('username');//用户名
        $real_name = input('pid');//状态
        $phone = input('privilege');//privilege
        $map = [];//查询条件
        if ('' != $username) {
            $map['username'] = ['like', "%" . $username . "%"];
        }
        if ('' != $real_name) {
            $map['status'] = $real_name;
        }
        if ('' != $phone) {
            $map['privilege'] = $phone;
        }
        $member = new MemberModel();
        $count = $member->getAllCountByWhere($map);//计算总页面

//        分页必备参数
        $Nowpage = input('get.page') ? input('get.page'):1;
        $limits = input('get.limit');// 获取总条数
        $lists = $member->getMemberByWhere($map, $Nowpage, $limits);

        return json(['code' => 0, 'msg' => '', 'count' => $count, 'data' => $lists]);
    }

    /**
     * 添加会员
     * @author [leo]
     */
    public function add()
    {
        if (request()->isAjax()) {

            $param = input('post.');
            $param['password'] = md5(md5($param['password']) . config('auth_key'));
            $member = new MemberModel();
            $flag = $member->insertMember($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        return $this->fetch();
    }


    /**
     * 编辑会员
     * @author [leo]
     */
    public function edit()
    {
        $member = new MemberModel();
        if (request()->isAjax()) {
            $param = input('post.');
            if (empty($param['password'])) {
                unset($param['password']);
            } else {
                $param['password'] = md5(md5($param['password']) . config('auth_key'));
            }
            $flag = $member->editMember($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }

        $id = input('param.id');
        $this->assign([
            'member' => $member->getOneMember($id),
        ]);
        return $this->fetch();
    }
    public function edits()
    {
        $member = new MemberModel();
        if (request()->isAjax()) {
            $param = input('post.');
            $flag = $member->editMember($param);



            return json(["code" => $flag["code"], "data" => $flag["data"], "msg" => $flag["msg"]]);
        }

        $id = input('param.id');
        $this->assign([
            'member' => $member->getOneMember($id),
        ]);
        return $this->fetch();
    }
    /**
     * 删除会员信息
     * @return \think\response\Json
     */
    public function del()
    {
        $id = input('param.id');
        $member = new MemberModel();
        $flag = $member->delMember($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    /**
     * 修改会员的状态
     * @return \think\response\Json
     */
    public function member_status()
    {
        $id = input('param.id');
        $status = Db::name('member')->where('manid', $id)->value('status');//判断当前状态情况
        if ($status == 1) {
            $flag = Db::name('member')->where('manid', $id)->setField(['status' => 0]);
            return json(['code' => 1, 'data' => $flag['data'], 'msg' => '已冻结']);
        } else {
            $flag = Db::name('member')->where('manid', $id)->setField(['status' => 1]);
            return json(['code' => 0, 'data' => $flag['data'], 'msg' => '已解冻']);
        }

    }

    /**
     * 删除选中的记录
     * @return \think\response\Json
     */
    public function delSelecteds()
    {
        $ids = input('param.ids');
        return deletes('member', $ids,'manid');//物理删除
    }

    /**
     * 修改密码
     * @return mixed|\think\response\Json
     */
    public function editpwd()
    {
        if (request()->isAjax()) {
            $param = input('post.');
            $user = Db::name('member')->where('manid=' . session('uid'))->find();
            if (md5(md5($param['old_password']) . config('auth_key')) != $user['password']) {
                return json(['code' => -1, 'url' => '', 'msg' => '旧密码错误']);
            } else {
                $pwd['password'] = md5(md5($param['password']) . config('auth_key'));
                Db::name('member')->where('manid=' . $user['id'])->update($pwd);
                return json(['code' => 1, 'url' => '/home/index/index', 'msg' => '密码修改成功']);
            }
        }
        return $this->fetch();
    }

    /**
     * 找回密码
     * @return \think\response\Json
     */
    public function forgot_password()
    {
        $param = input('post.');
        if ($param['code'] !== '') {
            if ($param['code'] != session('phoneCode')) {
                Session::delete('phoneCode');
                return json(['code' => -2, 'data' => '', 'msg' => '短信验证码错误']);
            } else {
                $user = Db::name('member')->where('manid=' . session('uid'))->find();
                if (md5(md5($param['old_password']) . config('auth_key')) != $user['password']) {
                    return json(['code' => -1, 'url' => '', 'msg' => '旧密码错误']);
                } else {
                    $pwd['password'] = md5(md5($param['password']) . config('auth_key'));
                    Db::name('member')->where('manid=' . $user['id'])->update($pwd);
                    return json(['code' => 1, 'url' => '/home/index/index', 'msg' => '密码修改成功']);
                }
            }
        } else {
            return json(['code' => -3, 'data' => '', 'msg' => '请输入短信验证码']);
        }
    }
}