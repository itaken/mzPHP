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
 * 是否 缓存
 * 
 * @return boolean -true 缓存 -false 不缓存
 */
function is_cache() {
	return isset($_GET['_no_cache']) ? FALSE : TRUE;
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
 * 时间处理
 *
 * @param int $time
 * @return string
 */
function get_time_diff($time) {
	$min = 60;
	$hour = $min * 60;
	$day = $hour * 24;
	$diff = time() - $time;

	switch ($diff) {
		case ($diff < 5):
			$str = '刚刚';
			break;
		case ($diff < $min):
			$str = $diff . ' 秒前';
			break;
		case ($diff < $hour):
			$str = floor($diff / $min) . ' 分钟前';
			break;
		case ($diff < $day):
			$str = floor($diff / $hour) . ' 小时前';
			break;
		case ($diff >= $day):
			$str = date('m-d H:i', $time);
			break;
		default:
			$str = '公元前';
	}
	return $str;
}

/**
 * 获取 时间区间
 * 
 * @param string $interval 区间 ( TD今日 YD昨日 W本周 LW上周 M本月 LM上月 Q本季 LQ上季 Y今年 LY去年 )
 * @param boolean $format 格式化 ( 例如: 2014-3-8 01:56:25 )
 * @return array | false 
 */
function get_time_interval($interval, $format = TRUE) {
	$start = $end = null;
	switch (strtoupper($interval)) {
		case 'TD':  // 本日
			$td = date('Y-m-d');
			$start = $td . ' 00:00:00';
			$end = $td . ' 23:59:59';
//			$start = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
//			$end = mktime(23, 59, 59, date('m'), date('d'), date('Y'));
			break;
		case 'YD':  // 昨日
			$yd = date('Y-m-d', strtotime('-1 day'));
			$start = $yd . ' 00:00:00';
			$end = $yd . ' 23:59:59';
			break;
		case 'W': // 本周
			$w = date('w');  // 本周的第几天
			$start = date('Y-m-d', strtotime(-$w . ' day')) . ' 00:00:00';
			$end = date('Y-m-d', strtotime((6 - $w) . ' day')) . ' 23:59:59';
			break;
		case 'LW':  // 上周
			$w = date('w') + 7;
			$start = date('Y-m-d', strtotime(-$w . ' day')) . ' 00:00:00';
			$end = date('Y-m-d', strtotime((6 - $w) . ' day')) . ' 23:59:59';
			break;
		case 'M': // 本月
			$m = date('Y-m');
			$start = $m . '-01 00:00:00';
			$end = $m . '-' . date('t') . ' 23:59:59';
			break;
		case 'LM':  // 上月
			$j = date('j');  // 月份第几天
			$start = date('Y-m', strtotime('-1 month')) . '-01 00:00:00';
			$end = date('Y-m-d', strtotime(-$j . ' day')) . ' 23:59:59';
			break;
		case 'Q':  // 本季度
			$n = date('n');  // 当前月份
			$qm = date('Y-m', strtotime(+($n % 3) . ' month'));  // 季度最后一月
			$start = date('Y') . '-' . (ceil($n / 3) * 3 - 3 + 1) . '-01 00:00:00';
			$end = $qm . '-' . date('t', strtotime($qm)) . ' 23:59:59';
//			$start = date('Y-m-d H:i:s', mktime(0, 0, 0, $season * 3 - 3 + 1, 1, date('Y')));
//			$end = date('Y-m-d H:i:s', mktime(23, 59, 59, $season * 3, date('t', mktime(0, 0, 0, $season * 3, 1, date('Y'))), date('Y')));
			break;
		case 'LQ': // 上一季度
			$n = date('n');  // 当前月份
			$lqm = date('Y-m', strtotime(($n % 3) - 3 . ' month'));  // 上季度最后一月
			$start = date('Y-m', strtotime((ceil($n / 3) * 3 - 3 + 1 - 3 - 3) . ' month')) . '-01 00:00:00';
			$end = $lqm . '-' . date('t', strtotime($lqm)) . ' 23:59:59';
			break;
		case 'Y':  // 今年
			$y = date('Y');
			$start = $y . '-01-01 00:00:00';
			$end = $y . '-12-31 23:59:59';
			break;
		case 'LY': // 去年
			$y = date('Y') - 1;
			$start = $y . '-01-01 00:00:00';
			$end = $y . '-12-31 23:59:59';
			break;
		default:
			return FALSE;
	}
	if (!$format) {
		$start = strtotime($start);
		$end = strtotime($end);
	}
	return array(
		'start' => $start,
		'end' => $end
	);
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
 * 生成 URL关键字
 * 
 * @param string $url 
 * @return array | false
 */
function generate_url_key($url) {
	if (empty($url)) {
		return FALSE;
	}
	if (!filter_var($url, FILTER_VALIDATE_URL)) {
		// 非 URL
		return FALSE;
	}
	$host = parse_url($url, PHP_URL_HOST);  // 获取 host
	if (empty($host)) {
		return FALSE;
	}
	$host = str_replace('www.', '', $host);
	$host_arr = explode('.', $host);
	$host_count = count($host_arr);
	if ($host_count < 4) {
		return $host;  // 去除 www
	}
	return $host_arr[$host_count - 3] . '.' . $host_arr[$host_count - 2] . '.' . $host_arr[$host_count - 1];
}

/**
 * 生成随机 字符串
 * 
 * @param int $len 字符串长度
 * @param int $type = 1 字符类型 0 纯数字 1 纯字母 2 数字字母混合
 * @return string 
 */
function rand_string($len = 6, $type = 1) {
	$len = abs(intval($len));
	if ($len < 1) {
		return '';
	}
	switch (intval($type)) {
		case 0:
			$key = '8901267345';
			break;
		case 1:
			$key = 'UvVwdDeEfFgGhHiIjJkKlWxXyYzZaAbBcPqQrRsStTuCLmMnNoOp';
			break;
		case 2:
			$key = '78eEfFgGhH90aAbBcCd12sStTuUvqQrR7654VwWxXyYzZ0983nNoOpP3456DiIjJkKlLmM21';
			break;
		default:
			$key = 'hHifFgGUvVwStTyYzulLmMnNoOpPIjJkKdDeEqQrRsaAbBcCWxXZ';
			break;
	}
	$max = intval(strlen($key)) - 1;
	$str = '';
	for ($i = 0; $i < $len; $i++) {
		$str .= $key{mt_rand(0, $max)};
	}
	return $str;
}

/**
 *  获取唯一值 ( 10位数 )
 * 
 * @param int $len 长度 ( 大于8 )
 * @return string
 */
function uniqid_string($len = 10) {
	$ip = get_client_ip();  // IP
	$time = number_format(microtime(TRUE), 6, '.', '');  // 时间
	$unqid = hash('crc32b', $time . $ip . mt_rand(100, 999));
	if (intval($len) > 8) {
		$unqid .= rand_string(intval($len) - 8, 2);
	}
	return strtolower($unqid);
}

/**
 * 系统非常规MD5加密方法
 * 
 * @param  string $str 要加密的字符串
 * @param string $key 加密密钥
 * @return string 
 */
function psw_md5($str, $key = '_@regel:)') {
	return '' == $str ? '' : md5(sha1($str) . md5($str) . $key);
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
	$key = md5(empty($key) ?  '_@regel#):say^hooo!!(' : $key);
	$str_len = strlen($string);
	$key_len = strlen($key);
	for ($i = 0; $i < $str_len; $i++) {
		for ($j = 0; $j < $key_len; $j++) {
			$string[$i] = $key[$j] ^ $string[$i];
		}
	}
	return base64_decode($string);
}