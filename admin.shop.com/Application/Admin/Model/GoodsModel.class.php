<?php
/**
 * Created by PhpStorm.
 * User: sone
 * Date: 2016/6/29
 * Time: 11:41
 */

namespace Admin\Model;


use Think\Model;
use Think\Page;

class GoodsModel extends Model{

    //批量验证
    protected $patchValidate = ture;

    //自动验证
    /**
     *  商品名必填
     *  商品分类必填
     *  品牌必填
     *  供货商必填
     *  市场价必填,必须是货币 currency 货币
     *  商城价格必填,必须是货币
     *  库存必填,必须是数字
     * ...
     */
    protected $_validate     = [
        ['name', 'require', '商品名称不能为空'],
        ['sn', '', '货号已存在', self::VALUE_VALIDATE,'unique'],
        ['goods_category_id', 'require', '商品分类不能为空'],
        ['brand_id', 'require', '品牌不能为空'],
        ['supplier_id', 'require', '供货商不能为空'],
        ['market_price', 'require', '市场价不能为空'],
        ['market_price', 'currency', '市场价不合法'],
        ['shop_price', 'require', '售价不能为空'],
        ['shop_price', 'currency', '售价不合法'],
        ['stock', 'require', '库存不能为空'],
    ];
    //自动完成
    //计算数组中所有值的和 array_sum
    protected $_auto =[
        ['sn','createSn',self::MODEL_INSERT,'callback'],
        ['goods_status', 'crrun', self::MODEL_BOTH, 'callback'],
        ['inputtime', NOW_TIME, self::MODEL_INSERT],
    ];

    //判断复选框的值
    protected function  crrun($goods_status){
        if(isset($goods_status)){
          return  array_sum($goods_status);
        }else{
          return 0;
        }
    }

    /**
     * 判断是否提交了货号,如果没有,就生成一个.
     */
    protected function createSn($sn){
       //开启事务
        $this->startTrans();
        //如果已经有写入的货号,就什么都不做
        if($sn){
            return $sn;
        }
        //生成规则:SN年月日编号:SN2016062800001
        //1.获取今天已经常见了多少个商品
        //获取当天时间
        $date = date('Ymd');
        $goods_num_model = M('GoodsNum');
        //将计算的的数保存到数据表中
        //如果当天有num的话就num+1,没有就然那个当天num=1;
        if($num = $goods_num_model->getFieldByDate($date,'num')){
            ++$num;
            $data = ['date'=>$date,'num'=>$num];
            //修改数据库中的商品数量的数据
            $flag = $goods_num_model->save($data);
        }else{
            $num =1;
            $data =['date'=>$date,'num'=>$num];
            //当天没有就添加一个num进去
            $flag = $goods_num_model->add($data);
        }
        if($flag === false){
            $this->rollback();
        }
        //计算sn是多少
        //2.计算SN
        //str_pad()使用另一个字符串填充字符串为指定长度
        //   指定长度为5为把0填充在左边
        $sn = 'SN' . $date . str_pad($num, 5, '0', STR_PAD_LEFT);
       // dump($sn);exit;
        return $sn;
    }

    //添加商品 事务在自动完成的创建sn的方法中开启,在这里提交或者回滚.
    /***
     * @return bool
     */
    public function addGoods(){

        //保存基本信息
        if (($goods_id = $this->add()) === false) {
            //$this->rollback();
            return false;
        }
        //保存详细描述到商品intro表
        $data = [
          'goods_id'=>$goods_id,
            //获取过来的HTML代码不被转义存入 开起 false
            'content'=>I('post.content','',false),
        ];
        $goods_intro_model = M('GoodsIntro');
        if($goods_intro_model->add($data)===false){
            $this->rollback();
            return false;
        }
        //保存相册
        $goods_gallery_model = M('GoodsGallery');

        $pathes = I('post.path');

        foreach($pathes as $path){
            $arr[]=[
              'goods_id'=>$goods_id,
                'path'=>$path,
            ];
        }
        // var_dump($arr);exit;
        if($arr && ($goods_gallery_model->addAll($arr)===false)){
            $this->rollback();
            return false;
        }
        $this->commit();
        return true;
    }
    /**
     * 获取分页数据
     * @param array $cond 查询条件.
     * @return type
     */
    public function getPageResult(array $cond = []){
            //合并成一个数组
        $cond = array_merge(['status'=>1],$cond);
        //先获取总条数
        $count = $this->where($cond)->count();
        //获取分页代码
        $page_setting = C('PAGE_SETTING');
        $page = new Page($count,$page_setting['PAGE_SIZE']);
        $page->setConfig('theme',$page_setting['PAGE_THEME']);
        $page_html = $page->show();
        //获取分页数据
        $rows = $this->where($cond)->page(I('get.p',1),$page_setting['PAGE_SIZE'])->select();
        //由于列表页要展示是否是新品精品热销,但是这些信息放在一个字段中,所以为了简化视图代码,我们在模型中处理好后再返回
        foreach ($rows as $key => $value) {
            $value['is_best'] = $value['goods_status'] & 1 ? true : false;
            $value['is_new']  = $value['goods_status'] & 2 ? true : false;
            $value['is_hot']  = $value['goods_status'] & 4 ? true : false;
            $rows[$key] = $value;
        }
        return compact('rows', 'page_html');
    }
    /**
     * 获取商品信息,包括详细介绍和相册.
     * @param integer $id 商品id.
     * @return type
     */
    public function getGoodsInfo($id) {
        //获取商品的基本信息
        $row = $this->find($id);
        //由于在前端展示的时候,需要使用到各个状态,所以我们变成一个json对象
        $row['goods_status'];
        $tmp = [];
        if($row['goods_status']&1){
            $tmp[] = 1;
        }
        if($row['goods_status']&2){
            $tmp[] = 2;
        }
        if($row['goods_status']&4){
            $tmp[] = 4;
        }
        $row['goods_status'] = json_encode($tmp);
        unset($tmp);
        //获取商品的详细描述
        $goods_intro_model = M('GoodsIntro');
        $row['content'] = $goods_intro_model->getFieldByGoodsId($id,'content');
        //获取商品的相册
        $goods_gallery_model = M('GoodsGallery');
        $row['galleries']=$goods_gallery_model->getFieldByGoodsId($id,'id,path');
        return $row;
    }

    /**
     * 修改商品 包括商品详细描述和相册.
     * @return boolean
     */
    public function saveGoods() {
        $request_data = $this->data;
        $this->startTrans();
        //1.保存基本信息
        if($this->save()===false){
            $this->rollback();
            return false;
        }
        //2.保存详细描述
        $data              = [
            'goods_id' => $request_data['id'],
            'content'  => I('post.content', '', false),
        ];
        $goods_intro_model = M('GoodsIntro');
        if ($goods_intro_model->save($data) === false) {
            $this->rollback();
            return false;
        }
        //3.保存相册
        $goods_gallery_model = M('GoodsGallery');
        $pathes = I('post.path');
        $data = [];
        foreach($pathes as $path){
            $data[] = [
                'goods_id'=>$request_data['id'],
                'path'=>$path,
            ];
        }
        //如果上传了相册,并且相册保存失败,就回滚
        if($data && ($goods_gallery_model->addAll($data)===false)){
            $this->rollback();
            return false;
        }
        $this->commit();
        return true;
    }

}