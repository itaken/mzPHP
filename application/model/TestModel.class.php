<?php

defined('INI') or die('--TestModel--');

/**
 * 测试 模型
 *
 * @author regel chen<regelhh@gmail.com>
 * @since 2014-3-26
 * @version 1.0 RC1
 */
class TestModel extends CoreModel
{

    /**
     * @var string 真实表名 ( 为空,则表名为 当前模型名称 test,加上表前缀 )
     */
    protected $_sTrueTbl = '';

    /**
     * 添加内容
     */
    public function add()
    {
        $data = array(
            'title' => '测试', 'description' => '就是测试而已!',
        );
        // 写入数据
        $rs = $this->data($data)->insert();
        //		$rs = $this->insert($data);
        var_dump($rs);
    }

    /**
     * 修改内容
     */
    public function sve()
    {
        $where = array('id' => 3); // or $where = 'id = 3';
        $data = array('title' => 'test', 'description' => 'just test!');
        // 更新
        $rs = $this->where($where)->update($data);
        //		$rs = $this->update($data, $where);
        var_dump($rs);
    }

    /**
     * 删除内容
     */
    public function del()
    {
        $where = array('id' => 3); // or $where = 'id = 3';
        $rs = $this->where($where)->delect();
        //		$rs = $this->delect($where);
        var_dump($rs);
    }

    /**
     * 查询内容
     */
    public function slt()
    {
        // 查询单个结果集
        $where = array('id' => 3);
        $rs = $this->where($where)->find();
        var_dump($rs);

        // 查询多个结果集
        $rs = $this->where($where)->select();
        //		$rs = $this->_as('t')->where($where)->select();
        var_dump($rs);

        // 语句查询
        $sql = 'SELECT LAST_INSERT_ID()';
        //		$sql = 'SELECT * FROM test1';
        $rs = $this->query($sql);
        var_dump($rs);

        // 关联查询 ( 此处where条件必须是字符串类型 )
        $rs = $this->_as('t1')->join(array('test2 as `t2` ON t1.id = t2.id',))->where('t1.id = 3')->select();
        var_dump($rs);

        // 打印 最后一条SQL语句
        echo $this->_sql();
    }

    /**
     * 测试
     *
     * @return array
     */
    public function sidebar()
    {
        $bar = array();
        for ($i = 0; $i < 2; $i ++) {
            $side = array();
            for ($j = 0; $j < 3; $j++) {
                $side[] = '子目录 ' . $i . '-' . $j;
            }
            $bar['侧边栏-'.$i] = $side;
        }
        return $bar;
    }
}
