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
        /**
         * 列表页面,显示供货商列表
         */
        public function index(){
            //创建模型对象
            $supplier_model = D('Supplier');
            //查询数据
            $rows = $supplier_model->select();
            //传递数据
            $this->assign('rows',$rows);
            //调用视图
            $this->display();
        }

        public function add(){

            if(IS_POST){
                //创建模型
                $supplier_model = D('Supplier');
                //收集数据
                $supplier_model->create();
                //保存数据
                //提示跳转

            }else{
                //调用视图
                $this->display();
            }

        }

        public function edit(){



        }

        public function remove(){



        }

}