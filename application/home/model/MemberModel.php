<?php
/**
 * Created by PhpStorm.
 * User: CNDNS-JK
 * Date: 2018/1/19 0019
 * Time: 16:36
 */

namespace app\home\model;

use think\Db;
use think\Model;

/**
 * 会员模型
 * Class MemberModel
 */
class MemberModel extends Model
{
    protected $name = 'member';

    protected $autoWriteTimestamp = 'datetime';

//    protected $autoWriteTimestamp = true;   // 开启自动写入时间戳
    protected $createTime = 'dt';
    protected $updateTime = 'update_at';
    protected $pk = 'manid';

    /**
     * 获取性别值
     * @param $value
     * @return mixed
     */

    public function getSexAttr($value)
    {
        $status = [0 => '男', 1 => '女'];
        return $status[$value];
    }

    /**
     * 根据搜索条件获取用户列表信息
     */
    public function getMemberByWhere($map, $Nowpage, $limits)
    {
        return $this->where($map)->order('manid desc')
            ->page($Nowpage, $limits)
            ->select();
    }

    /**
     * 根据搜索条件获取所有的用户数量
     * @param $where
     */
    public function getAllCountByWhere($map)
    {
        return $this->where($map)->count();
    }

    /**
     * 根据id获取会员信息
     * @param $id
     * @return array|false|\PDOStatement|string|Model
     */
    public function getOneMember($id)
    {
        return $this->where('manid', $id)->find();
    }

    /**
     * 根据条件获取单条会员信息
     * @param $id
     * @return array|false|\PDOStatement|string|Model
     */
    public function getOnesMember($map)
    {
        return $this->where($map)->find();
    }


    /**
     * 插入信息
     */
    public function insertMember($param)
    {
        try {
            $result = $this->validate('MemberValidate')->allowField(true)->save($param);
            if (false === $result) {
                return ['code' => 0, 'data' => '', 'msg' => $this->getError()];
            } else {
                return ['code' => 1, 'data' => '', 'msg' => '添加成功'];
            }
        } catch (\Exception $e) {
            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

//    判断url是否有效
    function file_exists($url)
    {
        $curl = curl_init($url);
// 不取回数据
        curl_setopt($curl, CURLOPT_NOBODY, true);
// 发送请求
        $result = curl_exec($curl);
        $found = false;
// 如果请求没有发送失败
        if ($result !== false) {
// 再检查http响应码是否为200

        }else{
            return $found;
        }
    }

    /**
     * 编辑信息
     * @param $param
     */
    public function editMember($param)
    {
        try {
//            allowField 过滤post数组中的非数据表字段数据
//            $result = $this->validate('MemberValidate')->allowField(true)->save($param, ['manid' => $param['id']]);
            $result = $this->validate([
                'email' => 'email',
            ], ['email' => '邮箱格式错误',])->allowField(true)->save($param, ['manid' => $param['id']]);


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
     * 删除会员信息
     * @param $id
     * @return array
     */
    public function delMember($id)
    {
        try {
            $map['del'] = 1;
            $this->save($map, ['manid' => $id]);
            return ['code' => 1, 'data' => '', 'msg' => '删除成功'];
        } catch (\Exception $e) {
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    /**
     * 删除选中的记录
     * @param $ids
     * @return \think\response\Json
     */
    public function delSelecteds($ids)
    {
        try {
            $success = 0;//成功的记录数
            $error = 0;//失败的记录数
            if (strlen($ids) > 0) {
                $arrIds = explode(',', $ids);//将字符串转为数组
                $count = count($arrIds);//数组长度
                if ($count > 0) {
                    for ($i = 0; $i < $count; $i++) {
                        $flag = Db::name('member')->where('manid', $arrIds[$i])->delete();
                        if ($flag > 0) {
                            $success++;
                        } else {
                            $error++;
                            continue;
                        }
                    }
                    if ($success == $count) {
                        return json(['code' => 1, 'data' => '', 'msg' => $success . '条记录删除成功']);
                    } else {
                        return json(['code' => 0, 'data' => '', 'msg' => $error . '条记录删除成功']);
                    }
                }
            }
        } catch (\Exception $e) {
            return json(['code' => $e->getCode(), 'data' => '', 'msg' => $e->getMessage()]);
        }
    }

}