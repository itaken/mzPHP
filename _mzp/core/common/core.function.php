<?php

/**
 * 核心方法库
 * 
 * @author regel chen<regelhh@gmail.com>
 * @since 2014-3-21
 * @version 1.0 Beta
 */
defined('INI') or die('--CFunc--');

/**
 * @var string 过滤规则, 来自 360 safe
 */
$getfilter = "'|(and|or)\\b.+?(>|<|=|in|like)|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";
$postfilter = "\\b(and|or)\\b.{1,6}?(=|>|<|\\bin\\b|\\blike\\b)|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";
$cookiefilter = "\\b(and|or)\\b.{1,6}?(=|>|<|\\bin\\b|\\blike\\b)|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";

/**
 * 输入过滤, 修改自 360safe_php
 * 
 * @param mixed $value 需要过滤的数据
 * @param string $filt_req 过滤规则
 * @return void
 */
function stop_attack($value, $filt_req) {
	if (empty($value) || empty($filt_req)) {
		return FALSE;
	}
	$value = is_array($value) ? implode($value) : $value;
	// 匹配过滤
	if (preg_match("/" . $filt_req . "/is", $value)) {
		exit('Illegal operation! ');
	}
}

/**
 * 获取 配置内容
 * 
 * @param string $str 配置名称
 * @return mixed 配置值
 */
function c($str) {
	return isset($GLOBALS['config'][$str]) ? $GLOBALS['config'][$str] : null;
}

/**
 * 获取 gloab 变量
 * 
 * @param string $name  变量名称
 * @return mixed
 */
function g($name) {
	return isset($GLOBALS[$name]) ? $GLOBALS[$name] : null;
}

/**
 * 生成 URL
 * 
 * @param string $path url路径
 * @param array $param url参数
 * @return string 
 */
function u($path = null, $param = array()) {
	if (is_null($path)) {
		// 当前 URL
		return SITE_URL . $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'];
	}
	$path = explode('/', $path);
	if (!OPEN_SLINK) {
		$url = SITE_URL . '/?c=' . $path[0] . '&a=' . $path[1];
		return empty($param) ? $url : $url . '&' . http_build_query($param);
	}
	$param_str = '';
	if (!empty($param) && is_array($param)) {
		foreach ($param as $key => $value) {
			$param_str = '-' . xor_encrypt($key . '|' . $value);
		}
	}
	return SITE_URL . '/' . $path[0] . '-' . $path[1] . $param_str . '.html';
}

/**
 * 引入模板文件
 * 
 * @param string $path 模板路径
 * @param boolean $data 是否加载数据
 * @return string 模板内容
 */
function tpl($path, $data = FALSE) {
	$layout_file = MROOT . 'view/' . $path . '.tpl.html';  // 完整路径
	if (file_exists($layout_file)) {
		$data && extract($GLOBALS['__assign']);
		require( $layout_file );  // 引入文件
	} elseif (APP_DEBUG) {
		// 文件不存在
		exit('--"' . $path . '.tpl.html" ERROR: ' . 'FILE Not Found!!');
	}
}

/**
 * 获取 图片
 * 
 * @param string $name 图片名称
 * @param string $dir 附加目录
 * @return string
 */
function img($name, $dir = NULL) {
	$name = strtolower(trim($name));
	if (empty($name)) {
		return SITE_URL . '/static/imgs/public/default.jpg';
	}
	$param = MROOT . 'static/imgs/' . (empty($dir) ? $name : trim($dir) . '/' . $name) . '.*';
	$files = glob($param, GLOB_NOSORT);  // 匹配
	if (empty($files)) {
		return SITE_URL . '/static/imgs/public/default.jpg';
	}
	return str_replace(MROOT, SITE_URL . '/', $files[0]);
}

/**
 * 数据分发 ( 任意个参数 )
 * 
 * @args mixed 不固定参数
 *  ( 示例: array('name'=>'regel') | 'name','regel' | 'name','regel','msg','test' )
 * @return void
 */
function assign() {
	$num = func_num_args();  // 参数个数
	$data = array();
	if ($num == 1) {
		$data = func_get_arg(0);   // 第一个参数
		$data = is_array($data) ? $data : array($data => '');
	} elseif ($num == 2) {
		$data = array(func_get_arg(0) => func_get_arg(1));
	} else {
		$args = func_get_args();
		$i = 0;
		foreach ($args as $v) {
			if ($i % 2 == 0) {
				// 判断是否是数字或字符串
				if (is_numeric($v) || is_string($v)) {
					$value = isset($args[$i + 1]) ? $args[$i + 1] : null;
					$data[$v] = $value;
				}
			}
			$i ++;
		}
	}
	$GLOBALS['__assign'] = $data;
}

/**
 * 回溯 解析
 * 
 * @return array 
 */
function tarce() {
	$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);  // 获取回溯
	$back = array();
	foreach ($trace as $value) {
		$func = $value['function'];
		if (strcmp($func, __FUNCTION__) == 0) {
			continue;
		}
		if (isset($value['file'])) {
			if (strpos($value['file'], 'mzp.ini.php') !== FALSE) {
				break;
			}
		}
		$cls = isset($value['class']) ? $value['class'] : null;
		$back[] = array(
			'function' => $func,
			'class' => $cls,
		);
	}
	return $back;
}

/**
 * 模板输出 类似ThinkPHP的display
 * 
 * @args mixed 内容数据 | 模板子目录/模板名称 
 *   ( 示例: 'info.tpl.html' | array('meta_title'=>'regel') | array('msg'=>'Just Test!'),'info.tpl.html' | 'public','info' | array(...),'public','info' )
 * @return void 
 */
function render() {
	$num = func_num_args();  // 参数个数
	$data = array(
		// SEO
		'meta_title' => $GLOBALS['meta']['default_title'],
		'meta_keywords' => $GLOBALS['meta']['default_keywords'],
		'meta_description' => $GLOBALS['meta']['default_description']
	);
	if ($num == 0) {
		// 数据 使用assign数据
		isset($GLOBALS['__assign']) && ($data = array_merge($data, $GLOBALS['__assign']));
	}
	if ($num == 1) {
		$info = func_get_arg(0);
		if (is_array($info)) {
			// 传递一个参数, 数组则判定为传递数据
			$data = array_merge($data, $info);
		}
		if (is_string($info)) {
			// 字符串,则判定为传递模板文件
			$layout_file = MROOT . 'view/' . $info;
			if (file_exists($layout_file)) {
				isset($GLOBALS['__assign']) && ($data = $GLOBALS['__assign']);
				is_array($data) && extract($data);
				$GLOBALS['__assign'] = $data;
				require( $layout_file );
				exit;
			}
			APP_DEBUG && exit('"' . $info . '" ERROR: ' . 'FILE Not Found!!');
		}
	}
	if ($num > 1) {
		$args = func_get_args();  // 获取参数内容
		$tpl = array();
		foreach ($args as $arg) {
			if (is_array($arg)) {
				$data = array_merge($data, $arg);
				continue;
			}
			is_string($arg) && ($tpl[] = $arg);
		}
		if (count($tpl) == 1) {
			$layout_file = MROOT . 'view/' . $tpl[0];
			if (file_exists($layout_file)) {
				is_array($data) && extract($data);
				$GLOBALS['__assign'] = $data;
				require( $layout_file );
				exit;
			}
			APP_DEBUG && exit('"' . $tpl[0] . '" ERROR: ' . 'FILE Not Found!!');
		} else {
			$layout = $tpl[0];
			$sharp = $tpl[1];
		}
	}
	// 没有模板文件
	if (empty($layout) || empty($sharp)) {
		$trace = tarce();
		$layout = empty($layout) ? strtolower(str_replace('Controller', '', $trace[1]['class'])) : $layout;
		$sharp = empty($sharp) ? $trace[1]['function'] : $sharp;
	}
	$GLOBALS['c'] = $layout;
	$GLOBALS['a'] = $sharp;
	$tpl_file = $layout . c('tpl_file_depr') . $sharp . '.tpl.html';  // 模板文件
	$layout_file = MROOT . 'view/' . $tpl_file;  // 完整路径
	if (file_exists($layout_file)) {
		is_array($data) && extract($data);  // 如果是数组,则导入到当前的符号表中
		$GLOBALS['__assign'] = $data;
		require( $layout_file );
		exit;
	}
	APP_DEBUG && exit('"' . $tpl_file . '" ERROR: ' . 'FILE Not Found!!');
}

/**
 * 信息页
 * 
 * @param string $info 信息内容
 * @param string $title 信息标题
 * @param string $meta_title 标题栏标题
 * @return void
 */
function info_page($info, $title = '系统消息', $meta_title = '系统提示') {
	$data['title'] = $title;
	$data['info'] = $info;
	$data['meta_title'] = $meta_title;
	render($data, 'public/info.tpl.html');
}

/**
 * ajax 返回
 * 
 * @param string $data 返回的数据
 * @return void 
 */
function ajax_echo($data) {
	if (!headers_sent()) {
		header("Content-Type:text/html;charset=utf-8");
		header("Cache-Control: no-cache, must-revalidate");
		header("Pragma: no-cache");
	}
	exit($data);
}

/**
 * json 返回
 * 
 * @param mixed $data 返回的数据
 * @param string $info 提示信息
 * @param string $status 状态
 * @return void 
 */
function json_return($data, $info = '', $status = '') {
	$return_arr = array(
		'data' => $data,
		'info' => $info,
		'status' => $status
	);
	header('Content-Type:application/json; charset=utf-8');  // 定义返回格式
	exit(preg_replace('#\":\s*(null|false)#iUs', '":""', json_encode($return_arr)));
}
