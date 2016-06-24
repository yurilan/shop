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