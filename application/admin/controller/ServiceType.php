<?php
/**
 * Created by PhpStorm.
 * User: CNDNS-JK
 * Date: 2018/1/25 0025
 * Time: 11:43
 */

namespace app\admin\controller;

use think\Db;
use app\admin\model\ServiceTypeModel;

/**
 * 服务类型
 * Class ServiceType
 * @package app\admin\controller
 */
class ServiceType extends Base
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
     * 获取服务类型列表
     * @return \think\response\Json
     */
    public function getServiceTypeList()
    {
        $title = input('title');//服务类型名称
        $map = [];//查询条件
        if ('' != $title) {
            $map['title'] = ['like', "%" . $title . "%"];
        }

        $service_type_model = new ServiceTypeModel();//服务类型的模型
        $count = $service_type_model->getAllCountByWhere($map);//计算总页面
        $lists = $service_type_model->getServiceTypeByWhere($map);
        return json(['code' => 0, 'msg' => '', 'count' => $count, 'data' => $lists]);
    }

    /**
     * 添加服务类型
     * @return mixed|\think\response\Json
     */
    public function add()
    {
        if (request()->isAjax()) {
            $param = input('post.');
            $service_type_model = new ServiceTypeModel();//服务类型的模型
            $flag = $service_type_model->insertServiceType($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        return $this->fetch();
    }

    /**
     * 编辑服务类型
     * @return mixed|\think\response\Json
     */
    public function edit()
    {
        $service_type_model = new ServiceTypeModel();//服务类型的模型
        if (request()->isAjax()) {
            $param = input('post.');
            $flag = $service_type_model->editServiceType($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }

        $id = input('param.id');
        $this->assign([
            'service_type' => $service_type_model->getOneServiceType($id),
        ]);
        return $this->fetch();
    }


    /**
     * 删除服务类型
     * @return \think\response\Json
     */
    public function del(){
        $id = input('param.id');
        $service_type_model = new ServiceTypeModel();//服务类型的模型
        $flag = $service_type_model->delServiceType($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    /**
     * 服务类型的状态
     * @return \think\response\Json
     */
    public function service_type_status()
    {
        $id = input('param.id');
        $status = Db::name('service_type')->where('id', $id)->value('status');//判断当前状态情况
        if ($status == 1) {
            $flag = Db::name('service_type')->where('id', $id)->setField(['status' => 0]);
            return json(['code' => 1, 'data' => $flag['data'], 'msg' => '已停用']);
        } else {
            $flag = Db::name('service_type')->where('id', $id)->setField(['status' => 1]);
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
        return deletes('service_type', $ids);//物理删除
    }

}