<?php

namespace app\home\controller;

use app\admin\model\Question2Model;
use app\admin\model\QuestionModel;
use app\home\model\MemberModel;
use think\Db;
use think\View;

class Index extends Base
{
    public function index()
    {
        return $this->fetch('/index');
    }

//活动详情页面
    public function activity()
    {
        $this->is_login();

        $this->assign('city', '上海');
        $this->assign('lng', '121.48789948569473');
        $this->assign('lat', '31.24916171');
        $this->assign('id', '2868');

        $id = input('param.id');

        $condition['A.id'] = $id;
//        取出活动信息详情
        $info = Db::name('activity')
            ->field('A.*,A.editor as zbf,
            case when A.shenhe =1 then "进行中"
            else "已关闭" end status
            ')
            ->alias('A')
            ->where($condition)
//            ->join('photo B','B.cid=A.cid')
            ->find();

        $map['A.cid'] = $info['cid'];

        session('cid', $info['cid']);//保存相册id，作为上传照片参数使用。

//        取出相册照片根据pid取出上传者信息
        $photo = Db::name('photo')
            ->field('A.*,r.username')
            ->alias('A')
//            ->join('think_member r', 'A.pid=r.manid','LEFT')
            ->join('think_member r', 'A.pid=r.manid')
            ->where($map)
            ->select();


        $this->assign('info', $info);
        $this->assign('photo', $photo);

        return $this->fetch();
    }

    /**
     * 相片评论
     * @return mixed
     */
    public function albumcomment()
    {
        //判断登录
        $this->is_login();

//        照片id
        $id = input('param.id');

        $condition['id'] = $id;
        $photo = Db::name('photo')
            ->where($condition)
            ->find();
        $this->assign('photo', $photo);//单个照片

//        如果未登录，则无法评论
        if (!session('home')) {
//            $this->redirect('/home/Weixin/get_authorize_url');
            $login = 0;//未登录
        } else {
            $login = 1;//登录
        }
        $this->assign('login', $login);//单个照片

        return $this->fetch();
    }

    /**
     * 相册评论列表
     * @return \think\response\Json
     */
    public function commentlist()
    {


        $map = [];
        $map['pid'] = input('param.pid');
        $map['status'] = 1;//审核通过状态
        $counts = DBcount('comment', $map);

        $page = $counts['data'];
        $page = $page / 10;//总页数


        $Nowpage = input('get.page') ? input('get.page') : 1;
        $map1['A.pid'] = input('param.pid');
        $map1['A.status'] = 1;//审核通过状态
        $ques = Db::name('comment')
            ->alias('A')
            ->field('A.*,r.username as username,r.head as head')
            ->join('think_member r', 'A.manid=r.manid')
//            ->join('(select qid ,count(1) num from think_ques2 where type=1  group by qid) A', 'A.qid = think_ques
//            .qid', 'LEFT')
            ->where($map1)
            ->order('A.dt desc')
            ->page($Nowpage, 10)
            ->select();

        return json(['pages' => $page, 'data' => $ques]);

    }


    /**
     * 用户进行评论
     * @return mixed
     */
    public function comment()
    {


        $data['note'] = input('param.note');
        $data['pid'] = input('param.pid');//照片id
        $data['manid'] = session('home.manid');//评论者的id
        $data['dt'] = date('Y-m-d H:i:s', time());
        $data['status'] = 1;

        $re = DBadd('comment', $data, '', '');

        return $re;

    }


    /**
     * 上传照片
     * @return mixed
     */
    public function albumupload()
    {


//        判断提交上传照片
        if (request()->isPost()) {
//            上传照片到数据库中
            $data = input('post.');
            $user = session('home');//获取用户个人数据
            $data['addtime'] = date('Y-m-d H:i:s', time());
            $data['pid'] = $user['manid'];//上传者id
            //判断上传到个人相册或活动相册id(若存在isPic怎判断为个人上传至个人相册，)
            if(!isset($data['isPic'])){

                $data['cid'] = session('cid');//相册id

            }


            $re = DBadd('photo', $data);
            return $re;

        }


        return $this->fetch();
    }


    /**
     * 个人相册列表
     * @return mixed
     */
    public function album()
    {
        $this->is_login();

        $user = session('home');//获取用户个人数据

        $condition['uid'] = $user['manid'];//在相册中寻找自己id的相册

        $album = DBdateone('photo_class',$condition);

        $datalist = [];
        if($album){
            $photo['cid'] = $album['id'];//相册id

//           $datalist +=  DBdate('photo',$photo,100,'','id desc');//取出个人相册全部照片
            $dbdata = DBdate('photo',$photo,100,'','id desc');//根据相册id取出个人相册全部照片
           $datalist = array_merge($datalist,$dbdata); //合并数组
        }

        $photolist['pid'] = $user['manid'];
        $dbdata =  DBdate('photo',$photolist,100,'','id desc');//根据个人id取出个人全部上传过的照片

        $datalist = array_merge($datalist,$dbdata); //合并数组
        $this->assign('datalist',$datalist);

        return $this->fetch();
    }

    /**
     * 个人增加
     * @return mixed
     */
    public function albumone()
    {

        return $this->fetch();
    }

    public function albumdel(){

        $id = input('id');

       $re =  DBdel('photo',$id);

        return $re;
    }

    /**
     * 相册列表
     * @return mixed
     */
    public function albumlist()
    {

        return $this->fetch();
    }


//    首页加载流
    public function indexload()
    {
        $map = [];
        $member = new QuestionModel();
        $count = $member->getAllCountByWhere($map);//全部数量
        $page = $count / 10;//总页数

        $map['think_ques.status'] = 1;

        $Nowpage = input('get.page') ? input('get.page') : 1;

        $ques = Db::name('ques')
            ->field('think_ques.*,r.username as username,r.head as head,case
when A.num is null then 0
ELSE A.num
 end num')
            ->join('think_member r', 'think_ques.manid=r.manid')
            ->join('(select qid ,count(1) num from think_ques2 where type=1  group by qid) A', 'A.qid = think_ques
            .qid', 'LEFT')
            ->where($map)
            ->order('think_ques.dt desc')
            ->page($Nowpage, 10)
            ->select();

        return json(['pages' => $page, 'data' => $ques]);

    }

//        获取专家列表
    public function list_zj()
    {
        $condition = [];
        $condition['status'] = 1;
        $condition['status'] = 1;

        $date = Db::name('member')
            ->alias('member')
            ->field('member.*,case
when A.num is null then 0
ELSE A.num
 end num')
            ->where($condition)
            ->join('(select manid ,count(1) num from think_ques group by manid) A', 'A.manid = member
            .manid', 'LEFT')
            ->order('dt desc')
            ->select();


        $this->assign('datalist', $date);
        return $this->fetch('/list');
    }

//    专家主页
    public function professor()
    {
        $view = new View();

        $id = input('param.id');
        $date = Db::name('member')
            ->find($id);

        //回答次数
        $map['manid'] = $date['manid'];
        $count = Db::name('ques')->where($map)->count();


        $this->assign('info', $date);
        $this->assign('num', $count);

        return $this->fetch('/professor');


    }

//问题详情
    public function wtshow()
    {

//        判断从个人中心点击过来的，显示回复作用
        if (input('?users')) {
//管理员
            $this->assign('users', input('users'));
        } else {
//
            $this->assign('users', 0);

        }

        $ques2 = new Question2Model();

        $id = input('param.id');//专家id
        $pid = input('param.qid');//问题id

//        存入问题id，待提交问题时使用
        session('pid', $pid);

        $date = Db::name('member')
            ->find($id);

        $map['qid'] = $pid;
//        获取全部问题
        $rq = $ques2->getsomeQuestion($map);


        //回答次数
        $mapp['manid'] = $date['manid'];
        $count = Db::name('ques')->where($mapp)->count();
        $this->assign('num', $count);


        $this->assign('info', $date);
        $this->assign('datalist', $rq);

        return $this->fetch();

    }

    /**
     * [add_rule 添加问题]
     * @return [type] [description]
     * @author [zzy] [1376161485@qq.com]
     */
    public function answer()
    {

        if (request()->isPost()) {
            $this->is_login();
            $user = session('home');
            $input = input('post.');
            $input['manid'] = $input['id'];
            $input['userid'] = $user['manid'];
            $input['status'] = 0;

            $member = new QuestionModel();
            $flag = $member->addQuestion($input);

            //添加到问题表2中
            $Question2 = new Question2Model();
            $input2['type'] = 1;
            $input2['qid'] = $flag['qid'];
            $input2['note'] = $input['note'];

            $Question2->addQuestion($input2);


            if ($flag['code'] == 1) {
//                echo "<script>window.history.go(-1);</script>"; exit;
                $this->success("提问成功！", 'index');
            }
        }
        $id = input('param.id');
        $date = Db::name('member')
            ->find($id);

        $this->assign('info', $date);

        return $this->fetch();


    }

    /**
     * [add_rule 追答问题]
     * @return [type] [description]
     * @author [zzy] [1376161485@qq.com]
     */
    public function huifu()
    {


        if (request()->isPost()) {

            $input = input('post.');
            if (session('?adminhuifu')) {
                //判断为管理员身份
                $input['type'] = 3;
                $input2['isread'] = 1;
                $url = '/admin/question/index';
            } else {
                if (session('home.status') == 1) {
                    //判断为专家身份
                    $input['type'] = 2;
                    $input2['isread'] = 1;

                } else {
                    //判断为用户身份
                    $input['type'] = 1;

                }
                $url = 'index';

            }
            $input['qid'] = session('pid');
            //添加到问题对话列表1中
            $input2['id'] = $input['qid'];
            $input2['lastreply'] = $input['note'];
            $Question = new QuestionModel();
            $Question->editQuestion($input2);

            //添加回复到表2中

            $member = new Question2Model();
            $flag = $member->addQuestion($input);


            if ($flag['code'] == 1) {
//                $this->success("回复成功！", 'index');
                $this->success("回复成功！", $url);
            }
        }

//获取专家id
        $id = input('param.id');
//dump($_SESSION);die;
        $user = $date = Db::name('member')->find($id);
//        如果是管理员回复，获取管理员信息
        if (input('?admin')) {
            $admin = $date = Db::name('member')->find(65);
            session('home', $admin);
            session('adminhuifu', 1);
        } else {
//            若不是管理员需要判断登录
            $this->is_login();
        }
//        判断是否是专家admin，
        if ($_SESSION['think']['home']['status'] == 1) {

            //   根据问题id     获取用户信息id
            $id = session('pid');
            $ques = Db::name('ques')->find($id);
            $user = Db::name('member')->find($ques['userid']);

        }

        $this->assign('user', $user);

//输出专家信息必要的！
        $this->assign('info', $date);

        return $this->fetch();


    }

    /**
     * [newsdel 删除单个回复]
     * @return [type] [description]
     * @author [zzy] [1376161485@qq.com]
     */
    public function newsdel()
    {
        $id = input('id');
        $model = new Question2Model();
        $result = $model->delQuestion($id);
        $this->success($result['msg']);

    }


    /**
     * [add_rule 我的提问]
     * @return [type] [description]
     * @author [zzy] [1376161485@qq.com]
     */
    public function myquestion()
    {
        $this->is_login();
        $id = session('home');

//        做判断看用户问题和看专家问题
        if ($id['status'] == 1) {
            $condition['think_ques.manid'] = $id['manid'];

        } else {
//            所有用户的问题
            $condition['think_ques.userid'] = $id['manid'];
        }


        $datalist = Db::name('ques')
            ->field('think_ques.*,r.username as username,r.head as head,
            CASE
WHEN think_ques.isread=1 THEN
	"已回复"
ELSE
	"待回复"
END isread')
//            ->where('manid',4)
            ->where($condition)
            ->join('think_member r', 'think_ques.manid=r.manid')
            ->order('think_ques.dt desc')
            ->select();


        $this->assign('datalist', $datalist);

        return $this->fetch();


    }


    /**
     * [add_rule 提交图片]
     * @return [type] [description]
     * @author [zzy] [1042797627@qq.com]
     */
    public function up()
    {

        $file = new namespace\Upload();

        $getSaveName = $file->uploadImage('images');

        $getSaveName = substr($getSaveName, 1, strlen($getSaveName) - 2);//去掉两端双引号

        if (!empty($getSaveName)) {
            return json(
                ['code' => 1, 'msg' => '上传成功', 'data' => $getSaveName]
            );

        } else {
            return json(array(
                'code' => 0
            , 'msg' => $getSaveName

            ));
        }


    }


    /**
     * [indexPage 个人中心]
     * @return [type] [description]
     * @author [zzy] [1376161485@qq.com]
     */
    public function personal()
    {
        $this->is_login();//判断是否登陆
        $member = new MemberModel();
        $view = new View();
//        session_start();
        $info = session('home');
//        $info = $member->getOneMember($user['manid']);

        if ($info['status'] == 1) {
//           专家个人信息
            $view->messege = '专家信息';

        } else {
//            用户个人信息
            $view->messege = '个人信息';

        }


        $this->assign('user', $info);

        return $this->fetch('/gerenzhongxin');


    }

    /**
     * [indexPage 个人信息修改]
     * @return [type] [description]
     * @author [zzy] [1376161485@qq.com]
     */
    public function gerenxiugai()
    {
        $this->is_login();//判断是否登陆
        $member = new MemberModel();
//        $info = $member->getOneMember(4);
        $user = session('home');
        $info = $member->getOneMember($user['manid']);
        //更新个人数据
        session('home', $info);

        $this->assign('user', $info);

        if (request()->isPost()) {
            $condition = input('post.');

            $rq = $member->editMember($condition);
//            $rq = $member->editMemberone($condition);
            if ($rq['code'] == 1) {
                $this->success($rq['msg'], 'index/personal');

            } else {
                $this->error($rq['msg'], 'index/personal');
            }
        }
        return $this->fetch('/gerenxiugai');
    }

    /**
     * [indexPage 专家申请]
     * @return [type] [description]
     * @author [zzy] [1376161485@qq.com]
     */
    public function shenqing()
    {
        $this->is_login();//判断是否登陆
        $member = new MemberModel();
        $view = new View();
        $info = session('home');

//        $info = $member->getOneMember(4);
        $this->assign('user', $info);

        if (request()->isPost()) {
            $condition = input('post.');

            $condition['privilege'] = 1;
            $condition['status'] = 0;

            $rq = $member->editMember($condition);
            if ($rq['code'] == 1) {
                $this->success('申请成功，待管理员审核', 'index/personal');

            } else {
                $this->error($rq['msg'], 'index/personal');
            }

        }

        return $this->fetch();


    }


}
