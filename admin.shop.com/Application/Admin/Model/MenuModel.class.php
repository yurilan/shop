<?php
/**
 * Created by PhpStorm.
 * User: sone
 * Date: 2016/7/4
 * Time: 0:30
 */

namespace Admin\Model;


use Admin\Logic\NestedSets;
use Think\Model;

class MenuModel extends Model{
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


    public function addMenu(){
        $this->startTrans();
        //创建ORM对象
        unset($this->data[$this->getPk()]);
        //创建ORM对象
        $orm  = D('MySQL', 'Logic');
        //创建nestedsets对象
        $nestedsets = new NestedSets($orm, $this->getTableName(), 'lft', 'rght', 'parent_id', 'id', 'level');
        //获取菜单id
        if (($menus_id = $nestedsets->insert($this->data['parent_id'], $this->data, 'bottom'))===false) {
            $this->error = '添加菜单失败';
            $this->rollback();
            return false;
        }
        //将权限和菜单进行绑定
        $menu_permission_model = M('MenuPermission');
        $data = [];
        //获取权限id
        $permission_ids = I('post.permission_id');
        foreach ($permission_ids as $permission_id) {
            $data[] = [
                'menu_id'=>$menus_id,
                'permission_id'=>$permission_id,
            ];
        }
        if ($data) {
            //添加到中间表中
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
     * 获取菜单关联的权限
     * @param integer $id 菜单id.
     * @return type
     */
    public function getMenuInfo($id) {
        //找到当前id所对应的菜单
        $row  = $this->find($id);
        $menu_permission_model = M('MenuPermission');
        //找到中间表中嗦对应的权限巴拉巴拉巴拉~``
        $row['permission_ids'] = json_encode($menu_permission_model->where(['menu_id' => $id])->getField('permission_id', true));
        return $row;
    }

    public function saveMenu(){
        $this->startTrans();
        //判断是否修改了父级菜单,如果没修改就不要创建nestedsets
        //获取原来的父级菜单,要使用getFieldById因为find会将数据放到data属性中
        $parent_id = $this->getFieldById($this->data['id'], 'parent_id');
        if ($this->data['parent_id'] != $parent_id) {
            //获取当前的父级菜单
            //创建ORM对象
            $orm = D('MySQL', 'Logic');
            //创建nestedsets对象
            $nestedsets = new NestedSets($orm, $this->getTableName(), 'lft', 'rght', 'parent_id', 'id', 'level');
            //moveUnder只计算左右节点和层级，不保存其它数据
            if ($nestedsets->moveUnder($this->data['id'], $this->data['parent_id'], 'bottom') === false) {
                $this->error = '不能将菜单移动到后代菜单下';
                return false;
            }
        }

        //保存和权限中间表的关联关系
        $menu_permission_model = M('MenuPermission');
        //先删除历史关系
        if($menu_permission_model->where(['menu_id'=>$this->data['id']])->delete()===false){
            $this->error ='删除历史关联失败';
            $this->rollback();
            return false;
        }
        //保存修改的新的权限关联
        $data  = [];
        $permission_ids = I('post.permission_id');
        foreach ($permission_ids as $permission_id) {
            $data[] = [
                'menu_id'=>$this->data['id'],
                'permission_id'=>$permission_id,
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
}