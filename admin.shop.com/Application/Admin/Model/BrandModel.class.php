<?php
/**
 * Created by PhpStorm.
 * User: sone
 * Date: 2016/6/25
 * Time: 9:33
 */

namespace Admin\Model;


use Think\Model;
use Think\Page;
class BrandModel extends Model{
        //开启批量验证
     protected $patchValidate = true;
        /**
         * 验证条件
         */
    protected $_validate =[
        ['name','require','品牌名字不能为空'],
        ['name','','品牌已存在',self::EXISTS_VALIDATE,'unique'],
        ['status','0,1','品牌状态不合法',self::EXISTS_VALIDATE,'in'],
        ['sort','number','排序必须为数字'],
    ];

    public function getPageResult(array $cond=[]){

        //获取分页代码
        //获取分页配置
        $page_setting =C('PAGE_SETTING');
        //获取总行数
        $count = $this->where($cond)->count();
        $page = new Page($count,$page_setting['PAGE_SIZE']);
        //显示数据条数
        $page->setConfig('theme', $page_setting['PAGE_THEME']);
        $page_html = $page->show();
        //分页数据获取
        $rows = $this->where($cond)->page(I('get.p',1),$page_setting['PAGE_SIZE'])->select();
        //返回数据
        return compact(['rows','page_html']);
    }
}