<?php
/**
 * Created by PhpStorm.
 * User: CNDNS-JK
 * Date: 2018/1/22 0022
 * Time: 14:43
 */

namespace app\admin\model;

use think\Model;

/**
 * 房型的模型
 * Class HouseLayoutModel
 * @package app\admin\model
 */
class HouseLayoutModel extends Model
{

    protected $name = 'house_layout';

    protected $autoWriteTimestamp = true;   // 开启自动写入时间戳

    /**
     * 根据搜索条件获取房型信息
     */
    public function getLayoutByWhere($map)
    {
        return $this->where($map)->order('id desc')->select();
    }

    /**
     * 根据搜索条件获取所有的房型数量
     * @param $where
     */
    public function getAllCountByWhere($map)
    {
        return $this->where($map)->count();
    }

    /**
     * 根据id获取房型的信息
     * @param $id
     * @return array|false|\PDOStatement|string|Model
     */
    public function getOneLayout($id){
        return $this->where('id',$id)->find();
    }

    /**
     * 插入信息
     */
    public function insertLayout($param)
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
     * 编辑信息
     * @param $param
     */
    public function editLayout($param)
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
     * 删除房型信息
     * @param $id
     * @return array
     */
    public function delLayout($id)
    {
        try {
            $this->where('id', $id)->delete();
            return ['code' => 1, 'data' => '', 'msg' => '删除成功'];
        } catch (\Exception $e) {
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }
}