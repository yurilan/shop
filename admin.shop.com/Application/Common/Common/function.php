<?php
/**
 * Created by PhpStorm.
 * User: sone
 * Date: 2016/6/24
 * Time: 19:22
 * 将错误信息加载转换成有序列表.
 */
/**
 * @param \Think\Model $model
 *@return string
 */
function get_error(\Think\Model $model){
    $errors = $model->getError();
    $html = '<ol>';
    foreach($errors as $error){
        $html.='<li>'.$error . '</li>';
    }
    $html.='</ol>';
    return $html;
}

/**将一个关联数组转换成下拉列表
 * @param array $data 关联数组,二维数组.
 * @param string $name_field 提示文本的字段名.
 * @param string $value_field value数据的字段名.
 * @param string $name 表单控件的name属性.
 * @param string $default_value 回显是传过来的数据值
 * @return string
 */
function arr2select(array $data,$name_field='name',$value_field='id',$name = '',$default_value=''){

    $html = '<select name="'.$name.'" class="'.$name.'">';
    $html .= '<option value="">--请选择--</option>';
    foreach ($data as $key => $value) {
        //如果传进来的id = 回显值id
        //由于get和post提交的数据都是字符串,所以可能存在数字0和空字符串相等的问题
        //我们将遍历的数据变成string,然后强制类型比较.
        if((string)$value[$value_field] == $default_value){
            $html .= '<option value="' . $value[$value_field] . '" selected="selected">' . $value[$name_field] . '</option>';
        }else{
            $html .= '<option value="' . $value[$value_field] . '">' . $value[$name_field] . '</option>';
        }
    }
    $html .= '</select>';
   // dump($html);
    return $html;

}

/**加盐加密
 * @param $password 原密码
 * @param $salt  盐
 * @return string
 */
function salt_mcrypt($password,$salt){
    return md5(md5($password).$salt);
}
/**
 * 获取和设置用户session
 * @param mixed $data
 * @return type
 */
function login($data=null){
    if(is_null($data)){
        return session('USERINFO');
    }else{
        //不是null传$data放进session中
        session('USERINFO',$data);
    }
}
/**
 * 获取和设置用户权限session
 * @param mixed $data
 * @return type
 */
function permission_pathes($data=null){
    if(is_null($data)){
        $pathes = session('PERMISSION_PATHES');
        if(!is_array($pathes)){
            $pathes = [];
        }
        return $pathes;
    }else{
        session('PERMISSION_PATHES',$data);
    }
}
/**
 * 获取和设置用户权限ID session
 * @param mixed $data
 * @return type
 */
function permission_pids($data=null){
    if(is_null($data)){
        $pids = session('PERMISSION_PIDS');
        if(!is_array($pids)){
            $pids = [];
        }
        return $pids;
    }else{
        session('PERMISSION_PIDS',$data);
    }
}