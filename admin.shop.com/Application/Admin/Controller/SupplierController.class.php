<?php
/**
 * Created by PhpStorm.
 * User: sone
 * Date: 2016/6/24
 * Time: 18:05
 */

namespace Admin\Controller;


use Think\Controller;

class SupplierController extends Controller{
    private $_model = null;
    //调父类方法
    protected function _initialize(){
        $this->_model = D('Supplier');
    }
        /**
         * 列表页面,显示供货商列表
         */
        public function index(){
            //获取搜索关键词
            $name = I('get.name');
            $cond['status'] = ['egt',0];
            if($name){
                $cond['name']=['like','%'.$name.'%'];
            }
            $data = $this->_model->getPageResult($cond);
            //传递数据
            $this->assign($data);
            //调用视图
            $this->display();
        }

        public function add(){

            if(IS_POST){
                //创建模型
               $this->_model  = D('Supplier');
                //收集数据
               if( $this->_model->create()===false){
                   $this->error(get_error($this->_model));
               }
                //保存数据
                //提示跳转
                if( $this->_model->add()===false){
                    $this->error(get_error($this->_model));
                }else{
                   $this->success('添加成功',U('index'));
               }

            }else{
                //调用视图
                $this->display();
            }

        }
        //修改供应商
        public function edit($id){
            if(IS_POST){
                //验证
                if($this->_model->create()===false){
                    $this->error(get_error($this->_model));
                }
                //修改
                if($this->_model->save()===false){
                    $this->error(get_error($this->_model));
                }
                $this->success('修改成功',U('index'));
            }else{
                //获取数据
               $row = $this->_model->find($id);
                //展示页面
                $this->assign('row',$row);
                $this->display('add');
            }


        }
        //逻辑删除供应商,在原来的名字上添加_del下划线
        public function remove($id){
            //调用模型删除
            //exp 去掉函数外的引号
            $data = [
                'id'=>$id,
                'status'=>-1,
                'name'=>['exp','concat(name,"_del")'],
            ];
            //setField更改字段内容
            if($this->_model->setField($data) === false){
                $this->error(get_error($this->_model));
            }else{
                $this->success('删除成功',U('index'));
            }
        }


}