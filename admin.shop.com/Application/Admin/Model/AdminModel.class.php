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
use Think\Verify;

class AdminModel extends Model{
    //批量验证//开启批量验证
    protected $patchValidate = true;
    /**
     *
     * 验证条件
     */
    protected $_validate =[
        ['username','require','用户名不能为空'],
        ['username','','用户名已被占用',self::EXISTS_VALIDATE,'unique','register'],
        ['password','require','密码不能为空',self::EXISTS_VALIDATE,'',],
        ['password','6,16','密码长度不合法',self::EXISTS_VALIDATE,'length'],
        ['repassword','password','两次密码不一致',self::EXISTS_VALIDATE,'confirm'],
        ['email','require','邮箱不能为空'],
        ['email','email','邮箱格式不合法',self::EXISTS_VALIDATE],
        ['email','','邮箱已被占用',self::EXISTS_VALIDATE,'unique'],


       // ['captcha','require','验证码必填',self::EXISTS_VALIDATE,'','login'],
        //['captcha','check_captcha','验证码不正确',self::EXISTS_VALIDATE,'callback','login']
    ];
    /**
     * 1. add_time 当前时间
     * 2. 盐 自动生成随机盐
     * @var type
     */
    protected $_auto =[
        ['add_time',NOW_TIME,'register'],
        ['salt','\Org\Util\String::randString','register','function']
    ];
    /**验证传进来的值 ,进行验证码验证
     * @param $code
     * @return bool
     */
    public function check_captcha($code){
        $verify = new Verify();
        return $verify->check($code);
    }
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

    /**
     * 登录方法
     */
        public function login(){

            $username = $this->data['username'];
            $password = $this->data['password'];
            //为了安全我们将用户信息都删除
            session('USERINFO',null);
            //先验证用户名是否正确
            $userinfo = $this->getByUsername($username);

            if(!$userinfo){
                $this->error='用户名验证错误';
                return false;
            }
            //验证密码匹配盐是否正确
                $passwords = salt_mcrypt($password,$userinfo['salt']);
                if($passwords!=$userinfo['password']){
                    $this->error='密码不正确';
                    return false;
                }
            //保存登录的时间和IP
            $data = [
                'last_login_time' => NOW_TIME,
                'last_login_ip' => get_client_ip(),
                'id'=> $userinfo['id'],
            ];
            $this->save($data);


            //把用户信息保存到session中
                login($userinfo);
            //删除用户相关的token记录
            $admin_token_model = M('AdminToken');
            $admin_token_model->delete($userinfo['id']);
            //获取用户权限
            $this->getPermissions($userinfo['id']);
                //自动登陆相关
            if (I('post.remember')) {
                //生成cookie和数据表数据
                $data = [
                    'admin_id' => $userinfo['id'],
                    'token'    => \Org\Util\String::randString(40),
                ];

                cookie('USER_AUTO_LOGIN_TOKEN', $data, 604800); //保存一个星期

                $admin_token_model->add($data);
            }
            return $userinfo;
        }

    /**获取用户权限列表,从角色和额外权限的关联表中获取,
     * @param $admin_id
     * @return bool
     */
        private function getPermissions($admin_id){
//SELECT DISTINCT path FROM admin_role AS ar JOIN
// role_permission AS rp ON ar.`role_id`=rp.`role_id` JOIN
// permission AS p ON p.`id`=rp.`permission_id` WHERE path<>''
// AND admin_id=1
            //筛选的条件 空路径的,id想等的
            $cond = [
                'path' => ['neq',''],
                'admin_id'=>$admin_id,
            ];
            //根据用户ID找到他对应的权限id和权限
            $permissions = M()->distinct(true)->field('permission_id,path')->table('admin_role')->alias('ar')->join('__ROLE_PERMISSION__ as rp ON ar.role_id =rp.role_id')->join('__PERMISSION__ as p ON p.id =rp.permission_id')->where($cond)->select();

            //-------------
            $pids = [];
            $paths       = [];
            foreach ($permissions as $permission) {
                $paths[] = $permission['path'];
                $pids[] = $permission['permission_id'];
            }
            permission_pathes($paths);
            permission_pids($pids);
            return true;
        }

    /**自动登录
     * @return bool|mixed
     */
    public function autoLogin() {
        //从cookie中取出数据
        $data = cookie('USER_AUTO_LOGIN_TOKEN');
        if (!$data) {
            return false;
        }

        //和数据表中的对比
        $admin_token_model = M('AdminToken');
        if (!$admin_token_model->where($data)->count()) {
            return false;
        }
        //为了避免token被窃取,自动登陆一次就重置
        $admin_token_model->delete($data['admin_id']);
        //生成cookie和数据表数据
        $data = [
            'admin_id' => $data['admin_id'],
            'token'    => \Org\Util\String::randString(40),
        ];

        cookie('USER_AUTO_LOGIN_TOKEN', $data, 604800); //保存一个星期
        $admin_token_model->add($data);//将新token保存到数据表中.

        //如果匹配,保存用户信息到session中
        $userinfo = $this->find($data['admin_id']);
        login($userinfo);

        //获取并保存用户权限
        $this->getPermissions($userinfo['id']);
        return $userinfo;
    }

    public function rest(){
        $id=login()['id'];
        //从session中获取当前用户的密码和盐;
        //获取提交的原来的密码
        $oldpassword = $this->data['password'];
        //把密码加盐加密
        $passwords = salt_mcrypt($oldpassword,login()['salt']);
        //和session中的密码做对比
        if($passwords!=login()['password']){
            $this->error='原密码不正确';
            return false;
        }
        $newpassword=I('post.newpassword');
        $newpassword = salt_mcrypt($newpassword,login()['salt']);
        //如果获取的值不为空
        $data=[];
        if($newpassword){
            $data=[
                'id'=>$id,
                'password'=>$newpassword,
            ];

            $this->save($data);
        }

    }
}