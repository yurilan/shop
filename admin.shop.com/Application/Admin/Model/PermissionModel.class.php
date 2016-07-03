<?php
/**
 * Created by PhpStorm.
 * User: sone
 * Date: 2016/7/1
 * Time: 18:58
 */

namespace Admin\Model;


use Admin\Logic\NestedSets;
use Think\Model;

class PermissionModel extends Model{

    protected $_validate = [
        ['name', 'require', '权限名称不能为空']
    ];

    /**
     * 获取权限列表
     */
    public function getList(){
        return $this->where(['status'=>1])->order('lft')->select();
    }

    //使用nestedsets添加权限
    public function addPermission(){
        //  报错时,错误删除主键
        unset($this->data[$this->getPk()]);
        //创建orm
        $orm  = D('MySQL','Logic');
        //创建nestedsets对象
        $nestedsets = new NestedSets($orm, $this->getTableName(), 'lft', 'rght', 'parent_id', 'id', 'level');
        if ($nestedsets->insert($this->data['parent_id'], $this->data, 'bottom') === false) {
            $this->error = '添加失败';
            return false;
        }
        return true;
    }

    /**
     * 编辑权限.
     * @return bool
     */
    public function savePermission(){
        //判断是否修改了父级权限
        $parent_id = $this->getFieldById($this->data['id'],'parent_id');
        //要修改的父级不等于原来的父级
        if($parent_id != $this->data['parent_id']){
            //创建orm
            $orm = D('MySQL','Logic');
            //创建nestedsets对象
            $nestedsets = new NestedSets($orm, $this->getTableName(), 'lft', 'rght', 'parent_id', 'id', 'level');
            if($nestedsets->moveUnder($this->data['id'], $this->data['parent_id'], 'bottom') === false) {
                $this->error = '不能将分类移动到自身或后代分类中';
                return false;
            }
        }
        //保存基本数据
        return $this->save();
    }

    public function deletePermission($id){
        $this->startTrans();
        //获取后代权限
        //找到当前权限的左右节点
        $permission_info = $this->field('lft,rght')->find($id);
        $cond = [
            'lft' =>['egt',$permission_info['lft']],//大于等于左节点的
            'rght'=>['elt',$permission_info['rght']],
        ];
        //
        $permission_ids = $this->where($cond)->getField('id',true);
        //


        //创建orm
        $orm        = D('MySQL', 'Logic');
        //创建nestedsets对象
        $nestedsets = new NestedSets($orm, $this->getTableName(), 'lft', 'rght', 'parent_id', 'id', 'level');

        if ($nestedsets->delete($id) === false) {
            $this->error = '删除失败';
            $this->rollback();
            return false;
        } else {
            $this->commit();
            return true;
        }
    }






}