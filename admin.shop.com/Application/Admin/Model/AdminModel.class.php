<?php
/**
 * Created by PhpStorm.
 * User: sone
 * Date: 2016/7/2
 * Time: 13:10
 */

namespace Admin\Model;


use Think\Model;
use Think\Page;

class AdminModel extends Model{
    //批量验证//开启批量验证
    protected $patchValidate = true;
    /**
     *
     * 验证条件
     */
    protected $_validate =[
        ['username','require','用户名不能为空'],
        ['username','','用户名已被占用',self::EXISTS_VALIDATE,'unique'],
        ['password','require','密码不能为空',self::EXISTS_VALIDATE],
        ['password','6,16','密码长度不合法',self::EXISTS_VALIDATE,'length'],
        ['repassword','password','两次密码不一致',self::EXISTS_VALIDATE,'confirm'],
        ['email','require','邮箱不能为空'],
        ['email','email','邮箱格式不合法',self::EXISTS_VALIDATE],
        ['email','','邮箱已被占用',self::EXISTS_VALIDATE,'unique'],
    ];
    /**
     * 1. add_time 当前时间
     * 2. 盐 自动生成随机盐
     * @var type
     */
    protected $_auto =[
        ['add_time',NOW_TIME],
        ['salt','\Org\Util\String::randString',self::MODEL_INSERT,'function']
    ];

    //创建管理员
    public function addAdmin(){
        //加盐加密
        $this->data['password'] = salt_mcrypt($this->data['password'],$this->data['salt']);
        //直接添加基本信息;
        if(($admin_id = $this->add())===false){
            //$this->rollback();
            return false;
        }
        //保存管理员角色关联
        $admin_role_model = M('AdminRole');
        $data = [];
        $role_ids = I('post.role_id');
        foreach($role_ids as $role_id){
            $data[]=[
                'role_id'=>$role_id,
                'admin_id'=>$admin_id,
            ];
        }
        if($data){
            if($admin_role_model->addAll($data)===false){
                $this->error('添加角色关联失败');
                //$this->rollback();
                return false;
            }
        }
        // $this->commit;
        return true;
    }


    /** 获取分页数据
     * @param array $cond
     * @return array
     */
    public function getPageResult(array $cond=[]){
        //获取总行数
            $count = $this->where($cond)->count();
        //获取配置
            $page_setting = C('PAGE_SETTING');
        //工具类对象的加载
            $page = new Page($count,$page_setting['PAGE_SIZE']);
        //设置主题
            $page->setConfig('theme',$page_setting['PAGE_THEME']);
        //获取分页代码
            $page_html = $page->show();
        //获取分页数据
            $rows = $this->where($cond)->page(I('get.p',1),$page_setting['PAGE_THEME'])->select();
        //返回数据
         // compact() 函数创建包含变量名和它们的值的数组。
            return compact('rows','page_html');

    }

    /**
     * @param $id
     * @return mixed
     */
    public function getAdminInfo($id){
        //根据要编辑的用户 找到中间表的角色
        $row = $this->find($id);
        $admin_role_model = M('AdminRole');
        $row['role_ids'] = json_encode($admin_role_model->where(['admin_id'=>$id])->getField('role_id',true));
        return $row;
    }

    /** 修改管理员
     * @param $id 管理员id
     * @return bool
     */
    public function saveAdmin($id){
        $this->startTrans();
        //保存管理员的修改的角色关联

        $admin_role_model = M('AdminRole');

        $data =[];
        //获取到传过来的角色id
        $role_ids = I('post.role_id');
        //var_dump($id);exit;
        foreach($role_ids as $role_id){
            $data[]=[
                'admin_id'=>$id,
                'role_id'=>$role_id,
            ];
        }
        //删除原有的关联的角色
        if(($admin_role_model->where(['admin_id'=>$id])->delete())===false){
            $this->error = '删除原有的角色失败';
            $this->rollback();
            return false;
        }
        //判断有没有关联
        if($data){
            if($admin_role_model->addAll($data)===false){
                $this->error ='保存角色关联失败';
                $this->rollback();
                return false;
            }
        }
        $this->commit();
        return true;
    }

    public function deleteAdmin($id){
        $this->startTrans();
        //删除admin表中的管理员
        if($this->delete($id)===false){
            $this->rollback();
            return false;
        }
        //删除关联表中的记录
        $admin_role_model = M('AdminRole');
        if($admin_role_model->where(['admin_id'=>$id])->delete()===false){
            $this->error ='删除角色关联失败';
            $this->rollback();
            return false;
        }
        $this->commit();
        return true;

    }


}