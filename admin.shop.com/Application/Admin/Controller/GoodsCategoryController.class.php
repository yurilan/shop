<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Admin\Controller;
use Think\Controller;

/**
 * Description of BrandController
 *
 * @author qingf
 */
class GoodsCategoryController extends Controller {

    /**
     * @var \Admin\Model\GoodsCategoryModel 
     */
    private $_model = null;

    protected function _initialize() {
        $this->_model = D('GoodsCategory');
    }

    public function index() {
        //获取品牌列表
        $this->assign('rows',$this->_model->getList());
        $this->display();
    }

    public function add() {
        if (IS_POST) {
            //收集数据
            if($this->_model->create()===false){
                $this->error(get_error($this->_model));
            }
            if($this->_model->addCategory() === false){
                $this->error(get_error($this->_model));
            }
            $this->success('添加成功',U('index'));
        }else{
            $this->before_view();
            $this->display();
        }
    }
    public function edit($id) {
        if (IS_POST) {
            //收集数据
            if($this->_model->create()===false){
                $this->error(get_error($this->_model));
            }
            if($this->_model->saveCategory() === false){
                $this->error(get_error($this->_model));
            }
            $this->success('修改成功',U('index'));
        } else {
            //展示数据
            $row = $this->_model->find($id);
            $this->assign('row', $row);
            //获取所有的分类
            $goods_categories = json_encode($this->_model->getList());
            $this->assign('goods_categories', $goods_categories);
            $this->display('add');
        }
    }
    public function remove($id) {
        if($this->_model->deleteCategory($id)===false){
            $this->error(get_error($this->_model));
        }else{
            $this->success('删除成功',U('index'));
        }
    }
    //添加顶级分类
    public function before_view(){
        $goods_categories = $this->_model->getList();
        array_unshift($goods_categories,['id'=>0,'name'=>'顶级分类','parent_id'=>0]);
        $goods_categories = json_encode($goods_categories);
        $this->assign('goods_categories',$goods_categories);
    }
}
