<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>用户注册</title>
    <link rel="stylesheet" href="http://www.shop.com/Public/css/base.css" type="text/css">
    <link rel="stylesheet" href="http://www.shop.com/Public/css/global.css" type="text/css">
    <link rel="stylesheet" href="http://www.shop.com/Public/css/header.css" type="text/css">
    <link rel="stylesheet" href="http://www.shop.com/Public/css/login.css" type="text/css">
    <link rel="stylesheet" href="http://www.shop.com/Public/css/footer.css" type="text/css">
    <style type="text/css">
        .error{
            padding-left: 1em;
            color:red;
        }
    </style>
</head>
<body>
<!-- 顶部导航 start -->
<div class="topnav">
    <div class="topnav_bd w990 bc">
        <div class="topnav_left">
        </div>
        <div class="topnav_right fr">
            <ul>
                <li>您好，欢迎来到京西！[<a href="login.html">登录</a>] [<a href="register.html">免费注册</a>] </li>
                <li class="line">|</li>
                <li>我的订单</li>
                <li class="line">|</li>
                <li>客户服务</li>
            </ul>
        </div>
    </div>
</div>
<!-- 顶部导航 end -->

<div http://www.shop.com/Public/css="clear:both;"></div>

<!-- 页面头部 start -->
<div class="header w990 bc mt15">
    <div class="logo w990">
        <h2 class="fl"><a href="index.html"><img src="http://www.shop.com/Public/images/logo.png" alt="京西商城"></a></h2>
    </div>
</div>
<!-- 页面头部 end -->

<!-- 登录主体部分start -->
<div class="login w990 bc mt10 regist">
    <div class="login_hd">
        <h2>用户注册</h2>
        <b></b>
    </div>
    <div class="login_bd">
        <div class="login_form fl">
            <form action="<?php echo U();?>" method="post" id='reg-form'>
                <ul>
                    <li>
                        <label for="">用户名：</label>
                        <input type="text" class="txt" name="username" /><span class="error"></span>
                        <p>3-20位字符，可由中文、字母、数字和下划线组成</p>
                    </li>
                    <li>
                        <label for="">密码：</label>
                        <input type="password" class="txt" name="password" id='password'/><span class="error"></span>
                        <p>6-16位字符，可使用字母、数字和符号的组合，不建议使用纯数字、纯字母、纯符号</p>
                    </li>
                    <li>
                        <label for="">确认密码：</label>
                        <input type="password" class="txt" name="repassword" /><span class="error"></span>
                        <p> <span>请再次输入密码</p>
                    </li>
                    <li>
                        <label for="">邮箱：</label>
                        <input type="text" class="txt" name="email" /><span class="error"></span>
                        <p>邮箱必须合法</p>
                    </li>
                    <li>
                        <label for="">手机号码：</label>
                        <input type="text" class="txt" value="" name="tel" id="tel" placeholder=""/><span class="error"></span>
                    </li>
                    <li>
                        <label for="">验证码：</label>
                        <input type="text" class="txt" value="" placeholder="请输入短信验证码" name="captcha" disabled="disabled" id="captcha"/> <input type="button" onclick="bindPhoneNum(this)" id="get_captcha" value="获取验证码" style="height: 25px;padding:3px 8px"/><span class="error"></span>

                    </li>
                    <li class="checkcode">
                        <label for="">验证码：</label>
                        <input type="text"  name="checkcode" />
                        <img src="<?php echo U('Captcha/captcha',['nocache'=>NOW_TIME]);?>" alt="" id='img_captcha'/>
                        <span>看不清？<a href="javascript:;" onclick="change_img_captcha()">换一张</a></span><span class="error"></span>
                    </li>

                    <li>
                        <label for="">&nbsp;</label>
                        <input type="checkbox" class="chb" checked="checked" name='agree' /> 我已阅读并同意《用户注册协议》<span class="error"></span>
                    </li>
                    <li>
                        <label for="">&nbsp;</label>
                        <input type="submit" value="" class="login_btn" />
                    </li>
                </ul>
            </form>


        </div>

        <div class="mobile fl">
            <h3>手机快速注册</h3>
            <p>中国大陆手机用户，编辑短信 “<strong>XX</strong>”发送到：</p>
            <p><strong>1069099988</strong></p>
        </div>

    </div>
</div>
<!-- 登录主体部分end -->

<div style="clear:both;"></div>
<!-- 底部版权 start -->
<div class="footer w1210 bc mt15">
    <p class="links">
        <a href="">关于我们</a> |
        <a href="">联系我们</a> |
        <a href="">人才招聘</a> |
        <a href="">商家入驻</a> |
        <a href="">千寻网</a> |
        <a href="">奢侈品网</a> |
        <a href="">广告服务</a> |
        <a href="">移动终端</a> |
        <a href="">友情链接</a> |
        <a href="">销售联盟</a> |
        <a href="">京西论坛</a>
    </p>
    <p class="copyright">
        © 2005-2013 京东网上商城 版权所有，并保留所有权利。  ICP备案证书号:京ICP证070359号
    </p>
    <p class="auth">
        <a href=""><img src="http://www.shop.com/Public/images/xin.png" alt="" /></a>
        <a href=""><img src="http://www.shop.com/Public/images/kexin.jpg" alt="" /></a>
        <a href=""><img src="http://www.shop.com/Public/images/police.jpg" alt="" /></a>
        <a href=""><img src="http://www.shop.com/Public/images/beian.gif" alt="" /></a>
    </p>
</div>
<!-- 底部版权 end -->
<script type="text/javascript" src="http://www.shop.com/Public/js/jquery-1.8.3.min.js"></script>
<script type="text/javascript" src="http://www.shop.com/Public/ext/jquery-validate/dist/jquery.validate.js"></script>
<script type="text/javascript">
    function bindPhoneNum() {
        //启用输入框
        $('#captcha').prop('disabled', false);
        var time = 60;
        //发送验证码的接口
        var url = '<?php echo U("Api/regSms");?>';
        var data = {'tel': $('#tel').val()};
        console.debug(data);
        $.getJSON(url,data,function(response) {
            console.debug(response);
        });

        var interval = setInterval(function () {
            time--;
            if (time <= 0) {
                clearInterval(interval);
                var html = '获取验证码';
                $('#get_captcha').prop('disabled', false);
            } else {
                var html = time + ' 秒后再次获取';
                $('#get_captcha').prop('disabled', true);

            }
            $('#get_captcha').val(html);
        }, 1000);
    }
    //图片验证码的刷新
    function change_img_captcha(){
        $('#img_captcha').attr('src','<?php echo U("Captcha/captcha");?>?nocache='+new Date().getDate());
    }

    //jquery-validate验证
    //rules表示规则,messages表示提示,errorPlacement表示错误发生时执行的逻辑,success成功时执行的逻辑,为空就可以了.
    var setting = {
        rules: {
            username: {
                required: true,
                rangelength: [3,20]
            },
            password: {
                required: true,
                rangelength: [6,16]
            },
            repassword: {
                required: true,
                rangelength: [6,16],
                equalTo: "#password"
            },
            email: {
                required: true,
                email: true
            },
            tel: {
                required: true,
                telephone:true,
            },
            checkcode: {
                required: true,
            },
            captcha: {
                required: true,
            },

            agree: "required"
        },
        messages: {
            username: {
                required: "用户名不能为空",
                rangelength: "用户名必须3-20个字符"
            },
            password: {
                required: "密码不能为空",
                rangelength: "密码长度必须为6-16位"
            },
            repassword: {
                required: "确认密码不能为空",
                rangelength: "确认密码长度必须为6-16位",
                equalTo: "两次密码不一致"
            },
            email: "邮箱地址不合法",
            tel: {
                required: '手机号码不能为空',
            },
            checkcode: {
                required: '图片验证码不能为空',
            },
            captcha: {
                required: '短信验证码不能为空',
            },
            agree: "必须同意许可协议"
        },
        //把错误信息放在指定的位置上去
        errorPlacement:function(error,node){
            //找到出错控件后的error节点,并把错误信息放进去
            var node = node[0];
            var msg_node = $(node).siblings('.error').get(0);
            $(msg_node).text($(error[0]).text());
        },
        success:function(){},
    }
    //对指定的表单使用jqery-validate验证
    $('#reg-form').validate(setting);
    //由于手机号码规则并不存在,所以我们自定义一个验证规则,第一个是规则名,第二个是验证函数,第三个是错误提示信息.
    $.validator.addMethod('telephone',check_telephone,'手机号码不合法');
    function check_telephone(tel,node){
        var reg = /^1[34578]\d{9}$/;
        return reg.test(tel);
    }

</script>
</body>
</html>