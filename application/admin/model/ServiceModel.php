<?php
/**
 * Created by PhpStorm.
 * User: CNDNS-JK
 * Date: 2018/1/25 0025
 * Time: 17:47
 */

namespace app\admin\model;

use think\Model;

/**
 * 设施服务的模型
 * Class Service
 * @package app\admin\model
 */
class ServiceModel extends Model
{

    protected $name = 'service';

    protected $autoWriteTimestamp = true;   // 开启自动写入时间戳

    /**
     * 根据搜索条件获取服务信息
     */
    public function getServiceByWhere($map)
    {
        return $this->field('think_service.*,think_service_type.id tid,think_service_type.title as type')
            ->join('think_service_type','think_service_type.id=think_service.tid')
            ->where($map)
            ->order('think_service.id desc')
            ->select();
    }

    /**
     * 根据搜索条件获取所有服务的数量
     * @param $where
     */
    public function getAllCountByWhere($map)
    {
        return $this->field('think_service.*,think_service_type.id tid,think_service_type.title as type')
            ->join('think_service_type','think_service_type.id=think_service.tid')
            ->where($map)->count();
    }


    /**
     * 根据id获取服务的信息
     * @param $id
     * @return array|false|\PDOStatement|string|Model
     */
    public function getOneService($id){
        return $this->field('think_service.*,think_service_type.id tid,think_service_type.title as type')
            ->join('think_service_type','think_service_type.id=think_service.tid')
            ->where('think_service.id',$id)->find();
    }

    /**
     * 插入服务
     */
    public function insertService($param)
    {
        try {
            $result = $this->allowField(true)->save($param);
            if (false === $result) {
                return ['code' => 0, 'data' => '', 'msg' => $this->getError()];
            } else {
                return ['code' => 1, 'data' => '', 'msg' => '添加成功'];
            }
        } catch (\Exception $e) {
            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    /**
     * 编辑服务类型
     * @param $param
     */
    public function editService($param)
    {
        try {
            $result = $this->allowField(true)->save($param, ['id' => $param['id']]);
            if (false === $result) {
                return ['code' => 0, 'data' => '', 'msg' => $this->getError()];
            } else {
                return ['code' => 1, 'data' => '', 'msg' => '编辑成功'];
            }
        } catch (\Exception $e) {
            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    /**
     * 删除服务
     * @param $id
     * @return array
     */
    public function delService($id)
    {
        try {
            $this->where('id', $id)->delete();
            return ['code' => 1, 'data' => '', 'msg' => '删除成功'];
        } catch (\Exception $e) {
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }
}