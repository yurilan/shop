<?php
/**
 * Created by PhpStorm.
 * User: sone
 * Date: 2016/7/4
 * Time: 0:29
 */

namespace Admin\Controller;


use Think\Controller;

class MenuController extends Controller{
    /**
     * @var \Admin\Model\MenuModel
     */
    private $_model = null;

    protected function _initialize() {
        $this->_model = D('Menu');
    }

    public function index() {
        //获取菜单列表
        $this->assign('rows', $this->_model->getList());
        $this->display();
    }

    /**
     * 添加菜单,自动计算左右节点和层级.
     */
    public function add() {
        if (IS_POST) {
            //收集数据
            if ($this->_model->create() === false) {
                $this->error(get_error($this->_model));
            }
            if ($this->_model->addMenu() === false) {
                $this->error(get_error($this->_model));
            }
            $this->success('添加成功', U('index'));
        } else {
            $this->before_view();
            $this->display();
        }
    }

    /**
     * 修改节点.
     * 不允许移动到后代节点下
     * @param type $id
     */
    public function edit($id) {
        if (IS_POST) {
            //收集数据
            if ($this->_model->create() === false) {
                $this->error(get_error($this->_model));
            }
            if ($this->_model->saveMenu() === false) {
                $this->error(get_error($this->_model));
            }
            $this->success('修改成功', U('index'));
        } else {
            //展示数据
            $row = $this->_model->getMenuInfo($id);
            $this->assign('row', $row);
            //获取所有的菜单
            $this->before_view();
            $this->display('add');
        }
    }
    /**
     * 物理删除节点,会同时删除后代节点
     * @param type $id
     */
    public function remove($id) {
        if ($this->_model->deleteMenu($id) === false) {
            $this->error(get_error($this->_model));
        } else {
            $this->success('删除成功', U('index'));
        }
    }

    /**
     * 获取菜单基本数据 和权限列表数据
     */
    private function before_view() {
        $menus = $this->_model->getList();
        array_unshift($menus, ['id' => 0, 'name' => '顶级菜单', 'parent_id' => 0]);
        $menus = json_encode($menus);
        $this->assign('menus', $menus);


        //获取权限列表
        $permission_model = D('Permission');
        $permissions = $permission_model->getList();
        $permissions = json_encode($permissions);
        $this->assign('permissions', $permissions);
    }

}