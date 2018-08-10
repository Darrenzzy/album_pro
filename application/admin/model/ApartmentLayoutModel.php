<?php
/**
 * Created by PhpStorm.
 * User: CNDNS-JK
 * Date: 2018/1/25 0025
 * Time: 09:47
 */

namespace app\admin\model;

use think\Model;
use think\Db;

/**
 * 户型的模型
 * Class ApartmentLayoutModel
 * @package app\admin\model
 */
class ApartmentLayoutModel extends Model
{
    protected $name = 'apartment_layout ';

    protected $autoWriteTimestamp = true;   // 开启自动写入时间戳

    /**
     * 根据搜索条件获取户型信息
     */
    public function getLayoutByWhere($map)
    {
        return $this->where($map)->order('id desc')->select();
    }

    /**
     * 根据搜索条件获取所有户型数量
     * @param $where
     */
    public function getAllCountByWhere($map)
    {
        return $this->where($map)->count();
    }

    /**
     * 根据id获取户型的信息
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