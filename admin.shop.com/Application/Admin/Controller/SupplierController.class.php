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
         * �б�ҳ��,��ʾ�������б�
         */
        public function index(){
            //����ģ�Ͷ���
            $supplier_model = D('Supplier');
            //��ѯ����
            $rows = $supplier_model->select();
            //��������
            $this->assign('rows',$rows);
            //������ͼ
            $this->display();
        }

        public function add(){

            if(IS_POST){
                //����ģ��
                $supplier_model = D('Supplier');
                //�ռ�����
                $supplier_model->create();
                //��������
                //��ʾ��ת

            }else{
                //������ͼ
                $this->display();
            }

        }

        public function edit(){



        }

        public function remove(){



        }

}