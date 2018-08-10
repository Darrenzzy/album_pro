<?php

namespace app\admin\model;
use think\Model;

class Question2Model extends Model
{
    protected $name = 'ques2';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'datetime';
//    protected $autoWriteTimestamp = true;
    protected $createTime = 'dt';





    /**
     * [getAllQuestion 获取条件问题互动]
     * @author [leo] [3093230151@qq.com]
     */
    public function getsomeQuestion($map)
    {
        return $this->order('dt asc')
            ->where($map)
            ->select();
    }

    /**
     * [insertQuestion 前台回复问题]
     * @author [leo] [3093230151@qq.com]
     */
    public function addQuestion($param)
    {
        try{
            $result = $this
                ->allowField(true)
                ->save($param);
            if(false === $result){
                return ['code' => -1, 'data' => '', 'msg' => $this->getError()];
            }else{
                return ['code' => 1, 'data' => '', 'msg' => '回复成功'];
            }
        }catch( PDOException $e){
            return ['code' => -2, 'data' => '', 'msg' => $e->getMessage()];
        }
    }




    /**
     * [delQuestion 删除单条回复]
     * @author [leo] [3093230151@qq.com]
     */
    public function delQuestion($id)
    {
        try{
            $this->where('id', $id)->delete();
//            writelog(session('uid'),session('username'),'用户【'.session('username').'】删除单条回复成功',1);
            return ['code' => 1, 'data' => '', 'msg' => '删除单条回复成功'];
        }catch( PDOException $e){
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

}