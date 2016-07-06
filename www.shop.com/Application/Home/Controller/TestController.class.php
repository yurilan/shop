<?php
/**
 * Created by PhpStorm.
 * User: sone
 * Date: 2016/7/6
 * Time: 17:12
 */

namespace Home\Controller;


use Think\Controller;

class TestController extends Controller{
    //短信
    public function sms(){
         //引入TopSdk.php
        vendor('Alidayu.TopSdk');
        //dump(get_included_files());
        $c = new \TopClient;
        $c->appkey = '23401266';
        $c->secretKey = 'c3880f690b64dafffaae83aeaaaea936';
        $req = new \AlibabaAliqinFcSmsNumSendRequest;
        $req->setSmsType("normal");
        $req->setSmsFreeSignName("加冰");
        $req->setSmsParam("{'product':'芽儿哟','cond':'4563'}");
        $req->setRecNum("18381746614");
        $req->setSmsTemplateCode("SMS_11560789");
        $resp = $c->execute($req);
    }

    public function sendEmail() {
        Vendor('PHPMailer.PHPMailerAutoload');

        $mail = new \PHPMailer;


        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host       = 'smtp.qq.com';  // Specify main and backup SMTP servers
        $mail->SMTPAuth   = true;                               // Enable SMTP authentication
        $mail->Username   = '464772384@qq.com';                 // SMTP username
        $mail->Password   = 'nnbgldxzseetbjcg';                           // SMTP password
        $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
        $mail->Port       = 465;                                    // TCP port to connect to

        $mail->setFrom('464772384@qq.com', 'jingxi');
        $mail->addAddress('ylcjb1@163.com', 'brother four');     // Add a recipient

        $mail->isHTML(true);                                  // Set email format to HTML

        $mail->Subject = '欢迎注册京西商场';
        $url = U('Member/Active',['email'=>'ylcjb1@163.com'],true,true);
        $mail->Body    = '欢迎您注册我们的网站,请点击<a href="'.$url.'">链接</a>激活账号.如果无法点击,请复制以下链接粘贴到浏览器窗口打开!<br />' . $url;
        $mail->CharSet = 'UTF-8';

        if (!$mail->send()) {
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            echo 'Message has been sent';
        }
    }
}