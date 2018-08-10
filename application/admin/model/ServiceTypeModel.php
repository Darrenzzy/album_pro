<?php
/**
 * Created by PhpStorm.
 * User: CNDNS-JK
 * Date: 2018/1/25 0025
 * Time: 11:32
 */

namespace app\admin\model;

use think\Model;
class ServiceTypeModel extends Model
{

    protected $name = 'service_type';

    protected $autoWriteTimestamp = true;   // 开启自动写入时间戳


    /**
     * 根据搜索条件获取服务类型信息
     */
    public function getServiceTypeByWhere($map)
    {
        return $this->where($map)->order('sort asc')->select();
    }

    /**
     * 根据搜索条件获取所有服务类型的数量
     * @param $where
     */
    public function getAllCountByWhere($map)
    {
        return $this->where($map)->count();
    }

    /**
     * 获取再用的服务类型
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getOnAbleType(){
        return $this->where('status',1)->order('sort asc')->select();
    }

    /**
     * 根据id获取服务类型的信息
     * @param $id
     * @return array|false|\PDOStatement|string|Model
     */
    public function getOneServiceType($id){
        return $this->where('id',$id)->find();
    }

    /**
     * 插入服务类型
     */
    public function insertServiceType($param)
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
    public function editServiceType($param)
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
     * 删除服务类型
     * @param $id
     * @return array
     */
    public function delServiceType($id)
    {
        try {
            $this->where('id', $id)->delete();
            return ['code' => 1, 'data' => '', 'msg' => '删除成功'];
        } catch (\Exception $e) {
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }
}