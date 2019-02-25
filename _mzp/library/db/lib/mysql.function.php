<?php

/**
 * 获取数据库配置
 *
 * @param string $name 配置项名称
 * @return mixed 值
 */
function DBConfig($name = null)
{
    $db_config = $GLOBALS['db']['config'];
    if (empty($db_config)) {
        $config = include(DB_CLS_ROOT . 'db.config.php');
        $GLOBALS['db']['config'] = $config;
    }
    if (is_null($name)) {
        return $db_config;
    }
    return isset($db_config[$name]) ? $db_config[$name] : null;
}

/**
 * 数据库连接
 *
 * @param string $host
 * @param string $port
 * @param string $user
 * @param string $password
 * @param string $db_name
 * @param string $chart 编码格式
 * @return resource
 */
function db($host = null, $port = null, $user = null, $password = null, $db_name = null, $chart = null)
{
    // 数据库配置
    $host = empty($host) ? DBConfig('DB_HOST') : $host;
    $port = empty($port) ? DBConfig('DB_PORT') : intval($port);
    $user = empty($user) ? DBConfig('DB_USER') : $user;
    $password = is_null($password) ? DBConfig('DB_PSW') : $password;
    $db_name = empty($db_name) ? DBConfig('DB_NAME') : $db_name;
    (empty($host) || empty($port) || empty($user)) && die('ERROR: DB config error!');
    $db_key = 'mysqli-' . md5($host . '-' . $port . '-' . $user . '-' . $password . '-' . $db_name);
    $GLOBALS['db']['key'] = $db_key;
    if (isset($GLOBALS[$db_key])) {
        $mysql = $GLOBALS[$db_key];
        if (mysql_ping($mysql)) {  // 判断 连接 是否 alive
            return $mysql;
        }
    }
    $mysql = mysql_connect($host . ':' . $port, $user, $password, true); // 初始化 mysqli
    if (mysql_errno($mysql)) {
        $msg = APP_DEBUG ? ' < ' . mysql_error($mysql) . ' >' : '';
        exit('ERROR: Connect failed!' . $msg);
    }
    mysql_select_db($db_name, $mysql);
    $chart = empty($chart) ? DBConfig('CHART_SET') : $chart;  // 编码格式
    mysql_query('SET NAMES "' . $chart . '"', $mysql); // 设置编码格式
    $GLOBALS[$db_key] = $mysql;
    return $mysql;
}

/**
 * 运行 SQL 语句
 *
 * @param string $sql
 * @param object $db
 * @return mixed
 */
function run_sql($sql, $db = null)
{
    $db = is_null($db) ? db() : $db;
    $GLOBALS['db']['last_sql'] = $sql;
    $result = mysql_query($sql, $db);
    if (mysql_errno($db)) {
        // 发生错误, 记录错误信息
        $GLOBALS['db']['last_error'] = mysql_error($db);
        return false;
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
function get_line($sql, $db = null)
{
    $db = is_null($db) ? db() : $db;
    $result = run_sql($sql, $db);
    if (empty($result)) {
        return array();
    }
    $row = mysql_fetch_assoc($result);
    mysql_free_result($result);  // 释放资源
    return $row;
}

/**
 * 获取 数据
 *
 * @param string $sql SQL语句
 * @param object
 * @return array
 */
function get_data($sql, $db = null)
{
    $data = array();
    $db = is_null($db) ? db() : $db;
    $result = run_sql($sql, $db);
    if (empty($result)) {
        return $data;
    }
    while ($row = mysql_fetch_array($result, MYSQLI_ASSOC)) {
        $data[] = $row;
    }
    mysql_free_result($result);  // 释放资源
    return $data;
}

/**
 * 获取 最后插入ID号
 *
 * @param object $db 数据库对象
 * @return int
 */
function insert_id($db)
{
    return mysql_insert_id($db);
}

/**
 * 获取 操作影响行数
 *
 * @param object $db
 * @return int
 */
function affected_rows($db)
{
    return mysql_affected_rows($db);
}

/**
 * 关闭数据库
 *
 * @param object $db
 * @return void
 */
function close_db($db = null)
{
    if (is_callable($db)) {
        mysql_close($db);
    }
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
function escape_string($str, $db = null)
{
    $db = is_null($db) ? db() : $db;
    return mysql_real_escape_string($str, $db);
    //	return mysqli_real_escape_string($db, $str);
}
