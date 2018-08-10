<?php

namespace app\admin\model;
use think\Model;
use think\Db;

class QuestionModel extends Model
{
    protected $name = 'ques';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'datetime';
//    protected $autoWriteTimestamp = true;
    protected $createTime = 'dt';
    protected $updateTime = 'update_at';
    protected $pk = 'qid';

    /**
     * [insertQuestion 前台添加问题]
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
                return ['code' => 1, 'data' => '', 'msg' => '提问成功','qid'=> $this->qid];
            }
        }catch( PDOException $e){
            return ['code' => -2, 'data' => '', 'msg' => $e->getMessage()];
        }
    }



    /**
     * [getAllQuestion 获取全部菜单]
     * @author [leo] [3093230151@qq.com]
     */
    public function getAllQuestion($map, $Nowpage, $limits)
    {
        return $this->field('think_ques.*,think_member.username as username')
            ->order('qid desc')
            ->where($map)
            ->join('think_member','think_member.manid=think_ques.manid','left')
            ->page($Nowpage, $limits)
            ->select();
    }

    /**
     * 根据搜索条件获取所有服务的数量
     * @param $where
     */
    public function getAllCountByWhere($map='')
    {
//        return $this->field('think_service.*,think_service_type.id tid,think_service_type.title as type')
//            ->join('think_service_type','think_service_type.id=think_service.tid')
        return $this->order('qid asc')
//            ->page($Nowpage, $limits)
        ->where($map)->count();
    }


    /**
     * [insertQuestion 添加菜单]
     * @author [leo] [3093230151@qq.com]
     */
    public function insertQuestion($param)
    {
        try{
            $result = $this
                ->allowField(true)
                ->save($param);
            if(false === $result){
                writelog(session('uid'),session('username'),'用户【'.session('username').'】添加菜单失败',2);
                return ['code' => -1, 'data' => '', 'msg' => $this->getError()];
            }else{
                writelog(session('uid'),session('username'),'用户【'.session('username').'】添加菜单成功',1);
                return ['code' => 1, 'data' => '', 'msg' => '添加菜单成功'];
            }
        }catch( PDOException $e){
            return ['code' => -2, 'data' => '', 'msg' => $e->getMessage()];
        }
    }



    /**
     * [editQuestion 编辑菜单]
     * @author [leo] [3093230151@qq.com]
     */
    public function editQuestion($param)
    {
        try{
            $result =  $this
                ->allowField(true)
                ->save($param, ['qid' => $param['id']]);
            if(false === $result){
//                writelog(session('uid'),session('username'),'用户【'.session('username').'】编辑菜单失败',2);
                return ['code' => 0, 'data' => '', 'msg' => $this->getError()];
            }else{
//                writelog(session('uid'),session('username'),'用户【'.session('username').'】编辑菜单成功',1);
                return ['code' => 1, 'data' => '', 'msg' => '编辑菜单成功'];
            }
        }catch( PDOException $e){
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    /**
     * [getAllQuestion 获取条件问题互动]
     * @author [leo] [3093230151@qq.com]
     */
    public function getsomeQuestion($map)
    {
        return $this->field('think_ques.*,think_member.username as username')
            ->order('dt desc')
            ->where($map)
            ->join('think_member','think_member.manid=think_ques.manid')
            ->select();
    }


    /**
     * [getOneQuestion 根据菜单id获取一条信息]
     * @author [leo] [3093230151@qq.com]
     */
    public function getOneQuestion($id)
    {
        return $this->where('qid', $id)->find();
    }



    /**
     * [delQuestion 删除问题]
     * @author [leo] [3093230151@qq.com]
     */
    public function delQuestion($id)
    {
        try{
            $this->where('qid', $id)->delete();
            writelog(session('uid'),session('username'),'用户【'.session('username').'】删除问题成功',1);
            return ['code' => 1, 'data' => '', 'msg' => '删除问题成功'];
        }catch( PDOException $e){
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }


    /**
     * [delQuestion 编辑菜单]
     * @param $ids
     * @param $ids
     * @return \think\response\Json
     */
    public function editSelecteds($ids)
    {
        try {
            $success = 0;//成功的记录数
            $error = 0;//失败的记录数
            if (strlen($ids) > 0) {
                $arrIds = explode(',', $ids);//将字符串转为数组
                $count = count($arrIds);//数组长度
                if ($count > 0) {
                    for ($i = 0; $i < $count; $i++) {

                        $flag = Db::name('ques')->where('qid', $arrIds[$i])->setField('status', 1);
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