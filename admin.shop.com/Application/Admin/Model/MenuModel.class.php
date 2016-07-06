<?php

namespace Admin\Model;

class MenuModel extends \Think\Model {

    protected $patchValidate = true; //开启批量验证

    /**
     * name 必填，不能重复
     * status 可选值0-1
     * sort 必须是数字
     * @var type
     */
    protected $_validate = [
        ['name', 'require', '菜单名称不能为空'],
    ];

    /**
     * 获取所有的商品菜单。
     * @return array
     */
    public function getList() {
        return $this->where(['status' => ['egt', 0]])->order('lft')->select();
    }

    /**
     * 完成菜单的添加，和计算左右节点和层级的功能。
     * 使用nestedsets实现
     */
    public function addMenu() {
        $this->startTrans();
        unset($this->data[$this->getPk()]);
        //创建ORM对象
        $orm        = D('MySQL', 'Logic');
        //创建nestedsets对象
        $nestedsets = new \Admin\Logic\NestedSets($orm, $this->getTableName(), 'lft', 'rght', 'parent_id', 'id', 'level');
        if (($menus_id   = $nestedsets->insert($this->data['parent_id'], $this->data, 'bottom')) === false) {
            $this->error = '添加菜单失败';
            $this->rollback();
            return false;
        }
        //将权限和菜单进行绑定
        $menu_permission_model = M('MenuPermission');
        $data                  = [];
        $permission_ids        = I('post.permission_id');
        foreach ($permission_ids as $permission_id) {
            $data[] = [
                'menu_id'       => $menus_id,
                'permission_id' => $permission_id,
            ];
        }
        if ($data) {
            if ($menu_permission_model->addAll($data) === false) {
                $this->error = '保存权限关联失败';
                $this->rollback();
                return false;
            }
        }
        $this->commit();
        return true;
    }

    /**
     * 编辑菜单，并且自动计算左右节点和层级
     * 不允许移动到后代菜单下去.
     */
    public function saveMenu() {
        $this->startTrans();
        //判断是否修改了父级菜单,如果没修改,就不要创建nestedsets
        //获取原来的父级菜单,要使用getFieldById因为find会将数据放到data属性中
        $parent_id = $this->getFieldById($this->data['id'], 'parent_id');
        if ($this->data['parent_id'] != $parent_id) {
            //获取当前的父级菜单
            //创建ORM对象
            $orm        = D('MySQL', 'Logic');
            //创建nestedsets对象
            $nestedsets = new \Admin\Logic\NestedSets($orm, $this->getTableName(), 'lft', 'rght', 'parent_id', 'id', 'level');
            //moveUnder只计算左右节点和层级，不保存其它数据
            if ($nestedsets->moveUnder($this->data['id'], $this->data['parent_id'], 'bottom') === false) {
                $this->error = '不能将菜单移动到后代菜单下';
                return false;
            }
        }

        //保存关联关系
        $menu_permission_model = M('MenuPermission');
        //先删除历史关系
        if ($menu_permission_model->where(['menu_id' => $this->data['id']])->delete() === false) {
            $this->error = '删除历史关联失败';
            $this->rollback();
            return false;
        }

        //保存新的权限关联
        $data           = [];
        $permission_ids = I('post.permission_id');
        foreach ($permission_ids as $permission_id) {
            $data[] = [
                'menu_id'       => $this->data['id'],
                'permission_id' => $permission_id,
            ];
        }
        if ($data) {
            if ($menu_permission_model->addAll($data) === false) {
                $this->error = '保存权限关联失败';
                $this->rollback();
                return false;
            }
        }

        //保存基本信息
        if ($this->save() === false) {
            $this->rollback();
            return false;
        }
        $this->commit();
        return true;
    }

    /**
     * 删除菜单，并且自动计算左右节点和层级
     */
    public function deleteMenu($id) {
        $this->startTrans();

        //删除关联
        //保存关联关系
        $menu_permission_model = M('MenuPermission');
        //先删除历史关系
        //查询出子级菜单列表
        $info = $this->field('lft,rght')->find($id);
        $cond = [
            'lft'=>['egt',$info['lft']],
            'rght'=>['elt',$info['rght']],
        ];
        $menu_ids = $this->where($cond)->getField('id',true);
        if ($menu_permission_model->where(['menu_id' => ['in',$menu_ids]])->delete() === false) {
            $this->error = '删除历史关联失败';
            $this->rollback();
            return false;
        }


        //获取当前的菜单,及其后代菜单
        //创建ORM对象
        $orm        = D('MySQL', 'Logic');
        //创建nestedsets对象
        $nestedsets = new \Admin\Logic\NestedSets($orm, $this->getTableName(), 'lft', 'rght', 'parent_id', 'id', 'level');
        //delete会将所有的后代菜单一并删除,并且重新计算相关节点的左右节点.
        if($nestedsets->delete($id)===false){
            $this->rollback();
            return false;
        }


        $this->commit();
        return true;
    }

    /**
     * 获取菜单信息,包括关联的权限
     * @param integer $id 菜单id.
     * @return type
     */
    public function getMenuInfo($id) {
        $row                   = $this->find($id);
        $menu_permission_model = M('MenuPermission');
        $row['permission_ids'] = json_encode($menu_permission_model->where(['menu_id' => $id])->getField('permission_id', true));
        return $row;
    }

    /**
     * 获取用户可见的菜单,超级管理员可以看到所有菜单
     * @return array 菜单列表
     */
    public function getMenuList() {

        //如果是超级管理员,就可以看到所有的菜单
        $userinfo = login();
        if($userinfo['username']=='admin'){
            //获取用户菜单的id
            $menus = $this->distinct(true)->field('id,parent_id,name,path')->alias('m')->join('__MENU_PERMISSION__ as mp ON mp.menu_id=m.id')->select();
        }else{
            //获取用户权限id
            $pids = permission_pids();

            //获取用户菜单的id
            if($pids){
                $menus = $this->distinct(true)->field('id,parent_id,name,path')->alias('m')->join('__MENU_PERMISSION__ as mp ON mp.menu_id=m.id')->where(['permission_id'=>['in',$pids]])->select();
            }else{
                $menus = [];
            }
        }
        //获取菜单信息
        return $menus;
    }
}
