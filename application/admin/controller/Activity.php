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
//use think\QRcode;//引入生成二维码

class Activity extends Base
{
//    定义表名称，共下面函数引用。
    public $tablename ="activity";
//    public $tableclass ="activity_class";
    public $tableclass ="photo_class";


    public function index()
    {

//        dump(config('domain'));
//        $code =  delete_dir_file('qrcode');
//       $code =  Qrcode(time(),'activity_class');
//dump($code);

//die;
//        if(input('leibie',1)!=1){
            $this->assign('leibie','活动管理');
            $this->assign('p',0);

//        }

//        读取用户列表
//        $condition['status'] = '1';
//        $conditions['status'] = '0';
        $db = Db::name($this->tableclass)
//            ->where($condition)
//            ->whereor($conditions)
            ->select();
        $this->assign('users',$db);
        return $this->fetch();
    }

    /**
     * 获取列表
     * @author [leo]
     */
    public function getList()
    {


//        $username = input('username');//用户名
//        $real_name = input('pid');//状态
        $phone = input('cid');//相册id
        $map = [];//查询条件
//        if ('' != $username) {
//            $map['username'] = ['like', "%" . $username . "%"];
//        }
//        if ('' != $real_name) {
//            $map['status'] = $real_name;
//        }
        if ('' != $phone) {
            $map['cid'] = $phone;
        }
        $member = DB::name($this->tablename);
        $count = $member->count();//计算总页面
//        分页必备参数
//        $Nowpage = input('get.page') ? input('get.page'):1;
//        $limits = input('get.limit');// 获取总条数
        $lists = $member
            ->alias('A')
            ->field('A.*,B.classname')
            ->where($map)
            ->order('id desc')
            ->join('photo_class B','A.cid=B.id','left')
            ->select();

      delete_dir_file('qrcode/');//初始化删除二维码页面
        foreach($lists as $key=>$vo){
            $url =  config('domain').'home/index/activity?id='.$vo['id'];
            $lists[$key]['qrcode']  =  Qrcode($vo['id'].uniqid(),$url);//生成二维码 uniqid()唯一标示随机数

        }

        return json(['code' => 0, 'msg' => '', 'count' => $count, 'data' => $lists]);
    }


    /**
     * 修改信息
     * @author [leo]
     */
    public function edit()
    {
        if (request()->isPost()) {
            $param = input('post.');
            if(isset($param['pic'])){
                if($param['pic']!=''){
                    $param['picname'] = $param['pic'];
                }
            }
            $param['table'] = $this->tablename;
            $flag = DBsaves($param['table'],$param,['table'=>0,'file'=>0,'pic'=>0]);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }

        //读取单条信息
        $id['id'] = input('param.id');
        if($id==''){
            return json(['code' => 0, 'data' =>0, 'msg' => '未获取到数据']);
        }
        $data = DBdateone($this->tablename,$id);
        $this->assign('info',$data);

        //      新闻栏目列表
        $datalist = DBdate($this->tableclass,'',100,'','id desc');
        $this->assign('datalist',$datalist);
        return $this->fetch();

    }


    /**
     * 修改信息
     * @author [leo]
     */
    public function editclass()
    {
        if (request()->isPost()) {
            $param = input('post.');
            $param['table'] = $this->tableclass;
            $flag = DBsaves($param['table'],$param,['table'=>0]);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }


        return $this->fetch();

    }

//    新闻栏目
    public function classindex()
    {
            $this->assign('leibie','相册管理');
            $this->assign('p',0);



//        读取用户列表
        $condition['status'] = '1';
        $conditions['status'] = '0';
        $db = Db::name('member')
            ->where($condition)
            ->whereor($conditions)
            ->select();
        $this->assign('users',$db);
        return $this->fetch('/album/newsclass');
    }

//    新闻栏目获取
    public function newsclassget()
    {
        $member = DB::name($this->tableclass);

        $count = $member->count();//计算总页面
        $condition = [];

        if(input('?get.uid')&&input('get.uid')!=0){
        $condition['uid'] = input('get.uid');
        }

//        分页必备参数
        $Nowpage = input('get.page') ? input('get.page'):1;
        $limits = input('get.limit');// 获取总条数
        $lists = $member
            ->alias('A')
            ->field('A.*,B.username as user ')
            ->join('member B','A.uid=B.manid','left')
//            ->page($Nowpage, $limits)
            ->order('id desc')
            ->where($condition)
            ->select();
        return json(['code' => 0, 'msg' => '', 'count' => $count, 'data' => $lists]);
    }


    /**
     * 删除会员信息
     * @return \think\response\Json
     */
    public function del()
    {
        $id = input('param.id');
        if($id==''){
            return json(['code' => 0, 'data' => 0,'msg' => '未获取到数据']);
        }
        $flag = DBdel($this->tablename,$id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    /**
     * 删除会员信息
     * @return \think\response\Json
     */
    public function delclass()
    {
        $id = input('param.id');
        if($id==''){
            return json(['code' => 0, 'data' => $flag['data'], 'msg' => '未获取到数据']);
        }
        $flag = DBdel($this->tableclass,$id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }
    /**
     * 删除选中的记录
     * @return \think\response\Json
     */
    public function delSelecteds()
    {
        $ids = input('param.ids');
        return deletes($this->tablename, $ids);//物理删除
    }

    /**
     * [roleAdd 添加]
     * @return [type] [description]
     * @author [leo] [3093230151@qq.com]
     */
    public function add()
    {
        if(request()->isAjax()){
            $params = input('post.');

            $params['picname'] = $params['pic'];
            $flag = DBaddzzy($this->tablename,$params,['file'=>0,'pic'=>0]);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }

//        列表
        $datalist = DBdate($this->tableclass,'',100,'','id desc');
        $this->assign('datalist',$datalist);
        return $this->fetch();
    }

    /**
     * [roleAdd 添加]
     * @return [type] [description]
     * @author [leo] [3093230151@qq.com]
     */
    public function addclass()
    {
        if(request()->isAjax()){
            $param = input('post.');
            $flag = DBadd($this->tableclass,$param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }


//        列表
        $datalist = DBdate('member','',100,'','manid desc');
        $this->assign('datalist',$datalist);
        return $this->fetch();
    }


    /**
     * 修改的状态
     * @return \think\response\Json
     */
    public function member_status()
    {
        $id = input('param.id');
        $status = Db::name($this->tablename)->where('id', $id)->value('shenhe');//判断当前状态情况
        if ($status == 1) {
            $flag = Db::name($this->tablename)->where('id', $id)->setField(['shenhe' => 0]);
            return json(['code' => 1, 'data' => $flag['data'], 'msg' => '已冻结']);
        } else {
            $flag = Db::name($this->tablename)->where('id', $id)->setField(['shenhe' => 1]);
            return json(['code' => 0, 'data' => $flag['data'], 'msg' => '已解冻']);
        }

    }


}