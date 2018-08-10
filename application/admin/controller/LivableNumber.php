<?php
/**
 * Created by PhpStorm.
 * User: CNDNS-JK
 * Date: 2018/1/25 0025
 * Time: 10:45
 */

namespace app\admin\controller;
use think\Db;
use app\admin\model\LivableNumberModel;
/**
 * 宜居人数
 * Class LivableNumber
 * @package app\admin\controller
 */
class LivableNumber extends Base
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
     * 获取宜居人数列表
     * @return \think\response\Json
     */
    public function getLivableList()
    {
        $title = input('title');//宜居人数名称
        $map = [];//查询条件
        if ('' != $title) {
            $map['title'] = ['like', "%" . $title . "%"];
        }

        $livable_model = new LivableNumberModel();//宜居人数的模型
        $count = $livable_model->getAllCountByWhere($map);//计算总页面
        $lists = $livable_model->getLivableNumberByWhere($map);
        return json(['code' => 0, 'msg' => '', 'count' => $count, 'data' => $lists]);
    }

    /**
     * 添加宜居人数
     * @return mixed|\think\response\Json
     */
    public function add()
    {
        if (request()->isAjax()) {
            $param = input('post.');
            $livable_model = new LivableNumberModel();//宜居人数的模型
            $flag = $livable_model->insertLivableNumber($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        return $this->fetch();
    }

    /**
     * 编辑宜居人数
     * @return mixed|\think\response\Json
     */
    public function edit()
    {
        $livable_model = new LivableNumberModel();//宜居人数的模型
        if (request()->isAjax()) {
            $param = input('post.');
            $flag = $livable_model->editLivableNumber($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }

        $id = input('param.id');
        $this->assign([
            'livable' => $livable_model->getOneLivableNumber($id),
        ]);
        return $this->fetch();
    }

    /**
     * 删除户型
     * @return \think\response\Json
     */
    public function del(){
        $id = input('param.id');
        $livable_model = new LivableNumberModel();//宜居人数的模型
        $flag = $livable_model->delLivableNumber($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    /**
     * 宜居人数的状态
     * @return \think\response\Json
     */
    public function livable_status()
    {
        $id = input('param.id');
        $status = Db::name('livable_number')->where('id', $id)->value('status');//判断当前状态情况
        if ($status == 1) {
            $flag = Db::name('livable_number')->where('id', $id)->setField(['status' => 0]);
            return json(['code' => 1, 'data' => $flag['data'], 'msg' => '已停用']);
        } else {
            $flag = Db::name('livable_number')->where('id', $id)->setField(['status' => 1]);
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
        return deletes('livable_number', $ids);//物理删除
    }
}