<?php
/**
 * Created by PhpStorm.
 * User: sone
 * Date: 2016/6/25
 * Time: 9:11
 */

namespace Admin\Controller;
use Think\Controller;

class BrandController extends Controller{
    private $_model = null;
    protected function _initialize(){
        $this->_model = D('Brand');
    }
    //显示品牌列表
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

    //添加品牌
    public function add(){
        if(IS_POST){
            //创建模型
            $this->_model = D("Brand");
            //收集验证数据
            if($this->_model->create()===false){
                $this->error(get_error($this->_model));
            }
            //保存数据
            if($this->_model->add()===false){
                $this->error(get_error($this->_model));
            }
            $this->success('添加成功',U('index'));
            //成功跳转

        }else{

            $this->display();
        }


    }


    //修改品牌
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
    //逻辑删除品牌,在原来的名字上添加_del下划线
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