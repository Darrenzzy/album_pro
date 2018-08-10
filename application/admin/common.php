<?php

use think\Db;

/**
 * 将字符解析成数组
 * @param $str
 */
function parseParams($str)
{
    $arrParams = [];
    parse_str(html_entity_decode(urldecode($str)), $arrParams);
    return $arrParams;
}


/**
 * 子孙树 用于菜单整理
 * @param $param
 * @param int $pid
 */
function subTree($param, $pid = 0)
{
    static $res = [];
    foreach ($param as $key => $vo) {

        if ($pid == $vo['pid']) {
            $res[] = $vo;
            subTree($param, $vo['id']);
        }
    }

    return $res;
}


/**
 * 记录日志
 * @param  [type] $uid         [用户id]
 * @param  [type] $username    [用户名]
 * @param  [type] $description [描述]
 * @param  [type] $status      [状态]
 * @return [type]              [description]
 */
function writelog($uid, $username, $description, $status)
{

    $data['admin_id'] = $uid;
    $data['admin_name'] = $username;
    $data['description'] = $description;
    $data['status'] = $status;
    $data['ip'] = request()->ip();
    $data['add_time'] = time();
    $log = Db::name('Log')->insert($data);

}


/**
 * 整理菜单树方法
 * @param $param
 * @return array
 */
function prepareMenu($param)
{
    $parent = []; //父类
    $child = [];  //子类

    foreach ($param as $key => $vo) {

        if ($vo['pid'] == 0) {
            $vo['href'] = '#';
            $parent[] = $vo;
        } else {
            $vo['href'] = url($vo['name']); //跳转地址
            $child[] = $vo;
        }
    }

    foreach ($parent as $key => $vo) {
        foreach ($child as $k => $v) {

            if ($v['pid'] == $vo['id']) {
                $parent[$key]['child'][] = $v;
            }
        }
    }
    unset($child);
    return $parent;
}


/**
 * 格式化字节大小
 * @param  number $size 字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 */
function format_bytes($size, $delimiter = '')
{
    $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
    for ($i = 0; $size >= 1024 && $i < 5; $i++) {
        $size /= 1024;
    }
    return $size . $delimiter . $units[$i];
}

/**
 * 删除选中的记录
 * @param $table [表名,不需要前缀]
 * @param $ids [id]
 * @param $manids [manid]字段主键
 * @return \think\response\Json
 */
function deletes($table, $ids,$manid='')
{
    try {
        $success = 0;//成功的记录数
        $error = 0;//失败的记录数
        if (strlen($ids) > 0) {
            $arrIds = explode(',', $ids);//将字符串转为数组
            $count = count($arrIds);//数组长度
            if ($count > 0) {
                for ($i = 0; $i < $count; $i++) {
                    if($manid!=''){
                        $flag = Db::name($table)->where($manid, $arrIds[$i])->delete();

                    }else{
                        $flag = Db::name($table)->where('id', $arrIds[$i])->delete();

                    }

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

