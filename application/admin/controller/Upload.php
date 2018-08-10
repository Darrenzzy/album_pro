<?php

namespace app\admin\controller;
use think\Controller;
use think\File;
use think\Request;

class Upload extends Base
{
	//图片上传
    public function upload(){
       $file = request()->file('file');
//       用自定义的命名规则作为文件名
       $info = $file->rule('zzy')->move(ROOT_PATH . 'public' . DS . 'uploads/images');
       if($info){
//            return $info;
            return $info->getSaveName();
        }else{
            return $info->getError();
//            return $info['error'];
        }
    }

    //会员头像上传
    public function uploadface(){
       $file = request()->file('file');
       $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads/face');
       if($info){
            echo $info->getSaveName();
        }else{
            echo $file->getError();
        }
    }

    /**
     *上传图片
     * @param $position
     * @return \think\response\Json
     */
    public function uploadImage($position=''){

        if($position==''){
            $position = 'images';
        }

        $file = request()->file('file');

        $info = $file->rule('zzy')->move(ROOT_PATH . 'public' . DS . 'uploads/'.$position);
//        if($info){
//            return json(['src' => $info->getSaveName()]);
//        }else{
//            return json(['error'=>$info->getError()]);
//        }

        if($info){
//            return $info->getSaveName();
            return json_encode($info->getSaveName());

        }else{
            return $info->getError();
        }
    }

}