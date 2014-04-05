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
 * 获取 配置内容
 * 
 * @param string $str 配置名称
 * @return mixed 配置值
 */
function c($str) {
	$c_conf_file = CROOT . 'config' . DS . 'core.config.php';  // 核心配置
	$c_conf = file_exists($c_conf_file) ? include($c_conf_file) : array();
	// 引入项目配置
	$app_conf_file = MROOT . 'config' . DS . 'conf.inc';
	$app_conf = file_exists($app_conf_file) ? include($app_conf_file) : array();
	$conf = array_merge($c_conf, $app_conf);
	return isset($conf[$str]) ? $conf[$str] : null;
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
		$url = SITE_URL . '?c=' . $path[0] . '&a=' . $path[1];
		return empty($param) ? $url : $url . '&' . http_build_query($param);
	}
	$param_str = '';
	if (!empty($param) && is_array($param)) {
		foreach ($param as $key => $value) {
			$param_str = '/' . $key . '/' . urlencode($value);
		}
	}
	return SITE_URL . $path[0] . '/' . $path[1] . $param_str . '.html';
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
 * 模板输出 类似ThinkPHP的display
 * 
 * @args mixed 内容数据 | 模板子目录/模板名称 
 *   ( 示例: 'info.tpl.html' | array('meta_title'=>'regel') | array('msg'=>'Just Test!'),'public/info.tpl.html' | 'public','info' | array(...),'public','info' )
 *   ( 数据必须使用array类型, 模板必须使用string类型 , 更改布局使用 L:public/extend | L:public/extend.tpl.html 格式)
 * @return void 
 */
function render() {
	$tpl_suffix = '.tpl.html';  // 模板后缀
	$layout_file = 'public/extend.tpl.html';
	$tpl = $___CSS___ = null;  // 错误信息
	$tpl_depr = c('TPL_FILE_DEPR');  // 分割
	$data = array(
		// SEO
		'meta_title' => c('DEFAULT_META_TITLE'),
		'meta_keywords' => c('DEFAULT_META_KEYWORDS'),
		'meta_description' => c('DEFAULT_META_DESCRIPTION')
	);
	if (isset($GLOBALS['__assign'])) {  // 已经assign数据,则叠加
		$data = array_merge($data, $GLOBALS['__assign']);
	}
	$args = func_get_args();  // 获取参数
	foreach ($args as $arg) {
		if (empty($arg)) {
			continue;
		}
		if (is_array($arg)) {
			// 数据 使用assign数据
			$data = array_merge($data, $arg);
		}
		if (is_string($arg)) {
			if (stripos($arg, 'l:') === 0) {
				$layout_file = substr($arg, 2);
				$layout_file = strpos($layout_file, $tpl_suffix) ? $layout_file : $layout_file . $tpl_suffix;
				continue;
			}
			$tpl .= $arg . $tpl_depr;
		}
	}
	$exc = 'public' . $tpl_depr . 'info';  // 例外
	$exception = strpos($tpl, $exc) !== FALSE ? TRUE : FALSE;  // 是否例外
	if (empty($tpl) || $exception) {
		// 没有模板文件, 使用回溯信息
		$trace = tarce();
		foreach ($trace as $value) {
			if (empty($value['class'])) {
				continue;
			}
			$layout = strtolower(str_replace('Controller', '', $value['class']));
			$sharp = $value['function'];
			$tpl = $exception ? $exc . $tpl_suffix : $layout . $tpl_depr . $sharp . $tpl_suffix;  // 回溯组装模板
			break;
		}
	} else {
		$tpl_arr = explode($tpl_depr, $tpl);
		$layout = $tpl_arr[0];
		$sharp = $tpl_arr[1];
		$tpl = strpos($tpl, $tpl_suffix) ? trim($tpl, $tpl_depr) : trim($tpl, $tpl_depr) . $tpl_suffix;
	}
	$GLOBALS['c'] = $layout;
	$GLOBALS['a'] = $sharp;
	// 载入模板
	$tpl_file = MROOT . 'view/' . $tpl;  // 完整路径
	if (!file_exists($tpl_file)) {
		APP_DEBUG && exit('"' . $tpl . '" ERROR: FILE Not Found!!');
		@call_user_func(array(new CoreController(), '_empty'));
	}
	$layout_tpl = MROOT . 'view/' . $layout_file;  // 布局文件
	if (!file_exists($layout_tpl)) {
		APP_DEBUG && exit('"' . $layout_file . '" ERROR: FILE Not Found!!');
		@call_user_func(array(new CoreController(), '_empty'));
	}
	is_array($data) && extract($data, EXTR_OVERWRITE);  // 如果是数组,则导入到当前的符号表中
	// 页面缓存
	$_css_ = array();
	ob_start();
	ob_implicit_flush(FALSE);  // 打开/关闭绝对刷送
	require( $tpl_file );  // 引入模板
	$___CONTENT___ = ob_get_clean();  // 获取并清空缓存
	if (is_array($_css_)) {
		foreach ($_css_ as $v) {
			$file = MROOT . 'static/css/' . $v;
			file_exists($file) && $___CSS___ .= '<link href="' . str_replace(MROOT, SITE_URL, $file) . '" type="text/css" rel="stylesheet">' . PHP_EOL;
		}
	}
	$GLOBALS['__assign'] = $data;
	require( $layout_tpl );
	exit;
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
	render($data, 'public/info');
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
