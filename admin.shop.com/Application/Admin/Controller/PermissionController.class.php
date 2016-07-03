<?php
/**
 * Created by PhpStorm.
 * User: sone
 * Date: 2016/7/1
 * Time: 18:38
 */

namespace Admin\Controller;


use Think\Controller;

class PermissionController extends Controller{
    /**
     * @var \Admin\Model\PermissionModel
     */
    private $_model =null;
    protected function _initialize(){
        $this->_model = D('Permission');
    }
    public function index(){
        //获取所有的权限列表
        $rows =$this->_model->getList();
        $this->assign('rows',$rows);
        $this->display();
    }
    public function add(){
        //添加权限
        if(IS_POST){
            //收集数据
            if($this->_model->create()===false){
                $this->error(get_error($this->_model));
            }
            //保存数据
            if ($this->_model->addPermission() === false) {
                $this->error(get_error($this->_model));
            }
            //跳转
            $this->success('添加成功', U('index'));
        }else{
            //准备好所有的权限列表
            $this->before_view();
            $this->display();
        }

    }


    public function edit($id){
        //编辑权限
        if(IS_POST) {
            //收集数据
            if ($this->_model->create() === false) {
                $this->error(get_error($this->_model));
            }
            //保存数据
            if ($this->_model->savePermission() === false) {
                $this->error(get_error($this->_model));
            }

            //跳转
            $this->success('修改成功', U('index'));
        }else{
//获取数据
            $row = $this->_model->find($id);
            //传递
            $this->assign('row',$row);
            //全部权限列表,json字符串,给ztree使用
            $this->before_view();
            $this->display('add');
        }
    }
    public function remove($id){
        //删除权限
        if($this->_model->deletePermission($id) === false){
            $this->error(get_error($this->_model));
        }
        //跳转
        $this->success('删除成功', U('index'));

    }
    //获取所有权限的方法
    private function before_view(){
        $permissions = $this->_model->getList();
        array_unshift($permissions,['id'=>0,'name'=>'顶级权限','parent_id'=>null]);
        //Ztree要json数据
        $this->assign('permissions',json_encode($permissions));
    }

}