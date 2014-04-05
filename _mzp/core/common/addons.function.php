<?php

/**
 * 附加方法库
 * 
 * @author regel chen<regelhh@gmail.com>
 * @since 2014-3-23
 * @version 1.0 Beta
 */
defined('INI') or die('--AFunc--');

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
 * 获取精确时间
 * 
 * @return float
 */
function get_time() {
	return number_format(microtime(TRUE), 6, '.', '');
}

/**
 * 是否 ajax 请求
 * 
 * @return boolean -true 是
 */
function is_ajax_request() {
	$headers = apache_request_headers();
	return (isset($headers['X-Requested-With']) && ( $headers['X-Requested-With'] == 'XMLHttpRequest' )) ||
			(isset($headers['x-requested-with']) && ($headers['x-requested-with'] == 'XMLHttpRequest' ));
}

if (!function_exists('apache_request_headers')) {

	/**
	 * 获取全部 HTTP 请求头信息
	 * 
	 * @return array
	 */
	function apache_request_headers() {
		foreach ($_SERVER as $key => $value) {
			if (substr($key, 0, 5) == "HTTP_") {
				$key = str_replace(" ", "-", ucwords(strtolower(str_replace("_", " ", substr($key, 5)))));
				$out[$key] = $value;
			} else {
				$out[$key] = $value;
			}
		}
		return $out;
	}
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
 * 判断是否 手机请求
 * 
 * @return boolean -true 是
 */
function is_mobile_request() {
	if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
		return TRUE;
	}
	if (isset($_SERVER['HTTP_PROFILE'])) {
		return TRUE;
	}
	if (strpos(strtolower($_SERVER['ALL_HTTP']), 'operamini') !== false) {
		return TRUE;
	}
	if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows phone') !== false) {
		return TRUE;
	}
	if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows') !== false) {
		return FALSE;
	}
	if ((isset($_SERVER['HTTP_ACCEPT'])) and ( strpos(strtolower($_SERVER['HTTP_ACCEPT']), 'application/vnd.wap.xhtml+xml') !== false)) {
		return TRUE;
	}
	if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|iphone|ipad|ipod|android|xoom)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
		return TRUE;
	}
	$mobile_agents = array(
		'w3c ', 'acs-', 'alav', 'alca', 'amoi', 'audi', 'avan', 'benq', 'bird', 'blac',
		'blaz', 'brew', 'cell', 'cldc', 'cmd-', 'dang', 'doco', 'eric', 'hipt', 'inno',
		'ipaq', 'java', 'jigs', 'kddi', 'keji', 'leno', 'lg-c', 'lg-d', 'lg-g', 'lge-',
		'maui', 'maxo', 'midp', 'mits', 'mmef', 'mobi', 'mot-', 'moto', 'mwbp', 'nec-',
		'newt', 'noki', 'oper', 'palm', 'pana', 'pant', 'phil', 'play', 'port', 'prox',
		'qwap', 'sage', 'sams', 'sany', 'sch-', 'sec-', 'send', 'seri', 'sgh-', 'shar',
		'sie-', 'siem', 'smal', 'smar', 'sony', 'sph-', 'symb', 't-mo', 'teli', 'tim-',
		'tosh', 'tsm-', 'upg1', 'upsi', 'vk-v', 'voda', 'wap-', 'wapa', 'wapi', 'wapp',
		'wapr', 'webc', 'winw', 'winw', 'xda', 'xda-'
	);
	if (in_array(strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4)), $mobile_agents)) {
		return TRUE;
	}
	return FALSE;
}

/**
 * 循环创建目录
 * 
 * @param string $dir
 * @param int|null $mode 文件夹的权限
 * @return boolean 
 */
function mk_dir($dir, $mode = 0777) {
	if (is_dir($dir) || @mkdir($dir, $mode)) {
		return TRUE;
	}
	if (!mk_dir(dirname($dir), $mode)) {
		return FALSE;
	}
	return @mkdir($dir, $mode);
}

/**
 * 系统加密方法 ( XOR 方法)
 * 
 * @param string $string 要加密的字符串
 * @param string $key  加密密钥
 * @return string
 */
function xor_encrypt($string, $key = '') {
	$string = base64_encode(trim($string));
	$key = md5(empty($key) ? '_@regel#):say^hooo!!(' : $key);
	$str_len = strlen($string);
	$key_len = strlen($key);
	for ($i = 0; $i < $str_len; $i++) {
		for ($j = 0; $j < $key_len; $j++) {
			$string[$i] = $string[$i] ^ $key[$j];
		}
	}
	return base64_encode($string);
}

/**
 * 系统解密方法 ( XOR 方法)
 * 
 * @param  string $string 要解密的字符串 （必须是 xor_encrypt 方法加密的字符串）
 * @param  string $key  加密密钥
 * @return string
 */
function xor_decrypt($string, $key = '') {
	$string = base64_decode(trim($string));
	if (empty($string)) {
		return FALSE;
	}
	$key = md5(empty($key) ? '_@regel#):say^hooo!!(' : $key);
	$str_len = strlen($string);
	$key_len = strlen($key);
	for ($i = 0; $i < $str_len; $i++) {
		for ($j = 0; $j < $key_len; $j++) {
			$string[$i] = $key[$j] ^ $string[$i];
		}
	}
	return base64_decode($string);
}
