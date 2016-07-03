<?php
namespace Admin\Model;

use Admin\Logic\NestedSets;
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

    /**
     * 完成分类的添加，和计算左右节点和层级的功能。
     * 使用nestedsets实现
     */
    public function addCategory() {
        unset($this->data[$this->getPk()]);
            //创建ORM对象
        $orm =  D('MySQL','Logic');
            //创建nestedsets对象
        $nestedsets = new NestedSets($orm,$this->trueTableName,'lft','rght','parent_id','id','level');

        return $nestedsets->insert($this->data['parent_id'],$this->data,'bottom');

    }
        //编辑分类,并且自动计算左右节点和层级
        //不允许移动到后代分类下去.
    public function saveCategory(){
            //判断是否修改了父级分类,如果没修改,就不要创建nestedsets
        //获取原来的父级分类,要使用getFieIdById因为find会将数据放到data属性中
        $parent_id = $this->getFieldById($this->data['id'],'parent_id');

            if($this->data['parent_id']!=$parent_id){
                //获取当前的父级分类
                //创建ORM对象
                $orm  = D('MySQL', 'Logic');
                //创建nestedsets对象
                $nestedsets = new NestedSets($orm, $this->trueTableName, 'lft', 'rght', 'parent_id', 'id', 'level');
                //moveUnder只计算左右节点和层级，不保存其它数据
                if ($nestedsets->moveUnder($this->data['id'], $this->data['parent_id'], 'bottom') === false) {
                    $this->error = '不能将分类移动到后代分类下';
                    return false;
                }
        }
        return $this->save();
    }

    //删除分类,并且自动计算左右节点和层级

    public function deleteCategory($id){
        //获取当前的父级分类
        //创建ORM对象
        $orm = D('MySQL','Logic');
        //创建nestedsets对象
        $nestedsets = new NestedSets($orm, $this->trueTableName, 'lft', 'rght', 'parent_id', 'id', 'level');
        //delete会将所有的后代分类一并删除,并且重新计算相关节点的左右节点.
        return $nestedsets->delete($id);
    }

}
