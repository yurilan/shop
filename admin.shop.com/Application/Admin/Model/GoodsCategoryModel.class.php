<?php
namespace Admin\Model;

use Think\Model;

class GoodsCategoryModel extends Model {

    protected $patchValidate = true; //开启批量验证
    /**
     * name 必填，不能重复
     * status 可选值0-1
     * sort 必须是数字
     * @var type
     */
    protected $_validate     = [
        ['name', 'require', '商品分类名称不能为空'],
    ];
    /**
     * 获取所有的商品分类。
     * @return array
     */
    public function getList() {
        return $this->where(['status'=>['egt',0]])->order('lft')->select();
    }

}
