<?php
/**
 * Created by PhpStorm.
 * User: sone
 * Date: 2016/6/25
 * Time: 11:48
 */

namespace Admin\Controller;
use Think\Controller;

class ArticleController extends Controller{
    private $_model = null;
    protected function _initialize(){
        $this->_model = D('Article');
    }

    //显示列表
    //显示文章列表
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

    //文章的添加
    public function add(){

        if(IS_POST){
            //创建模型
            $this->_model = D("Article");
            //收集验证数据
            if($this->_model->create()===false){
                $this->error(get_error($this->_model));
            }
            //保存数据
            if($this->_model->addArticle(I('post.'))===false){
                $this->error(get_error($this->_model));
            }else{
                $this->success('添加成功',U('index'));
            }

            //成功跳转
        }else{
            //准备好所有的文章分类的数据
            $ArticleCategoryModel = D('ArticleCategory');
            $rows = $ArticleCategoryModel->where(["status"=>['egt',0]])->select();
            $this->assign('rows',$rows);
            $this->display();
        }
    }
    //文章的编辑
    //编辑文章
    public function edit($id)
    {
        if (IS_POST){
            //验证
            if ($this->_model->create() === false) {
                $this->error(get_error($this->_model));
            }
            //修改
            if ($this->_model->saveArticle(I('post.')) === false) {
                $this->error(get_error($this->_model));
            }
            $this->success('修改成功', U('index'));
        }else{
            //准备好所有的文章分类的数据
            $ArticleCategoryModel = M('ArticleCategory');
            $ArticleContentModel = M("ArticleContent");
            $rows = $ArticleCategoryModel->where(["status"=>['egt',0]])->select();
            //var_dump($rows);exit;
            //$ros=$ArticleContentModel->where(array('article_id'=>$id))->select();
            $ros = $ArticleContentModel->find($id);
            //获取数据
            $row = $this->_model->find($id);
            //展示页面
            //回显文章内容
            $this->assign('ros', $ros);
            //回显文章数据
            $this->assign('row', $row);
            //回显文章分类
            $this->assign('rows', $rows);
            $this->display('add');
        }
    }
    /**
     * 根据id删除一个角色
     * @param $id
     */
    public function remove($id){
        $this->_model = D("Article");
        $result = $this->_model->deleteArticle($id);
        if($result!==false){
            $this->success("删除成功!",U('index'));
        }else{
            $this->error("删除失败!");
        }
    }
}