<?php
/**
 * Created by PhpStorm.
 * User: CNDNS-JK
 * Date: 2018/1/25 0025
 * Time: 17:50
 */

namespace app\admin\controller;

use think\Db;
use app\admin\model\ServiceModel;
use app\admin\model\ServiceTypeModel;
/**
 * 设施服务
 * Class Service
 * @package app\admin\controller
 */
class Service extends Base
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
     * 获取服务列表
     * @return \think\response\Json
     */
    public function getServiceList()
    {
        $title = input('title');//服务名称
        $type = input('type');//服务类型
        $map = [];//查询条件
        if ('' != $title) {
            $map['think_service.title'] = ['like', "%" . $title . "%"];
        }
        if ('' != $type) {
            $map['think_service_type.title'] = ['like', "%" . $type . "%"];
        }

        $service_model = new ServiceModel();//服务的模型
        $count = $service_model->getAllCountByWhere($map);//计算总页面
        $lists = $service_model->getServiceByWhere($map);
        return json(['code' => 0, 'msg' => '', 'count' => $count, 'data' => $lists]);
    }

    /**
     * 添加服务
     * @return mixed|\think\response\Json
     */
    public function add()
    {
        if (request()->isAjax()) {
            $param = input('post.');
            $service_model = new ServiceModel();//服务的模型
            $flag = $service_model->insertService($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $service_type_model = new ServiceTypeModel();//服务类型的模型
        $types = $service_type_model->getOnAbleType();//获取在用的服务类型
        $this->assign('types',$types);
        return $this->fetch();
    }

    /**
     * 编辑服务
     * @return mixed|\think\response\Json
     */
    public function edit()
    {
        $service_model = new ServiceModel();//服务的模型
        if (request()->isAjax()) {
            $param = input('post.');
            $flag = $service_model->editService($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }

        $id = input('param.id');
        $service_type_model = new ServiceTypeModel();//服务类型的模型
        $types = $service_type_model->getOnAbleType();//获取在用的服务类型
        $this->assign('types',$types);
        $this->assign([
            'service' => $service_model->getOneService($id),
        ]);
        return $this->fetch();
    }

    /**
     * 删除服务
     * @return \think\response\Json
     */
    public function del(){
        $id = input('param.id');
        $service_model = new ServiceModel();//服务的模型
        $flag = $service_model->delService($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    /**
     * 服务的状态
     * @return \think\response\Json
     */
    public function service_status()
    {
        $id = input('param.id');
        $status = Db::name('service')->where('id', $id)->value('status');//判断当前状态情况
        if ($status == 1) {
            $flag = Db::name('service')->where('id', $id)->setField(['status' => 0]);
            return json(['code' => 1, 'data' => $flag['data'], 'msg' => '已停用']);
        } else {
            $flag = Db::name('service')->where('id', $id)->setField(['status' => 1]);
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
        return deletes('service', $ids);//物理删除
    }

}