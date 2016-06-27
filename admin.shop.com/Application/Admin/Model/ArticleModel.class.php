<?php
/**
 * Created by PhpStorm.
 * User: sone
 * Date: 2016/6/25
 * Time: 14:09
 */

namespace Admin\Model;


use Think\Model;
use     Think\Page;

class ArticleModel extends Model
{

    //开启批量验证
    protected $patchValidate = true;
    /**
     * 验证条件
     */
    protected $_validate = [
        ['name', 'require', '文章名字不能为空'],
        ['name', '', '文章名字已存在', self::EXISTS_VALIDATE, 'unique'],
        ['status', '0,1', '文章状态不合法', self::EXISTS_VALIDATE, 'in'],
        ['sort', 'number', '排序必须为数字'],
        ['article_category_id', '0', '选择文章分类', self::EXISTS_VALIDATE, 'notequal']
    ];


    public function getPageResult(array $cond = [])
    {
        //获取分页代码
        //获取分页配置
        $page_setting = C('PAGE_SETTING');
        //获取总行数
        $count = $this->where($cond)->count();
        $page = new Page($count, $page_setting['PAGE_SIZE']);
        //显示数据条数
        $page->setConfig('theme', $page_setting['PAGE_THEME']);
        $page_html = $page->show();

        //分页数据获取
        $rows = $this->where($cond)->page(I('get.p', 1), $page_setting['PAGE_SIZE'])->select();
        $article = [];
        $model = M('ArticleCategory');
        foreach ($rows as &$row) {
            $ar['id'] = $row['article_category_id'];
            $row['ca'] = $model->where($ar)->getField('name');
           // $article[] = $row;
        }
        // var_dump($article);
      //  $rows = $article;
        unset($row); //建议
        //返回数据
        return compact(['rows', 'page_html']);
    }

    //添加方法
    public function addArticle($content)
    {
        //$this->data()中有文章表需要的数据
        //$content中有内容表需要的数据
        //开启事务
        $this->startTrans();
        //将create收集的数据添加到数据表中
        $article_id = parent::add();
        if ($article_id === false) {
            $this->rollback();
            return false;
        }
        //将传过来的文章内容添加到文章内容表中
        //获取文章内容
        $articleContent = $content['content'];
        //获取棒文章的id和内容插入到表中;
        $rows = ["article_id" => $article_id,
            "content" => $articleContent];
        // var_dump($articleContent);exit;
        //  var_dump($rows);exit;
        if (!empty($rows)) {
            //调用文章内容表
            $ArticleContentModel = M("ArticleContent");
            $result = $ArticleContentModel->add($rows);
            if ($result === false) {
                $this->rollback();
                return false;
            }
        }
        $this->commit();
        //返回数据  true
        return true;

    }


    //将请求中的数据更新到内容表和文章表

    public function saveArticle($requestData){
            $this->startTrans();
            //将文章表中的数据更新到数据库中
            $result = parent::save();
        if($result===false){
            $this->rollback();
            return false;
        }
            //将新的文章内容更新到ArticleContent表中
             //文章内容的
             $cond['content'] = $requestData['content'];
             $cond['article_id'] = $requestData['id'];
       // var_dump($cond);exit;
          if (!empty($cond)) {
            //调用文章内容表
            $ArticleContentModel = M("ArticleContent");
            $result = $ArticleContentModel->save($cond);
            if ($result === false) {
                $this->rollback();
                return false;
            }
        }
         $this->commit();
        return ture;
      }

    public function deleteArticle($Articleid){
        $this->startTrans();
        //删除Article表中数据
        $result = parent::delete($Articleid);
        if($result===false){
            $this->rollback();  //回滚事务
            return false;
        }
        //删除ArticleContent表中数据
        $ArticleContentModel = M("ArticleContent");
        $result = $ArticleContentModel->where(array("article_id"=>$Articleid))->delete();
        if($result===false){
            $this->rollback();  //回滚事务
            return false;
        }
        $this->commit();//提交事务
 }

}