<?php


namespace Common\Behavior;

class CheckPermissionBehavior extends \Think\Behavior{
    public function run(&$params) {
        //获取并验证权限
        $url = MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME;
        
        //获取忽略列表
        $ignore_setting = C('ACCESS_IGNORE');
        
        //配置所有用户都可以访问的页面
        $ignore = $ignore_setting['IGNORE'];
        if(in_array($url, $ignore)){
            return true;
        }
        
        //获取用户信息
        $userinfo = login();
        //如果没有登陆,就自动登陆
        if(!$userinfo){
            $userinfo = D('Admin')->autoLogin();
        }
        if(isset($userinfo['username']) && $userinfo['username'] == 'admin'){
            return true;
        }
        
        //获取权限列表
        $pathes = permission_pathes();
        
        //登陆用户可见页面
        $user_ignore = $ignore_setting['USER_IGNORE'];
        
        //允许访问的页面有,角色处获取的权限和忽略列表
        $urls = $pathes;
        if($userinfo){
            //登陆用户可见页面还要额外加上登陆后的忽略列表
            $urls = array_merge($urls,$user_ignore);
        }
        
        if(!in_array($url, $urls)){
            header('Content-Type: text/html;charset=utf-8');
            redirect(U('Admin/Admin/login'), 3, '无权访问');
        }
    }

}


/**
 * 1. 如果是忽略列表的,不执行任何逻辑,直接返回
 * 2. 如果是已登录用户,将登陆用户忽略列表和用户角色权限相关的路径,进行合并,如果当前请求在列表中,返回,不在重置到登陆
 */