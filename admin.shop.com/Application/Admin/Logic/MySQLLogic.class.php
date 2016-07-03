<?php
/**
 * Created by PhpStorm.
 * User: sone
 * Date: 2016/6/28
 * Time: 14:38
 */

namespace Admin\Logic;


class MySQLLogic implements DbMysql{


    public function connect()
    {
        // TODO: Implement connect() method.
        echo __METHOD__;
        dump(func_get_args());
        echo '<hr/>';
    }

    public function disconnect()
    {
        // TODO: Implement disconnect() method.
        echo __METHOD__;
        dump(func_get_args());
        echo '<hr/>';
    }

    public function free($result)
    {
        // TODO: Implement free() method.
        echo __METHOD__;
        dump(func_get_args());
        echo '<hr/>';
    }

    /**  就是执行sql语句的写操作 ;
     * @param string $sql
     * @param array $args
     * @return mixed
     */
    public function query($sql, array $args = array())
    {
        //获取所有实参数据
        $args=func_get_args();
        //弹出第一行的sql语句
        $sql = array_shift($args);
        //将sql语句进行分离
        $pam = preg_split('/\?[NFT]/',$sql);
        array_pop($pam);
        //重置清空sql
        $sql='';
        foreach($pam as $key=>$value){
            $sql.=$value.$args[$key];
        }
        //执行一个写操作
        $rows = M()->execute($sql);
        //只要第一行的语句
        return array_shift($rows);
    }
       //新增一条记录
    public function insert($sql, array $args = array())
    {
        // TODO: Implement insert() method.
        //获取所有实参数据
        $args=func_get_args();
        $sql = $args[0];
        $table_name = $args[1];
        $params = $args[2];
        $sql = str_replace('?T',$table_name,$sql);
        $tmp =[];
        foreach($params as $key=>$value){
            $tmp[] .= $key . '="'.$value.'"';
        }
        //把语句中?%改掉 后将数组改为字符串并用逗号连接
        $sql = str_replace('?%',implode(',',$tmp),$sql);
        if(M()->execute($sql)!=false){
        //返回最后插入的id;
        return M()->getLastInsID();
        }else{
            return false;
         }

    }

    public function update($sql, array $args = array())
    {
        // TODO: Implement update() method.


        echo __METHOD__;
        dump(func_get_args());
        echo '<hr/>';
    }

    public function getAll($sql, array $args = array())
    {
        // TODO: Implement getAll() method.
        echo __METHOD__;
        dump(func_get_args());
        echo '<hr/>';
    }

    public function getAssoc($sql, array $args = array())
    {
        // TODO: Implement getAssoc() method.
        echo __METHOD__;
        dump(func_get_args());
        echo '<hr/>';
    }
    /**  获取一行记录
     * @param string $sql
     * @param array $args
     *
     */
    public function getRow($sql, array $args = array())
    {
        // TODO: Implement getRow() method.
        //获取所有实参数据
        $args=func_get_args();
        //弹出第一行的sql语句
        $sql = array_shift($args);
        //将sql语句进行分离
        $pam = preg_split('/\?[NFT]/',$sql);
        array_pop($pam);
        //重置清空sql
        $sql='';
        foreach($pam as $key=>$value){
            $sql.=$value.$args[$key];
        }
        //调用基类执行拼接的语句 query返回一个二维数组
        $rows = M()->query($sql);
        //只要第一行的语句
        return array_shift($rows);
        //dump($row);
       // dump($pam);

    }

    public function getCol($sql, array $args = array())
    {
        // TODO: Implement getCol() method.
        echo __METHOD__;
        dump(func_get_args());
        echo '<hr />';
    }

    public function getOne($sql, array $args = array())
    {
        // TODO: Implement getOne() method.
        //获取所有实参数据
        $args=func_get_args();
        //弹出第一行的sql语句
        $sql = array_shift($args);
        //将sql语句进行分离
        $pam = preg_split('/\?[NFT]/',$sql);
        array_pop($pam);
        //重置清空sql
        $sql='';
        foreach($pam as $key=>$value){
            $sql.=$value.$args[$key];
        }
        //调用基类执行拼接的语句 query返回一个二维数组
        $rows = M()->query($sql);
        //获取第一行
        $row = array_shift($rows);
        //获取第一列的字段值
        return array_shift($row);

    }
}