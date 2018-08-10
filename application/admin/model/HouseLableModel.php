<?php
/**
 * Created by PhpStorm.
 * User: CNDNS-JK
 * Date: 2018/1/25 0025
 * Time: 11:06
 */

namespace app\admin\model;

use think\Model;
class HouseLableModel extends Model
{
    protected $name = 'house_lable';

    protected $autoWriteTimestamp = true;   // 开启自动写入时间戳


    /**
     * 根据搜索条件获取房型标签信息
     */
    public function getLableByWhere($map)
    {
        return $this->where($map)->order('id desc')->select();
    }

    /**
     * 根据搜索条件获取所有的房型标签数量
     * @param $where
     */
    public function getAllCountByWhere($map)
    {
        return $this->where($map)->count();
    }

    /**
     * 根据id获取房型标签的信息
     * @param $id
     * @return array|false|\PDOStatement|string|Model
     */
    public function getOneLable($id){
        return $this->where('id',$id)->find();
    }

    /**
     * 插入房型标签
     */
    public function insertLable($param)
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
     * 编辑房型标签
     * @param $param
     */
    public function editLable($param)
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
     * 删除房型标签信息
     * @param $id
     * @return array
     */
    public function delLable($id)
    {
        try {
            $this->where('id', $id)->delete();
            return ['code' => 1, 'data' => '', 'msg' => '删除成功'];
        } catch (\Exception $e) {
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

}