<?php
/**
 * Created by PhpStorm.
 * User: sone
 * Date: 2016/7/6
 * Time: 18:53
 */

namespace Home\Controller;


use Think\Controller;

class ApiController extends Controller{

    public function regSms($tel){
        //短信

            //引入TopSdk.php
            vendor('Alidayu.TopSdk');
            //dump(get_included_files());
            $c = new \TopClient;
            $c->appkey = '23401266';
            $c->secretKey = 'c3880f690b64dafffaae83aeaaaea936';
            $req = new \AlibabaAliqinFcSmsNumSendRequest;
            $req->setSmsType("normal");
            $req->setSmsFreeSignName("加冰");
            //调用随机数方法,生成一个随机的6数字
            $cond = \Org\Util\String::randNumber(100000,999999);
            //保存到session中
            session('reg_tel_code',$cond);
            $data = [
                'product'=>'芽儿哟',
                'cond'=>$cond
            ];
            $d1 =json_encode($data);
            //转换为json数据
            $req->setSmsParam("$d1");
            //发送的手机号
            $req->setRecNum($tel);
            $req->setSmsTemplateCode("SMS_11560789");
            $resp =  $c->execute($req);
            dump($resp);
        }
}