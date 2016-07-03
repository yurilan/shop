<?php
/**
 * Created by PhpStorm.
 * User: sone
 * Date: 2016/6/29
 * Time: 11:34
 */

namespace Admin\Controller;

use Think\Controller;

class GoodsController extends Controller{
    /**
     * @var \Admin\Model\GoodsModel
     */
    private $_model = null;

    protected function _initialize() {
        $this->_model = D('Goods');
    }

    public function index() {
        //接受查询的条件,进行条件拼接
        $name = I('get.name');
        $cond = [];
        if($name){
            $cond['name']=['like','%'.$name.'%'];
        }
        //分类的数据条件
        $goods_category_id = I('get.goods_category_id');
        if($goods_category_id){
            $cond['goods_category_id'] = $goods_category_id;
        }

        //品牌的数据条件
        $brand_id = I('get.brand_id');
        if($brand_id){
            $cond['brand_id'] = $brand_id;
        }

        //促销的状态
        $goods_status = I('get.goods_status');
        if($goods_status){
            //与上获取的值
            $cond[] = 'goods_status &'.$goods_status;
          //  $cond['goods_status'] = ['exp','&$goods_status'];
        }
        //是否上架
        $is_on_sale = I('get.is_on_sale');
        if(strlen($is_on_sale)){
            $cond['is_on_sale'] = $is_on_sale;
        }


        //获取商品列表
        $this->assign($this->_model->getPageResult($cond));

        //获取分类数据
        $goods_category_model = D('GoodsCategory');
        $goods_categories     = $goods_category_model->getList();
        $this->assign('goods_categories', $goods_categories);

        //获取品牌数据条件
        $brand_model = D('Brand');
        $brands      = $brand_model->getList();
        $this->assign('brands', $brands);

        //获取促销的状态
        $goods_statuses =[
            ['id'=>1,'name'=>'精品'],
            ['id' => 2, 'name' => '新品',],
            ['id' => 4, 'name' => '热销',]
        ];
        $this->assign('goods_statuses', $goods_statuses);

        //获取上架状态
        $is_on_sales    = [
            ['id' => 1, 'name' => '上架',],
            ['id' => 0, 'name' => '下架',],
        ];
        $this->assign('is_on_sales', $is_on_sales);

        $this->display();
    }

    //添加商品
    public function add(){

        if(IS_POST){
            //收集数据
            if($this->_model->create()===false){
                $this->error(get_error($this->_model));
            }
            //添加商品
            if($this->_model->addGoods()===false){
                $this->error(get_error($this->_model));
            }
              $this->success('添加成功',U('index'));
               // dump(I('post.','',false));
        }else{
            $this->before_view();
            $this->display();
        }

    }
    //编辑商品
    public function edit($id){
        if (IS_POST) {
            if($this->_model->create() === false) {
                $this->error(get_error($this->_model));
            }
            //修改商品
            if ($this->_model->saveGoods() === false) {
                $this->error(get_error($this->_model));
            }
            $this->success('修改成功', U('index'));
        } else {
            //1.获取数据
            $row = $this->_model->getGoodsInfo($id);
            //2.传递数据
            $this->assign('row', $row);
            $this->before_view();
            $this->display('add');
        }
    }
    //删除商品
    public function remove($id){
        //调用模型删除
        //exp 去掉函数外的引号
        $data = [
            'id'=>$id,
            'status'=>0,
        ];
        //setField更改字段内容
        if($this->_model->setField($data) === false){
            $this->error(get_error($this->_model));
        }else{
            $this->success('删除成功',U('index'));
        }
    }

    //获取数据
    private function  before_view(){

        //获取所有的商品的分类,使用ztree展示,所以转换成json
        $goods_category_model = D('GoodsCategory');
        $goods_categories = $goods_category_model->getList();
        $this->assign('goods_categories',json_encode($goods_categories));

        //获取所有的品牌列表
        $brand_model = D('Brand');
        $brands = $brand_model->getList();
        //var_dump($brands);
        $this->assign('brands',$brands);

        //获取所有的供货商列表
        $supplier_model = D('Supplier');
        $suppliers = $supplier_model->getList();
        $this->assign('suppliers',$suppliers);
    }
}