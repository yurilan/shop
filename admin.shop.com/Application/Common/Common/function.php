<?php
/**
 * Created by PhpStorm.
 * User: sone
 * Date: 2016/6/24
 * Time: 19:22
 * ��������Ϣ����ת���������б�.
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