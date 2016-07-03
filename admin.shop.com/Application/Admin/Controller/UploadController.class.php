<?php
/**
 * Created by PhpStorm.
 * User: sone
 * Date: 2016/6/27
 * Time: 19:02
 */

namespace Admin\Controller;
use Think\Controller;
use Think\Upload;

class UploadController extends Controller{

    public function uploadImg(){
        //创建upload对象
     // $options = C('UPLOAD_SETTING');
        $options =[
            'rootPath'=>ROOT_PATH,
            'savePath'=>'uploads/',

        ];
        $upload = new Upload($options);
        $file_info = $upload->uploadOne($_FILES['file_data']);
       // var_dump($file_info);exit;
        //获取上传文件的信息
        //上传成功返回文件的完整路径,失败返回错误信息
        if($file_info){
            if($upload->driver == 'Qiniu'){
                $file_url = $file_info['url'];
            } else{
                $file_url = BASE_URL . '/' . $file_info['savepath'] . $file_info['savename'];
            }
            $return = [
                'file_url' => $file_url,
                'msg'      => '上传成功',
                'status'   => 1,
            ];
        }else{
            $return = [
                'file_url' => '',
                'msg'      => $upload->getError(),
                'status'   => 0,
            ];
        }
        $this->ajaxReturn($return);
    }
}