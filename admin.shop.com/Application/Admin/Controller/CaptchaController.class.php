<?php
/**
 * Created by PhpStorm.
 * User: sone
 * Date: 2016/7/4
 * Time: 15:37
 */

namespace Admin\Controller;


use Think\Controller;
use Think\Verify;

class CaptchaController extends Controller{
        public function captcha(){
        $setting = [
            'length'=>4,
        ];
            //调用验证码工具勒
            $verify = new Verify($setting);
            $verify->entry();
        }

}