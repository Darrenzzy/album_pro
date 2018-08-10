<?php
/**
 * Created by PhpStorm.
 * User: CNDNS-JK
 * Date: 2018/1/25 0025
 * Time: 10:41
 */

namespace app\admin\model;

use think\Model;

/**
 * 宜居人数的模型
 * Class LivableNumberModel
 * @package app\admin\model
 */
class LivableNumberModel extends Model
{
    protected $name = 'livable_number ';

    protected $autoWriteTimestamp = true;   // 开启自动写入时间戳

    /**
     * 根据搜索条件获取宜居人数信息
     */
    public function getLivableNumberByWhere($map)
    {
        return $this->where($map)->order('id desc')->select();
    }

    /**
     * 根据搜索条件获取所有的宜居人数的数量
     * @param $where
     */
    public function getAllCountByWhere($map)
    {
        return $this->where($map)->count();
    }

    /**
     * 根据id获取宜居人数的信息
     * @param $id
     * @return array|false|\PDOStatement|string|Model
     */
    public function getOneLivableNumber($id){
        return $this->where('id',$id)->find();
    }

    /**
     * 插入宜居人数
     */
    public function insertLivableNumber($param)
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
     * 编辑宜居人数
     * @param $param
     */
    public function editLivableNumber($param)
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
     * 删除宜居人数
     * @param $id
     * @return array
     */
    public function delLivableNumber($id)
    {
        try {
            $this->where('id', $id)->delete();
            return ['code' => 1, 'data' => '', 'msg' => '删除成功'];
        } catch (\Exception $e) {
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

}