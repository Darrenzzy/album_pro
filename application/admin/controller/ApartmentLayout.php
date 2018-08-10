<?php
/**
 * Created by PhpStorm.
 * User: CNDNS-JK
 * Date: 2018/1/25 0025
 * Time: 09:50
 */

namespace app\admin\controller;

use app\admin\model\ApartmentLayoutModel;
use think\Db;
class ApartmentLayout extends Base
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
     * 获取户型列表
     * @return \think\response\Json
     */
    public function getApartmentLayoutList()
    {
        $title = input('title');//户型名称
        $map = [];//查询条件
        if ('' != $title) {
            $map['title'] = ['like', "%" . $title . "%"];
        }

        $layout_model = new ApartmentLayoutModel();//户型的模型
        $count = $layout_model->getAllCountByWhere($map);//计算总页面
        $lists = $layout_model->getLayoutByWhere($map);
        return json(['code' => 0, 'msg' => '', 'count' => $count, 'data' => $lists]);
    }

    /**
     * 添加户型
     * @return mixed|\think\response\Json
     */
    public function add()
    {
        if (request()->isAjax()) {
            $param = input('post.');
            $layout_model = new ApartmentLayoutModel();//户型的模型
            $flag = $layout_model->insertLayout($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        return $this->fetch();
    }

    /**
     * 编辑户型
     * @return mixed|\think\response\Json
     */
    public function edit()
    {
        $layout_model = new ApartmentLayoutModel();//户型的模型
        if (request()->isAjax()) {
            $param = input('post.');
            $flag = $layout_model->editLayout($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }

        $id = input('param.id');
        $this->assign([
            'layout' => $layout_model->getOneLayout($id),
        ]);
        return $this->fetch();
    }

    /**
     * 删除户型
     * @return \think\response\Json
     */
    public function del(){
        $id = input('param.id');
        $layout_model = new ApartmentLayoutModel();//户型的模型
        $flag = $layout_model->delLayout($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    /**
     * 户型的状态
     * @return \think\response\Json
     */
    public function layout_status()
    {
        $id = input('param.id');
        $status = Db::name('apartment_layout')->where('id', $id)->value('status');//判断当前状态情况
        if ($status == 1) {
            $flag = Db::name('apartment_layout')->where('id', $id)->setField(['status' => 0]);
            return json(['code' => 1, 'data' => $flag['data'], 'msg' => '已停用']);
        } else {
            $flag = Db::name('apartment_layout')->where('id', $id)->setField(['status' => 1]);
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
        return deletes('apartment_layout', $ids);//物理删除
    }
}