<?php

/**
 * 附加方法库
 *
 * @author itaken<regelhh@gmail.com>
 * @since 2014-3-23
 * @version 1.0
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
function stop_attack($value, $filt_req)
{
    if (empty($value) || empty($filt_req)) {
        return false;
    }
    $value = is_array($value) ? implode($value) : $value;
    // 匹配过滤
    if (preg_match("/" . $filt_req . "/is", $value)) {
        exit('Illegal operation! --->' . $value);
    }
}

/**
 * 获取精确时间
 *
 * @return float
 */
function get_time()
{
    return number_format(microtime(true), 6, '.', '');
}

/**
 * 是否 DEBUG
 *
 * @return boolean -true debug
 */
function is_debug()
{
    return isset($_GET['_debug']) ? true : false;
}

/**
 * 是否 缓存
 *
 * @return boolean -true 缓存 -false 不缓存
 */
function is_cache()
{
    return isset($_GET['_no_cache']) ? false : true;
}

/**
 * 是否 GET 提交
 *
 * @return boolean - true 是
 */
function is_get()
{
    return (filter_input(INPUT_SERVER, 'REQUEST_METHOD') == 'GET') ? true : false;
}

/**
 * 是否 POST 提交
 *
 * @return boolean - true 是
 */
function is_post()
{
    return (filter_input(INPUT_SERVER, 'REQUEST_METHOD') == 'POST') ? true : false;
}

/**
 * 是否 ajax 请求
 *
 * @return boolean -true 是
 */
function is_ajax()
{
    $headers = apache_request_headers();
    return (isset($headers['X-Requested-With']) && ($headers['X-Requested-With'] == 'XMLHttpRequest')) ||
            (isset($headers['x-requested-with']) && ($headers['x-requested-with'] == 'XMLHttpRequest'));
}

if (!function_exists('apache_request_headers')) {

    /**
     * 获取全部 HTTP 请求头信息
     *
     * @return array
     */
    function apache_request_headers()
    {
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
function is_mobile()
{
    if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
        return true;
    }
    if (isset($_SERVER['HTTP_PROFILE'])) {
        return true;
    }
    if (strpos(strtolower(filter_input(INPUT_SERVER, 'ALL_HTTP')), 'operamini') !== false) {
        return true;
    }
    if (strpos(strtolower(filter_input(INPUT_SERVER, 'HTTP_USER_AGENT')), 'windows phone') !== false) {
        return true;
    }
    if (strpos(strtolower(filter_input(INPUT_SERVER, 'HTTP_USER_AGENT')), 'windows') !== false) {
        return false;
    }
    if ((isset($_SERVER['HTTP_ACCEPT'])) && (strpos(strtolower(filter_input(INPUT_SERVER, 'HTTP_ACCEPT')), 'application/vnd.wap.xhtml+xml') !== false)) {
        return true;
    }
    $agent_regx = '/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|iphone|ipad|ipod|android|xoom)/i';
    if (preg_match($agent_regx, strtolower(filter_input(INPUT_SERVER, 'HTTP_USER_AGENT')))) {
        return true;
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
    if (in_array(strtolower(substr(filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'), 0, 4)), $mobile_agents)) {
        return true;
    }
    return false;
}

/**
 * 循环创建目录
 *
 * @param string $dir
 * @param int|null $mode 文件夹的权限
 * @return boolean
 */
function mk_dir($dir, $mode = 0777)
{
    if (!is_dir($dir) && strcasecmp(PHP_OS, 'WINNT') != false) {
        @mkdir($dir, $mode, true);
        return $dir;
    }
    $dir = _mkdir($dir, $mode);
    if (is_dir($dir)) {
        return $dir;
    }
    if (!mk_dir(dirname($dir), $mode)) {
        // 循环创建 目录 失败
        $msg = 'ERROR: Directory ' . (APP_DEBUG ? '"' . $dir . '"' : '') . ' creation failed!';
        exit($msg);
    }
    return $dir;
}

/**
 * 兼容 linux 创建文件
 *
 * @param string $dir
 * @param int $mode 权限
 * @return string
 */
function _mkdir($dir, $mode = 0777)
{
    if (is_writable($dir)) {
        return $dir;
    }
    $mask = umask(0);
    if (!is_dir($dir)) {
        @mkdir($dir, $mode);
    }
    chmod($dir, $mode);
    umask($mask);
    return $dir;
}

/**
 * 系统加密方法 ( XOR 方法)
 *
 * @param string $string 要加密的字符串
 * @param string $key  加密密钥
 * @return string
 */
function xor_encrypt($string, $key = '')
{
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
function xor_decrypt($string, $key = '')
{
    $string = base64_decode(trim($string));
    if (empty($string)) {
        return false;
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
 * 获取客户端 IP
 *
 * @return string
 */
function get_ip()
{
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
 * ajax 返回
 *
 * @param string $data 返回的数据
 * @return void
 */
function ajax_echo($data)
{
    if (!headers_sent()) {
        header('Content-Type:text/html;charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
    }
    exit($data);
}

/**
 * 重定向
 *
 * @param string $url 跳转URL
 * @param string $msg  跳转信息
 * @param int $time  跳转四件
 * @return void
 */
function jumps($url, $msg = '', $time = 0)
{
    $url = str_replace(array("\n", "\r"), '', $url);
    $url = empty($url) ? SITE_URL : $url;
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
 * 动态验证
 *
 * @param boolean $condition 条件
 * @param string $func 满足条件后动作
 * @param array|string $param 参数
 * @param string $else_func 不满足条件后动作
 * @param array|string $else_param 参数
 * @return mixed
 */
function opt_active($condition, $func, $param = array(), $else_func = null, $else_param = array())
{
    if ($condition) {
        // 如果是函数,则调用, 如果是语言结果则使用eval
        if (empty($func)) {
            return true;
        }
        is_callable($func) ? call_user_func($func, $param) : eval("$func ('$param');");
    } else {
        if (empty($else_func)) {
            return false;
        }
        is_callable($else_func) ? call_user_func($else_func, $else_param) : eval("$else_func ('$else_param');");
    }
}
