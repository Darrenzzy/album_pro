<?php
/**
 * Created by PhpStorm.
 * User: CNDNS-JK
 * Date: 2018/1/25 0025
 * Time: 10:11
 */

namespace app\admin\model;

use think\Model;

/**
 * 床位管理
 * Class BedManagementModel
 * @package app\admin\model
 */
class BedManagementModel extends Model
{

    protected $name = 'bed_management';

    protected $autoWriteTimestamp = true;   // 开启自动写入时间戳

    /**
     * 根据搜索条件获取床位信息
     */
    public function getBedByWhere($map)
    {
        return $this->where($map)->order('id desc')->select();
    }

    /**
     * 根据搜索条件获取所有床位数量
     * @param $where
     */
    public function getAllCountByWhere($map)
    {
        return $this->where($map)->count();
    }

    /**
     * 根据id获取床位的信息
     * @param $id
     * @return array|false|\PDOStatement|string|Model
     */
    public function getOneBed($id){
        return $this->where('id',$id)->find();
    }

    /**
     * 插入床位
     */
    public function insertBed($param)
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
     * 编辑床位
     * @param $param
     */
    public function editBed($param)
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
     * 删除床位信息
     * @param $id
     * @return array
     */
    public function delBed($id)
    {
        try {
            $this->where('id', $id)->delete();
            return ['code' => 1, 'data' => '', 'msg' => '删除成功'];
        } catch (\Exception $e) {
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }
}