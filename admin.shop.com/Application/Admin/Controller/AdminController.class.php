<?php
/**
 * Created by PhpStorm.
 * User: sone
 * Date: 2016/7/2
 * Time: 11:13
 */

namespace Admin\Controller;


use Think\Controller;

class AdminController extends Controller{

    /**
     * @var \Admin\Model\AdminModel
     */
    private $_model = null;

    protected function _initialize() {
        $this->_model = D('Admin');
    }

    public function index(){
        //接受查询的条件,进行条件拼接
        $name = I('get.name');
        $cond = [];
        if($name){
            $cond['username']=['like','%'.$name.'%'];
        }
        //获取搜索和分页后的数据
        $this->assign($this->_model->getPageResult($cond));
        $this->display();
    }

    public function add(){
          if(IS_POST){
              if($this->_model->create()===false){
                  $this->error(get_error($this->_model));
              }
              if($this->_model->addAdmin()===false){
                  $this->error(get_error($this->_model));
              }
                $this->success('添加成功',U('index'));
          }else{
              $this->before_view();
              $this->display();
          }
    }

    public function edit($id){

        if(IS_POST){
            if($this->_model->create()===false){
                $this->error(get_error($this->_model));
            }
            if($this->_model->saveAdmin($id)===false){
                $this->error(get_error($this->_model));
            }
            $this->success('修改成功',U('index'));
        }else{
            //获取管理员信息,包括角色
            $row = $this->_model->getAdminInfo($id);
            $this->assign('row',$row);
            //基本信息
            $this->before_view();
            $this->display('add');
        }

    }
    /**
     * 删除管理员,并且删除管理员和角色关联关系.
     * @param type $id
     */
    public function remove($id) {
        if($this->_model->deleteAdmin($id)===false){
            $this->error(get_error($this->_model));
        }
        $this->success('删除成功', U('index'));

    }
    //获取数据
    private function  before_view(){
        //获取所有的商品的分类,使用ztree展示,所以转换成json
        $Role_model = D('Role');
        $roles = $Role_model->getList();
        $this->assign('roles',json_encode($roles));
    }
 }