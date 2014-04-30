<?php

defined('INI') or die('--CoreModel--');

/**
 * 核心 模型类
 * 
 * @author regel chen<regelhh@gmail.com>
 * @since 2014-3-22
 * @version 1.0 Beta
 */
class CoreModel {

	/**
	 * @var object 
	 */
	private $_oDb = null;  // 数据库对象

	/**
	 * @var string 数据库信息
	 */
	private $_sDbName = null;  // 数据库 名称
	private $_sTblName = null;  // 数据表 名称
	protected $_sTrueTbl = null;  // 真实 数据表名称

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
	 */
	public function __construct() {
		// 引入数据库操作对象
		$db_type = c('DB_TYPE');  // 连接类型
		Mlib('db:' . $db_type . '.function');   // 载入 数据操作 库
		$this->_sDbName = '`' . c('DB_NAME') . '`';  // 数据库名称
		// 组装 数据表
		$true_tbl = $this->_sTrueTbl;  // 定义 真实表名
		if (empty($true_tbl)) {
			$mod = get_class($this); // 子类名称
			$match = array();
			preg_match_all('/[A-Z]{1}[a-z0-9]+/', str_replace('Model', '', $mod), $match);
			$tbl_name = c('TBL_PREFIX') . strtolower(implode('_', $match[0])) . c('TBL_SUFFIX');  // 表名
		} else {
			$tbl_name = $true_tbl;
		}
		$this->_sTblName = '`' . $tbl_name . '`';  // 完整表名
		
	}

	/**
	 * 析构函数
	 */
	public function __destruct() {
		if (is_object($this->_oDb)) {
			close_db($this->_oDb);
		}
		$this->_oDb = null;
		$this->_sDbName = null;
		$this->_sTblName = null;
		$this->_sWhere = null;
		$this->_sFields = null;
		$this->_sOrder = null;
	}

	/**
	 * 数据处理
	 * 
	 * @param array $array
	 * @param boolean $tostring 转为字符串
	 * @param string $relate 连接符
	 * @return array 
	 */
	private function _dataHandle($array, $tostring = FALSE, $relate = ',') {
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
	 * 运行SQL
	 * 
	 * @param string $sql 
	 * @return mixed 
	 */
	private function _runSQL($sql) {
		$dbobj = $this->_oDb;
		if(empty($dbobj)){
			$dbobj = db();  // 数据库连接
			$this->_oDb = $dbobj;
		}
		return run_sql($sql, $dbobj);
	}
	
	/**
	 * 更换表名
	 * 
	 * @param string $name 表名
	 * @return object
	 */
	protected function table($name) {
		if (is_string($name)) {
			$this->_sTblName = $name;
		}
		return $this;
	}

	/**
	 * 插入的数据
	 * 
	 * @param array $array
	 * @return object 
	 */
	protected function data($array) {
		$this->_aData = $this->_dataHandle($array);
		return $this;  // 连贯操作,返回对象
	}

	/**
	 * 插入一条数据
	 * 
	 * @param array $data
	 * @return int  -false 失败 -0 非自增ID
	 */
	protected function insert($data = array()) {
		$data = array_merge($this->_aData, $this->_dataHandle($data));
		if (empty($data)) {
			return 0;
		}
		$sql = 'INSERT INTO ' . $this->_sDbName . '.' . $this->_sTblName .
				'(' . implode(',', array_keys($data)) . ') VALUES (' . (implode(',', array_values($data))) . ')';
		$rs = $this->_runSQL($sql);
		if ($rs) {
			$insert_id = insert_id($this->_oDb);
//			// 获取 最后插入ID
//			$lsql = 'SELECT LAST_INSERT_ID() AS `id`';   // $sql = 'select @@IDENTITY';
//			$data = get_data($lsql, $this->_oDb);
//			$insert_id = $data[0]['id'];
			return $insert_id;
		}
		return FALSE;
	}

	/**
	 * 插入一组数据
	 * 
	 * @param array $data
	 * @return int  -false 失败 -0 非自增ID
	 */
	protected function insertAll($data = array()) {
		if (empty($data)) {
			return FALSE;
		}
		$tmp = array();
		$data = array_merge($this->_aData, $data);
		foreach ($data as $value) {
			$new_data = $this->_dataHandle($value);
			$field = implode(',', array_keys($new_data));
			$tmp[$field][] = '(' . implode(',', array_values($new_data)) . ')';
		}
		$sql = '';
		foreach ($tmp as $k => $v) {
			$sql .= 'INSERT INTO ' . $this->_sDbName . '.' . $this->_sTblName .
					'(' . $k . ') VALUES ' . implode(',', $v) . ';';
		}
		$rs = $this->_runSQL($sql);
		if ($rs) {
			$insert_id = insert_id($this->_oDb);
			return $insert_id;
		}
		return FALSE;
	}

	/**
	 * 查询 条件
	 * 
	 * @param string | array $where 条件
	 *   ( 支持 "id=1" , array('id'=>1), array(array('id'=>1,'title'=>'regel'),'or') 三种格式 )
	 * @return object 
	 */
	protected function where($where) {
		if (empty($where)) {
			return $this;
		}
		if (is_array($where)) {  // 条件为数组
			$relate = 'AND';  // 关联
			if (isset($where[0]) && is_array($where[0])) {
				(isset($where[1]) && in_array($where[1], array('AND', 'OR', 'and', 'or'))) && $relate = strtoupper($where[1]);
				$where = $where[0];
			}
			$where_str = $this->_dataHandle($where, TRUE, $relate);
			if ($where_str) {
				$this->_sWhere = ' WHERE ' . $where_str;
			}
			return $this;
		}
		$where = where_filter($where);
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
	protected function update($data, $where = null) {
		if (empty($data) || !is_array($data)) {
			return 0;
		}
		$data = $this->_dataHandle($data, TRUE);  // 数据处理
		if (empty($data)) {
			return 0;
		}
		if (!is_null($where)) {
			$this->where($where);
		}
		$sql = 'UPDATE ' . $this->_sDbName . '.' . $this->_sTblName . ' SET ' . $data . $this->_sWhere;
		$rs = $this->_runSQL($sql);
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
	protected function delect($where = null) {
		if (!is_null($where)) {
			// 传入条件
			$this->where($where);
		}
		$where = $this->_sWhere;
		if (empty($where)) {
			return 0;
		}
		$sql = 'DELETE FROM ' . $this->_sDbName . '.' . $this->_sTblName . $where;
		$rs = $this->_runSQL($sql);
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
	private function _crease($field, $step = 1, $opt = '+') {
		$step = is_numeric($step) ? abs($step) : intval($step);
		if (!preg_match('/^(\w|\.|`)+$/', $field)) {
			return FALSE;
		}
		$field = strpos($field, '`') === FALSE ? '`' . $field . '`' : $field;
		$sql = 'UPDATE ' . $this->_sDbName . '.' . $this->_sTblName . ' SET ' . $field . '=' . $field . $opt . $step . $this->_sWhere;
		$rs = $this->_runSQL($sql);
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
	protected function increase($field, $step = 1) {
		return $this->_crease($field, $step, '+');
	}

	/**
	 * 字段 减值
	 * 
	 * @param string $field 字段名
	 * @param int $step 步入值
	 * @return int 
	 */
	protected function decrease($field, $step = 1) {
		return $this->_crease($field, $step, '-');
	}

	/**
	 * 查询的字段
	 * 
	 * @param string | array $field 字段
	 * @return object 
	 */
	protected function field($field) {
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
		}
		if (is_string($field)) {
			if (preg_match('/^(\w|\.|`|,)+$/', $field)) {
				// 去除空格
				$field = preg_replace('/(;|\s)+/', '', $field);
				$this->_sFields = (strpos($field, '.') === FALSE) ? ((strpos($field, '`') === FALSE) ? '`' . $field . '`' : $field) : $field;
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
	protected function order($order) {
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
	protected function find($where = null, $field = null, $order = null) {
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
		$sql = 'SELECT ' . $this->_sFields . ' FROM ' . $this->_sDbName . '.' . $this->_sTblName . $this->_sAlias . $this->_sJoin .
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
	protected function limit($limit) {
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
	protected function select($where = null, $field = null, $order = null, $limit = null) {
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
		$sql = 'SELECT ' . $this->_sFields . ' FROM ' . $this->_sDbName . '.' . $this->_sTblName . $this->_sAlias . $this->_sJoin .
				$this->_sWhere . $this->_sOrder . $this->_sLimit;
		$data = get_data($sql, $this->_oDb);
		return $data;
	}

	/**
	 * 获取 最后一条 SQL 语句
	 * 
	 * @return string
	 */
	protected function _sql() {
		return isset($GLOBALS['db']['last_sql']) ? $GLOBALS['db']['last_sql'] : '';
	}

	/**
	 * 表 别名
	 * 
	 * @param string $name 
	 * @return object
	 */
	protected function _as($name) {
		if (preg_match('/^(\w|`)+$/', $name)) {
			$this->_sAlias = (strpos($name, '`') === FALSE) ? ' AS `' . $name . '` ' : ' AS ' . $name . ' ';
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
	protected function join() {
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
	 */
	protected function union() {
		// TODO:: 有空再来完善
		return $this;
	}

	/**
	 * SQL 查询
	 * 
	 * @param string $sql 
	 * @return mixed 
	 */
	protected function query($sql) {
		if (empty($sql)) {
			return FALSE;
		}
		$data = get_data($sql, $this->_oDb);
		if (count($data) == 1) {
			return $data[0];
		}
		return $data;
	}

}
