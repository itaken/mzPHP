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
		exit('Illegal operation! --->' . $value);
	}
}

/**
 * 是否 缓存
 * 
 * @return boolean -true 缓存 -false 不缓存
 */
function is_cache() {
	return isset($_GET['_cache']) ? TRUE : FALSE;
}

/**
 * 是否 DEBUG
 * 
 * @return boolean -true debug
 */
function is_debug() {
	return isset($_GET['_debug']) ? TRUE : FALSE;
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

/**
 * 系统非常规 MD5加密方法
 * 
 * @param  string $str 要加密的字符串
 * @param string $key 加密密钥
 * @return string 
 */
function md5_password($str, $key = 'mzPHP') {
	return '' == $str ? '' : md5(sha1($str) . md5($str) . $key);
}

/**
 * 获取客户端 IP
 * 
 * @return string
 */
function get_ip() {
	if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
		return getenv('HTTP_CLIENT_IP');
	} elseif (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
		return getenv('HTTP_X_FORWARDED_FOR');
	} elseif (getenv('HTTP_CDN_SRC_IP') && strcasecmp(getenv('HTTP_CDN_SRC_IP'), 'unknown')) {
		return getenv('HTTP_CDN_SRC_IP');
	} elseif (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
		return getenv('REMOTE_ADDR');
	} elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
		return filter_input(INPUT_SERVER, 'REMOTE_ADDR');
	} else {
		return '';
	}
}

/**
 * 重定向
 * 
 * @param string $url 
 * @param string $msg
 * @param int $time 
 * @return void
 */
function jumps($url, $msg = '', $time = 0) {
	$url = str_replace(array("\n", "\r"), '', $url);
	$url = empty($url) ? u('index/index') : $url;
	if (empty($msg)) {
		$msg = "系统将在{$time}秒之后自动跳转到{$url}！";
	}
	if (!headers_sent()) {
		// redirect
		if (0 === $time) {
			header('Location: ' . $url);
		} else {
			header("refresh:{$time};url={$url}");
			echo($msg);
		}
		exit();
	} else {
		$str = "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
		if ($time != 0) {
			$str .= $msg;
		}
		exit($str);
	}
}

/**
 * 是否 GET 提交
 * 
 * @return boolean - true 是
 */
function is_get(){
	return (filter_input(INPUT_SERVER, 'REQUEST_METHOD') == 'GET') ? TRUE : FALSE;
}

/**
 * 是否 POST 提交
 * 
 * @return boolean - true 是
 */
function is_post(){
	return (filter_input(INPUT_SERVER, 'REQUEST_METHOD') == 'POST') ? TRUE : FALSE;
}