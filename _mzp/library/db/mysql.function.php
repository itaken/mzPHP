<?php

/**
 * 数据库操作方法库
 * 
 * @author regel chen<regelhh@gmail.com>
 * @since 2014-3-22
 * @version 1.0 RC1
 */
defined('INI') or die('--MysqliFunc--');

/**
 * 数据库连接
 * 
 * @param string $host
 * @param string $port
 * @param string $user
 * @param string $password
 * @param string $db_name
 * @return resource
 */
function db($host = null, $port = null, $user = null, $password = null, $db_name = null) {
	// 数据库配置
	$host = empty($host) ? c('DB_HOST') : $host;
	$port = empty($port) ? c('DB_PORT') : $port;
	$user = empty($user) ? c('DB_USER') : $user;
	$password = empty($password) ? c('DB_PSW') : $password;
	$db_name = empty($db_name) ? c('DB_NAME') : $db_name;
	(empty($host) || empty($port) || empty($user)) && die('--ERROR: DB config error!');
	$db_key = 'mysqli-' . md5($host . '-' . $port . '-' . $user . '-' . $password . '-' . $db_name);
	$GLOBALS['db']['key'] = $db_key;  // 存储数据库KEY
	if (isset($GLOBALS[$db_key])) {
		$mysqli = $GLOBALS[$db_key];
		if ($mysqli->ping()) {  // 判断 连接 是否 alive
//			echo '---alive---';
			return $mysqli;
		}
	}
	$mysqli = @new mysqli($host, $user, $password, $db_name, intval($port)); // 初始化 mysqli
	if ($mysqli->connect_errno) {
		$msg = APP_DEBUG ? ' < ' . $mysqli->connect_error . ' >' : '';
		exit('--ERROR: Connect failed!' . $msg);
	}
	$mysqli->set_charset(c('CHART_SET'));  // 设置编码格式
//	$mysqli->select_db($db_name);  // 选择数据库
	$GLOBALS[$db_key] = $mysqli;
	return $mysqli;
}

/**
 * 运行 SQL 语句
 * 
 * @param string $sql
 * @param object $db 
 * @return mixed
 */
function run_sql($sql, $db = NULL) {
	$db = is_null($db) ? db() : $db;
	$GLOBALS['db']['last_sql'] = $sql;
	$result = $db->query($sql);
	if ($db->errno) {
		// 发生错误, 记录错误信息
		$GLOBALS['db']['last_error'] = $db->error;
		return FALSE;
	}
	return $result;
}

/**
 * 获取 单行数据
 * 
 * @param string $sql SQL语句
 * @param object 
 * @return array 
 */
function get_line($sql, $db = NULL) {
	$db = is_null($db) ? db() : $db;
	$result = run_sql($sql, $db);
	if (empty($result)) {
		return array();
	}
	$row = $result->fetch_assoc();
	$result->free();  // 释放资源
	return $row;
}

/**
 * 获取 数据
 * 
 * @param string $sql SQL语句
 * @param object 
 * @return array 
 */
function get_data($sql, $db = NULL) {
	$data = array();
	$db = is_null($db) ? db() : $db;
	$result = run_sql($sql, $db);
	if (empty($result)) {
		return $data;
	}
	while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
		$data[] = $row;
	}
	$result->free();  // 释放资源
	return $data;
}

/**
 * 获取 最后插入ID号
 * 
 * @param object $db 数据库对象
 * @return int 
 */
function insert_id($db) {
	return $db->insert_id;
}

/**
 * 获取 操作影响行数
 * 
 * @param object $db 
 * @return int 
 */
function affected_rows($db) {
	return $db->affected_rows;
}

/**
 * 关闭数据库 
 * 
 * @param object $db
 * @return void
 */
function close_db($db = NULL) {
	$db = is_null($db) ? db() : $db;
	$db->close();
	$db_key = $GLOBALS['db']['key'];
	unset($GLOBALS[$db_key]);
	unset($GLOBALS['db']);
}

/**
 * 转义 特殊字符
 * 
 * @param string $str SQL
 * @param object $db 数据库对象
 * @return string
 */
function escape_string($str, $db = NULL) {
	$db = is_null($db) ? db() : $db;
	return $db->escape_string($str);
//	return mysqli_real_escape_string($db, $str);
}

/**
 * 条件字符串 过滤
 * 
 * @param string $where_str 
 * @return string 
 */
function where_filter($where_str) {
	if (empty($where_str)) {
		return FALSE;
	}
	// 语句防注入
	if (preg_match('/[\'|"]\s*;\s*[\'|"]?\s*(update|insert|delete|drop|select)/i', $where_str)) {
		return FALSE;
	}
	// 1=1 / 1=2 防注入
	if (preg_match('/(1\s*=\s*1)|(1\s*=\s*2)|(\d\s*=\s*\d)/', $where_str)) {
		return FALSE;
	}
	return $where_str;
}
