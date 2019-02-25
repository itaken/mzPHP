<?php

/**
 * 数据库接口
 *
 * @author regel chen <regelhh@gmail.com>
 * @since 2014-6-4
 * @version 1.0 Beta
 */
interface iDB
{
    
    /**
     * 获取 client 版本信息
     *
     * @return mixed
     */
    public function get_client_version();

    /**
     * 选择 数据表
     *
     * @param string $tbl_name 表名
     * @param string $db_name 数据库名
     * @return object
     */
    public function select_tbl($tbl_name, $db_name = null);

    /**
     * 更换表名
     *
     * @param string $name 表名
     * @return object
     */
    public function table($name);

    /**
     * 插入的数据
     *
     * @param array $array
     * @return object
     */
    public function data($array);

    /**
     * 插入一条数据
     *
     * @param array $data
     * @return int  -false 失败 -0 非自增ID
     */
    public function insert($data = array());

    /**
     * 插入一组数据
     *
     * @param array $data
     * @return int  -false 失败 -0 非自增ID
     */
    public function insertAll($data = array());

    /**
     * 查询 条件
     *
     * @param string | array $where 条件
     *   ( 支持 "id=1" , array('id'=>1), array(array('id'=>1,'title'=>'regel'),'or') 三种格式 )
     * @return object
     */
    public function where($where);

    /**
     * 更新数据
     *
     * @param array $data 插入数据
     * @param string | array $where 条件
     * @return int
     */
    public function update($data, $where = null);

    /**
     * 删除操作
     *
     * @param string | array $where 条件
     * @return int
     */
    public function delect($where = null);

    /**
     * 字段 增值
     *
     * @param string $field 字段名
     * @param int $step 步入值
     * @return int
     */
    public function increase($field, $step = 1);

    /**
     * 字段 减值
     *
     * @param string $field 字段名
     * @param int $step 步入值
     * @return int
     */
    public function decrease($field, $step = 1);

    /**
     * 查询的字段
     *
     * @param string | array $field 字段
     * @return object
     */
    public function field($field);

    /**
     * 排序 条件
     *
     * @param string $order
     * @return object
     */
    public function order($order);

    /**
     * 获取单个元素
     *
     * @param string | array $where 条件
     * @param string | array $field 查询字段
     * @param string $order 排序条件
     * @return array
     */
    public function find($where = null, $field = null, $order = null);

    /**
     * 限制条件
     *
     * @param int | string $limit
     * @return object
     */
    public function limit($limit);

    /**
     * 获取 多个结果集
     *
     * @param string | array $where 条件
     * @param string | array $field 查询字段
     * @param string $order 排序条件
     * @return array
     */
    public function select($where = null, $field = null, $order = null, $limit = null);

    /**
     * 计算 查询结果
     *
     * @param string | array $where
     * @return int
     */
    public function count($where = null);

    /**
     * 表 关联 ( 默认左关联 )
     *
     * @args mixed 不固定参数
     * @return object
     */
    public function join();

    /**
     * 并联 操作
     *
     * @todo 不常用,有空再来完善
     * @return object
     */
    public function union();

    /**
     * SQL 查询
     *
     * @param string $sql
     * @return mixed
     */
    public function query($sql);
    
    /**
     * 获取 最后一条错误
     */
    public function _errmsg();
}
