<?php
/**
 * Created by PhpStorm.
 * User: CNDNS-JK
 * Date: 2018/1/25 0025
 * Time: 10:15
 */

namespace app\admin\controller;

use app\admin\model\BedManagementModel;
use think\Db;
/**
 * 床位管理的类
 * Class BedManagement
 * @package app\admin\controller
 */
class BedManagement extends Base
{

    /**
     * 列表页
     * @return mixed
     */
    public function index()
    {
        return $this->fetch();
    }

    /**
     * 获取床位列表
     * @return \think\response\Json
     */
    public function getBedList()
    {
        $title = input('title');//床位名称
        $map = [];//查询条件
        if ('' != $title) {
            $map['title'] = ['like', "%" . $title . "%"];
        }

        $bed_model = new BedManagementModel();//床位的模型
        $count = $bed_model->getAllCountByWhere($map);//计算总页面
        $lists = $bed_model->getBedByWhere($map);
        return json(['code' => 0, 'msg' => '', 'count' => $count, 'data' => $lists]);
    }

    /**
     * 添加床位
     * @return mixed|\think\response\Json
     */
    public function add()
    {
        if (request()->isAjax()) {
            $param = input('post.');
            $bed_model = new BedManagementModel();//床位的模型
            $flag = $bed_model->insertBed($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        return $this->fetch();
    }

    /**
     * 编辑床位
     * @return mixed|\think\response\Json
     */
    public function edit()
    {
        $bed_model = new BedManagementModel();//床位的模型
        if (request()->isAjax()) {
            $param = input('post.');
            $flag = $bed_model->editBed($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }

        $id = input('param.id');
        $this->assign([
            'bed' => $bed_model->getOneBed($id),
        ]);
        return $this->fetch();
    }

    /**
     * 删除床位
     * @return \think\response\Json
     */
    public function del(){
        $id = input('param.id');
        $bed_model = new BedManagementModel();//床位的模型
        $flag = $bed_model->delBed($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    /**
     * 床位的状态
     * @return \think\response\Json
     */
    public function bed_status()
    {
        $id = input('param.id');
        $status = Db::name('bed_management')->where('id', $id)->value('status');//判断当前状态情况
        if ($status == 1) {
            $flag = Db::name('bed_management')->where('id', $id)->setField(['status' => 0]);
            return json(['code' => 1, 'data' => $flag['data'], 'msg' => '已停用']);
        } else {
            $flag = Db::name('bed_management')->where('id', $id)->setField(['status' => 1]);
            return json(['code' => 0, 'data' => $flag['data'], 'msg' => '已启用']);
        }

    }

    /**
     * 删除选中的记录
     * @return \think\response\Json
     */
    public function delSelecteds()
    {
        $ids = input('param.ids');
        return deletes('bed_management', $ids);//物理删除
    }
}