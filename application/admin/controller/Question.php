<?php
/**
 * Created by PhpStorm.
 * User: CNDNS-JK
 * Date: 2018/1/19 0019
 * Time: 17:11
 */

namespace app\admin\controller;

use app\admin\model\QuestionModel;
use think\Db;
use think\Session;

class Question extends Base
{

    public function index()
    {
        return $this->fetch();//先实例化页面，数据另外取出
    }


    /**
     * 获取全部信息
     * @author [leo]
     */
    public function getAllQuestion()
    {
        $map = [];
        $member = new QuestionModel();
        $count = $member->getAllCountByWhere();//全部数量
        $Nowpage = input('get.page') ? input('get.page'):1;
//        $limits = config('list_rows');// 获取总条数
        $limits = input('get.limit');// 获取总条数
        $data = $member->getAllQuestion($map, $Nowpage, $limits);//全部问题

        return json(['code' => 0, 'msg' => '','count'=>$count,  'data' => $data]);
    }

    /**
     * 添加问题
     * @author [leo]
     */

    public function add()
    {
        if (request()->isAjax()) {

            $param = input('post.');
            $param['password'] = md5(md5($param['password']) . config('auth_key'));
            $member = new QuestionModel();
            $flag = $member->insertQuestion($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        return $this->fetch();
    }
    /**
     * 服务的状态
     * @return \think\response\Json
     */
    public function status()
    {
        $id = input('param.id');
        $status = Db::name('ques')->where('qid', $id)->value('status');//判断当前状态情况
        if ($status == 1) {
            $flag = Db::name('ques')->where('qid', $id)->setField(['status' => 0]);
            return json(['code' => 1, 'data' => $flag['data'], 'msg' => '已停用']);
        } else {
            $flag = Db::name('ques')->where('qid', $id)->setField(['status' => 1]);
            return json(['code' => 0, 'data' => $flag['data'], 'msg' => '已启用']);
        }

    }


    /**
     * 编辑问题
     * @author [leo]
     */
    public function edit()
    {

        $service_model = new QuestionModel();//问题的模型
        if (request()->isAjax()) {
            $param = input('post.');
            $flag = $service_model->editQuestion($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }

        $id = input('param.id');
        $service_type_model = new QuestionTypeModel();//服务类型的模型
        $types = $service_type_model->getOnAbleType();//获取在用的服务类型
        $this->assign('types',$types);
        $this->assign([
            'service' => $service_model->getOneQuestion($id),
        ]);
        return $this->fetch();
    }

    /**
     * 删除问题信息
     * @return \think\response\Json
     */
    public function del()
    {
        $id = input('param.id');
        $member = new QuestionModel();
        $flag = $member->delQuestion($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }
 
    /**
     * 删除选中的记录
     * @return \think\response\Json
     */
    public function delSelecteds()
    {
        $ids = input('param.ids');
        return deletes('ques', $ids,'qid');//物理删除
    }

    /**
     * 修改选中的记录
     * @return \think\response\Json
     */
    public function editSelecteds()
    {
        $ids = input('param.ids');
        $member = new QuestionModel();
        $flag = $member->editSelecteds($ids);
        return $flag;//物理删除
    }


}