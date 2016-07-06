<?php
/**
 * Created by PhpStorm.
 * User: sone
 * Date: 2016/7/5
 * Time: 18:56
 */

namespace Home\Controller;


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