<?php

/**
 * 数据库 操作类库
 *
 * @author regel chen <regelhh@gmail.com>
 * @since 2014-5-16
 * @version 1.0 Beta
 */
class mysql implements iDB
{

    /**
     * @var object  数据库对象
     */
    private $_oDb = null;

    /**
     * @var string 数据库信息
     */
    private $_sDbName = null;  // 数据库 名称
    private $_sTblName = null;  // 数据表 名称
    private $_sNewTblName = null;  // 数据表 名称

    /**
     * @var 数据库操作信息
     */
    private $_aData = array();  // 插入数据
    private $_sWhere = null;  // 判断条件
    private $_sFields = '*';  // 查询字段
    private $_sOrder = null;  // 排序
    private $_sLimit = null;  // 限制条件
    private $_sAlias = null;  // 别名
    private $_sJoin = null;  // 表关联

    /**
     * 初始化
     *
     * @param array $config 配置信息
     */

    public function __construct($config = array())
    {
        if (extension_loaded('mysqli')) {
            include_once('mysqli.function.php');
        } else {
            include_once('mysql.function.php');
        }
        $config = empty($config) ? DBConfig() : $config;
        $this->_sDbName = trim($config['DB_NAME']);
        $this->_oDb = db($config['DB_HOST'], $config['DB_PORT'], $config['DB_USER'], $config['DB_PSW'], $config['DB_NAME'], $config['CHART_SET']);  // 数据库连接
    }

    /**
     * 析构函数
     */
    public function __destruct()
    {
        if (is_callable($this->_oDb)) {
            close_db($this->_oDb);
        }
        $this->_oDb = null;
        $this->_sDbName = null;
        $this->_sTblName = null;
        $this->_release_data();
    }

    /**
     * 释放 数据
     *
     * @return void
     */
    private function _release_data()
    {
        $this->_aData = array();  // 插入数据
        $this->_sNewTblName = null;  // 表名
        $this->_sWhere = null;  // 判断条件
        $this->_sFields = '*';  // 查询字段
        $this->_sOrder = null;  // 排序
        $this->_sLimit = null;  // 限制条件
        $this->_sAlias = null;  // 别名
        $this->_sJoin = null;  // 表关联
    }

    /**
     * 获取最后一条错误信息
     *
     * @return string
     */
    public function _errmsg()
    {
        return isset($GLOBALS['db']['last_error']) ? $GLOBALS['db']['last_error'] : null;
    }

    /**
     * 获取 数据库 版本
     *
     * @return int 服务器版本
     */
    public function get_server_version()
    {
        // mysqli_get_server_version
        return $this->_oDb->server_version;
    }

    /**
     * 获取 数据库 client 版本
     *
     * @return int client 版本
     */
    public function get_client_version()
    {
        // mysqli_get_client_version
        return $this->_oDb->client_version;
    }

    /**
     * 选择 数据表
     *
     * @param string $tbl_name 表名
     * @param string $db_name 数据库名
     * @return object
     */
    public function select_tbl($tbl_name, $db_name = null)
    {
        $tbl_name = trim($tbl_name);
        if (preg_match('/^(\w|\`|-)+$/', $tbl_name)) {
            $this->_sTblName = $tbl_name;
        }
        $db_name = trim($db_name);
        if (preg_match('/^(\w|\`|-)+$/', $db_name)) {
            $this->_sDbName = $db_name;  // 数据库名称
        }
        return $this;
    }

    /**
     * 数据处理
     *
     * @param array $array
     * @param boolean $tostring 转为字符串
     * @param string $relate 连接符
     * @return array
     */
    private function _data_handle($array, $tostring = false, $relate = ',')
    {
        $new_data = array();
        $data_str = '';
        $dbobj = $this->_oDb;
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $new_data[$key] = $value;
                continue;
            }
            if (!is_string($key)) {
                continue;
            }
            if (is_numeric($value) || is_null($value)) {
                $new_data['`' . $key . '`'] = $value;
                $data_str .= ' `' . $key . '` = ' . $value . ' ' . $relate;
            } else {
                $nv = escape_string($value, $dbobj);
                $new_data['`' . $key . '`'] = '"' . $nv . '"';
                $data_str .= ' `' . $key . '` = "' . $nv . '" ' . $relate;
            }
        }
        if ($tostring) {
            return trim(trim($data_str, $relate));
        }
        return $new_data;
    }

    /**
     * 组装 db & tbl
     *
     * @return string
     */
    private function _pack_dbntbl()
    {
        $tbl = empty($this->_sNewTblName) ? trim($this->_sTblName, '`') : trim($this->_sNewTblName, '`');
        $db = trim($this->_sDbName, '`');
        return '`' . $db . '`.`' . $tbl . '`';
    }

    /**
     * 运行SQL
     *
     * @param string $sql
     * @return mixed
     */
    private function _run_sql($sql)
    {
        $dbobj = $this->_oDb;
        if (empty($dbobj)) {
            $dbobj = db();  // 数据库连接
            $this->_oDb = $dbobj;
        }
        $result = run_sql($sql, $dbobj);
        $this->_release_data();
        return $result;
    }

    /**
     * 更换表名
     *
     * @param string $name 表名
     * @return object
     */
    public function table($name)
    {
        $name = trim($name);
        if (preg_match('/^(\w|\`|-)+$/', $name)) {
            $this->_sNewTblName = $name;
        }
        return $this;
    }

    /**
     * 插入的数据
     *
     * @param array $array
     * @return object
     */
    public function data($array)
    {
        $this->_aData = $this->_data_handle($array);
        return $this;
    }

    /**
     * 插入一条数据
     *
     * @param array $data
     * @return int  -false 失败 -0 非自增ID
     */
    public function insert($data = array())
    {
        $data = array_merge($this->_aData, $this->_data_handle($data));
        if (empty($data)) {
            return 0;
        }
        $sql = 'INSERT INTO ' . $this->_pack_dbntbl() .
//				'(' . implode(',', array_keys($data)) . ') VALUES (' . (implode(',', array_values($data))) . ')';
                '(' . implode(',', array_keys($data)) . ') VALUES (' . (str_replace(',,', ',null,', implode(',', array_values($data)))) . ')';
        $rs = $this->_run_sql($sql);
        if ($rs) {
            $insert_id = insert_id($this->_oDb);
            //			// 获取 最后插入ID
            //			$lsql = 'SELECT LAST_INSERT_ID() AS `id`';   // $sql = 'select @@IDENTITY';
            //			$data = get_data($lsql, $this->_oDb);
            //			$insert_id = $data[0]['id'];
            return $insert_id;
        }
        return false;
    }

    /**
     * 插入一组数据
     *
     * @param array $data
     * @return int  -false 失败 -0 非自增ID
     */
    public function insertAll($data = array())
    {
        if (empty($data)) {
            return false;
        }
        $tmp = array();
        $data = array_merge($this->_aData, $data);
        foreach ($data as $value) {
            $new_data = $this->_data_handle($value);
            $field = implode(',', array_keys($new_data));
            $tmp[$field][] = '(' . implode(',', array_values($new_data)) . ')';
        }
        $sql = '';
        foreach ($tmp as $k => $v) {
            $sql .= 'INSERT INTO ' . $this->_pack_dbntbl() .
                    '(' . $k . ') VALUES ' . implode(',', $v) . ';';
        }
        $rs = $this->_run_sql($sql);
        if ($rs) {
            $insert_id = insert_id($this->_oDb);
            return $insert_id;
        }
        return false;
    }

    /**
     * 条件字符串 过滤
     *
     * @param string $where_str
     * @return string
     */
    private function _where_filter($where_str)
    {
        if (empty($where_str)) {
            return false;
        }
        // 语句防注入
        if (preg_match('/[\'|"]\s*;\s*[\'|"]?\s*(update|insert|delete|drop|select)/i', $where_str)) {
            return false;
        }
        // 1=1 / 1=2 防注入
        if (preg_match('/(1\s*=\s*1)|(1\s*=\s*2)|(\d\s*=\s*\d)/', $where_str)) {
            return false;
        }
        return $where_str;
    }

    /**
     * 查询 条件
     *
     * @param string | array $where 条件
     *   ( 支持 "id=1" , array('id'=>1), array(array('id'=>1,'title'=>'regel'),'or') 三种格式 )
     * @return object
     */
    public function where($where)
    {
        if (empty($where)) {
            return $this;
        }
        if (is_array($where)) {  // 条件为数组
            $relate = 'AND';  // 关联
            if (isset($where[0]) && is_array($where[0])) {
                (isset($where[1]) && in_array($where[1], array('AND', 'OR', 'and', 'or'))) && $relate = strtoupper($where[1]);
                $where = $where[0];
            }
            $where_str = $this->_data_handle($where, true, $relate);
            if ($where_str) {
                $this->_sWhere = ' WHERE ' . $where_str;
            }
            return $this;
        }
        $where = $this->_where_filter($where);
        if ($where) {
            $this->_sWhere = ' WHERE ' . $where;
        }
        return $this;
    }

    /**
     * 更新数据
     *
     * @param array $data 插入数据
     * @param string | array $where 条件
     * @return int
     */
    public function update($data, $where = null)
    {
        if (empty($data) || !is_array($data)) {
            return 0;
        }
        $data = $this->_data_handle($data, true);  // 数据处理
        if (empty($data)) {
            return 0;
        }
        if (!is_null($where)) {
            $this->where($where);
        }
        $sql = 'UPDATE ' . $this->_pack_dbntbl() . ' SET ' . $data . $this->_sWhere;
        $rs = $this->_run_sql($sql);
        if ($rs) {
            // 成功返回影响行数
            return affected_rows($this->_oDb);
        }
        return 0;
    }

    /**
     * 删除操作
     *
     * @param string | array $where 条件
     * @return int
     */
    public function delect($where = null)
    {
        if (!is_null($where)) {
            // 传入条件
            $this->where($where);
        }
        $where = $this->_sWhere;
        if (empty($where)) {
            return 0;
        }
        $sql = 'DELETE FROM ' . $this->_pack_dbntbl() . $where;
        $rs = $this->_run_sql($sql);
        if ($rs) {
            // 成功返回影响行数
            return affected_rows($this->_oDb);
        }
        return 0;
    }

    /**
     * 字段 增减
     *
     * @param string $field 字段名
     * @param int $step 步入值
     * @param string $opt 操作
     * @return int
     */
    private function _crease($field, $step = 1, $opt = '+')
    {
        $step = is_numeric($step) ? abs($step) : intval($step);
        if (!preg_match('/^(\w|\.|`)+$/', $field)) {
            return false;
        }
        $field = strpos($field, '`') === false ? '`' . $field . '`' : $field;
        $sql = 'UPDATE ' . $this->_pack_dbntbl() . ' SET ' . $field . '=' . $field . $opt . $step . $this->_sWhere;
        $rs = $this->_run_sql($sql);
        if ($rs) {
            // 成功返回影响行数
            return affected_rows($this->_oDb);
        }
        return 0;
    }

    /**
     * 字段 增值
     *
     * @param string $field 字段名
     * @param int $step 步入值
     * @return int
     */
    public function increase($field, $step = 1)
    {
        return $this->_crease($field, $step, '+');
    }

    /**
     * 字段 减值
     *
     * @param string $field 字段名
     * @param int $step 步入值
     * @return int
     */
    public function decrease($field, $step = 1)
    {
        return $this->_crease($field, $step, '-');
    }

    /**
     * 查询的字段
     *
     * @param string | array $field 字段
     * @return object
     */
    public function field($field)
    {
        if (empty($field)) {
            return $this;
        }
        if (is_array($field)) {
            $new_field = array();
            $dbobj = $this->_oDb;
            foreach ($field as $value) {
                if (!is_string($value)) {
                    continue;
                }
                $new_field = '`' . escape_string($value, $dbobj) . '`';
            }
            $this->_sFields = implode(',', $new_field);
        } elseif (is_string($field)) {
            if (preg_match('/^(\w|\.|`|,)+$/', $field)) {
                // 去除空格
                $field = preg_replace('/(;|\s)+/', '', $field);
                $this->_sFields = (strpos($field, '.') === false) ? ((strpos($field, '`') === false) ? '`' . $field . '`' : $field) : $field;
            }
        }
        return $this;
    }

    /**
     * 排序 条件
     *
     * @param string $order
     * @return object
     */
    public function order($order)
    {
        if (preg_match('/^[\w\s\.\`]+$/i', $order)) {
            $this->_sOrder = ' ORDER BY ' . $order;
        }
        return $this;
    }

    /**
     * 获取单个元素
     *
     * @param string | array $where 条件
     * @param string | array $field 查询字段
     * @param string $order 排序条件
     * @return array
     */
    public function find($where = null, $field = null, $order = null)
    {
        if (!is_null($where)) {
            // 传入条件
            $this->where($where);
        }
        if (!is_null($field)) {
            $this->field($field);
        }
        if (!is_null($order)) {
            $this->order($order);
        }
        $sql = 'SELECT ' . $this->_sFields . ' FROM ' . $this->_pack_dbntbl() . $this->_sAlias . $this->_sJoin .
                $this->_sWhere . $this->_sOrder . ' LIMIT 1';
        $data = get_line($sql, $this->_oDb);
        return $data;
    }

    /**
     * 限制条件
     *
     * @param int | string $limit
     * @return object
     */
    public function limit($limit)
    {
        if (is_numeric($limit)) {
            $this->_sLimit = ' LIMIT 0,' . $limit;
        } elseif (strpos($limit, ',')) {
            $arr = explode(',', $limit);
            $this->_sLimit = ' LIMIT ' . intval($arr[0]) . ',' . intval($arr[1]);
        }
        return $this;
    }

    /**
     * 获取 多个结果集
     *
     * @param string | array $where 条件
     * @param string | array $field 查询字段
     * @param string $order 排序条件
     * @return array
     */
    public function select($where = null, $field = null, $order = null, $limit = null)
    {
        if (!is_null($where)) {
            // 传入条件
            $this->where($where);
        }
        if (!is_null($field)) {
            $this->field($field);
        }
        if (!is_null($order)) {
            $this->order($order);
        }
        if (!is_null($limit)) {
            $this->limit($limit);
        }
        $sql = 'SELECT ' . $this->_sFields . ' FROM ' . $this->_pack_dbntbl() . $this->_sAlias . $this->_sJoin .
                $this->_sWhere . $this->_sOrder . $this->_sLimit;
        $data = get_data($sql, $this->_oDb);
        return $data;
    }

    /**
     * 计算 查询结果
     *
     * @param string | array $where 查询条件
     * @return int
     */
    public function count($where = null)
    {
        if (!is_null($where)) {
            // 传入条件
            $this->where($where);
        }
        $sql = 'SELECT count(*) FROM ' . $this->_pack_dbntbl() . $this->_sAlias . $this->_sJoin . $this->_sWhere;
        $data = get_data($sql, $this->_oDb);
        return $data;
    }

    /**
     * 获取 最后一条 SQL 语句
     *
     * @return string
     */
    public function _sql()
    {
        return isset($GLOBALS['db']['last_sql']) ? $GLOBALS['db']['last_sql'] : '';
    }

    /**
     * 表 别名
     *
     * @param string $name
     * @return object
     */
    public function _as($name)
    {
        if (preg_match('/^(\w|`)+$/', $name)) {
            $this->_sAlias = (strpos($name, '`') === false) ? ' AS `' . $name . '` ' : ' AS ' . $name . ' ';
        }
        return $this;
    }

    /**
     * 表 关联 ( 默认左关联 )
     *
     * @args mixed 不固定参数
     *   ( 示例: 'user AS u ON a.uid = u.uid' | 'user AS u ON a.uid = u.uid','left' | array('user AS u ON a.uid = u.uid','right') |
     * 	 array('user AS u ON a.uid = u.uid','inner'),array('course AS c ON c.cid = a.cid','left'),... |
     * 	array('user AS u ON a.uid = u.uid','course AS c ON c.cid = a.cid'),'inner'  )
     * @return object
     */
    public function join()
    {
        $count = func_num_args();
        $relate = ' LEFT';  // 默认关联类型
        $relate_arr = array('LEFT', 'RIGHT', 'INNER');
        if ($count == 1) {
            $arg = func_get_arg(0);
            if (is_array($arg)) {
                if (isset($arg[1])) {
                    $arg1 = strtoupper($arg[1]);
                    in_array($arg1, $relate_arr) && ($relate = ' ' . $arg1);
                }
                $this->_sJoin = $relate . ' JOIN ' . $arg[0];
            } elseif (is_string($arg)) {
                $this->_sJoin = $relate . ' JOIN ' . $arg;
            }
        } elseif ($count == 2) {
            $args1 = func_get_arg(0);
            $args2 = func_get_arg(1);
            $join = '';
            if (is_string($args2)) {  // 第二个参数是字符串
                $arg = strtoupper($args2);
                in_array($arg, $relate_arr) && ($relate = ' ' . $arg);
                foreach ($args1 as $value) {
                    $join .= $relate . ' JOIN ' . $value;
                }
            } else {
                $rl = $relate;
                if (isset($args1[1])) {
                    $arg1 = strtoupper($args1[1]);
                    in_array($arg1, $relate_arr) && ($rl = ' ' . $arg1);
                }
                $join = $rl . ' JOIN ' . $args1[0];
                if (isset($args2[1])) {
                    $arg2 = strtoupper($args2[1]);
                    in_array($arg2, $relate_arr) && ($rl = ' ' . $arg2);
                }
                $join .= $rl . ' JOIN ' . $args2[0];
            }
            $this->_sJoin = $join;
        } elseif ($count > 2) {
            $args = func_get_args();  // 参数组
            $join = '';
            foreach ($args as $value) {
                $rl = $relate;
                if (!is_array($value)) {
                    // 非数组不处理
                    continue;
                }
                if (isset($value[1])) {  // 关联类型
                    $arg = strtoupper($value[1]);
                    in_array($arg, $relate_arr) && ($rl = ' ' . $arg);
                }
                $join .= $rl . ' JOIN ' . $value[0];
            }
            $this->_sJoin = $join;
        }
        return $this;
    }

    /**
     * 并联 操作
     *
     * @todo 不常用,有空再来完善
     * @return object
     */
    public function union()
    {
        // TODO:: 有空再来完善
        return $this;
    }

    /**
     * SQL 查询
     *
     * @param string $sql
     * @return mixed
     */
    public function query($sql)
    {
        if (empty($sql)) {
            return false;
        }
        $data = get_data($sql, $this->_oDb);
        if (count($data) == 1) {
            return $data[0];
        }
        return $data;
    }
}
