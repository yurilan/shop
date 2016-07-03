<?php
/**
 * Created by PhpStorm.
 * User: sone
 * Date: 2016/7/2
 * Time: 1:02
 */

namespace Admin\Model;


use Think\Model;
use Think\Page;

class RoleModel extends Model{

    /**
     * 获取分页数据
     * @param array $cond
     * @return type
     */
    public function getPageResult(array $cond=[]) {
        //查询条件
        $cond = array_merge(['status'=>1],$cond);
        //总行数
        $count = $this->where($cond)->count();
        //获取配置
        $page_setting = C('PAGE_SETTING');
        //工具类对象
        $page = new Page($count, $page_setting['PAGE_SIZE']);
        //设置主题
        $page->setConfig('theme', $page_setting['PAGE_THEME']);
        //获取分页代码
        $page_html = $page->show();
        //获取分页数据
        $rows = $this->where($cond)->page(I('get.p',1),$page_setting['PAGE_SIZE'])->select();
        return compact('rows', 'page_html');
    }

    public function addRole(){
        $this->startTrans();
        //保存基本信息
        if(($role_id = $this->add())===false){
            $this->rollback();
            return false;
        }

        //保存关联的权限
        $permission_ids = I('post.permission_id');
        $data=[];
        foreach($permission_ids as $permission_id){
            $data[] = [
                'role_id'=>$role_id,
                'permission_id'=>$permission_id,
            ];
        }
        //如果有传过来的值 保存到中间表中
        if($data){
            $role_permission_model = M('RolePermission');
            if($role_permission_model->addAll($data) ===false){
                $this->error = '保存权限失败';
                $this->rollback();
                return false;
            }
        }
        $this->commit();
        return true;
    }
    //回显基本信息和对应权限
    public function getPermissionInfo($id){
        //获取当前角色基本信息
        $row = $this->find($id);
        //获取关联的权限信息
        $role_permission_model = M('RolePermission');
        $row['permission_ids'] = json_encode($role_permission_model->where(['role_id'=>$id])->getField('permission_id',true));
        return $row;
    }
    //执行修改方法
    public function saveRole() {
        $this->startTrans();
        $role_id = $this->data['id'];
        //保存基本信息
        if($this->save()===false){
            $this->rollback();
            return false;
        }
        //在中间表-删除原有的权限
        $role_permission_model = M('RolePermission');
        if($role_permission_model->where(['role_id'=>$role_id])->delete()===false){
            $this->error = '删除历史权限失败';
            $this->rollback();
            return false;
        }
        //保存关联的权限
        $permission_ids = I('post.permission_id');
        $data = [];
        foreach($permission_ids as $permission_id){
            //把新的权限放在数组中
            $data[] = [
                'role_id'=>$role_id,
                'permission_id'=>$permission_id,
            ];
        }
        //如果或去到了数据,就把得到的数据添加到中间表中;
        if($data){
            if($role_permission_model->addAll($data) ===false){
                $this->error = '保存权限失败';
                $this->rollback();
                return false;
            }
        }
        $this->commit();
        return true;
    }

    public function deleteRole($id){

        $this->startTrans();
        //删除角色记录
        if($this->delete($id) === false){
            $this->rollback();
            return false;
        }

        //删除权限关联-删除中间表中的对应数据
        $role_permission_model = M('RolePermission');
        if($role_permission_model->where(['role_id'=>$id])->delete()===false){
            $this->error = '删除权限关联失败';
            $this->rollback();
            return false;
        }
        //删除管理员关联
        $admin_role_model = M('AdminRole');
        //删除关联的角色
        if($admin_role_model->where(['role_id'=>$id])->delete()===false){
            $this->error = '删除管理员关联失败';
            $this->rollback();
            return false;
        }
        $this->commit();
        return true;
    }
    //找所有的显示角色
    public function getList() {
        return $this->where(['status'=>1])->select();
    }
}