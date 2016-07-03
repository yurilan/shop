<?php
/**
 * Created by PhpStorm.
 * User: sone
 * Date: 2016/7/2
 * Time: 0:59
 */

namespace Admin\Controller;


use Think\Controller;

class RoleController extends Controller{
    /**
     * @var \Admin\Model\RoleModel
     */
    private $_model = null;

    protected function _initialize() {
        $this->_model = D('Role');
    }


    public function index() {
        //搜索条件
        $name = I('get.name');
        $cond = [];
        if ($name) {
            $cond['name'] = [
                'like', '%' . $name . '%'
            ];
        }
        $this->assign($this->_model->getPageResult($cond));
        $this->display();
    }
    public function add() {
        if (IS_POST) {
            if ($this->_model->create() === false) {
                $this->error(get_error($this->_model));
            }
            if ($this->_model->addRole() === false) {
                $this->error(get_error($this->_model));
            }
            $this->success('添加成功', U('index'));
        } else {
            $this->before_view();
            $this->display();
        }
    }

    public function edit($id){
        if(IS_POST){
            if ($this->_model->create() === false) {
                $this->error(get_error($this->_model));
            }
            if ($this->_model->saveRole() === false) {
                $this->error(get_error($this->_model));
            }
            $this->success('修改成功', U('index'));
        }else{

            //获取角色的基本信息
            $row = $this->_model->getPermissionInfo($id);
            $this->assign('row',$row);
            //获取角色所有的的权限
            $this->before_view();
            $this->display('add');
        }

    }

    public function remove($id) {
        if($this->_model->deleteRole($id) === false){
            $this->error(get_error($this->_model));
        }
        $this->success('删除成功', U('index'));
    }

    private function before_view() {
        //获取所有权限
        $permission_model = D('Permission');
        $permissions      = $permission_model->getList();
        //传递
        $this->assign('permissions', json_encode($permissions));
    }
}