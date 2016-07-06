<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        $this->display();
    }
    public function top(){
        //根据session来获取需要的值传到top
        $userinfo = login();
        $this->assign('userinfo',$userinfo);
        $this->display();
    }
    public function menu(){
        //获取所有可见菜单列表
        $menu_model = D('Menu');
        $menus = $menu_model->getMenuList();
        $this->assign('menus', $menus);
        $this->display();
    }
    public function main(){
        $this->display();
    }

}