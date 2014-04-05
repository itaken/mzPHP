<?php

/**
 * 框架 统一入口
 * 
 * @author regel chen<regelhh@gmail.com>
 * @since 2014-3-21
 * @version 1.0 Beta
 */
#
####### 变量定义 #######
defined('MROOT') or die('--ERROR: NO MROOT');
!defined('DS') && define('DS', DIRECTORY_SEPARATOR); // 定义分割符号
!defined('APP_DEBUG') && define('APP_DEBUG', FALSE);  // 开启调试 ( 默认:关闭 )
!defined('OPEN_SLINK') && define('OPEN_SLINK', FALSE);  // 开启短链 ( 默认:关闭 )
define('INI', TRUE);  // 定义是否已初始化
define('CROOT', str_replace('\\', DS, dirname(__FILE__)) . DS . 'core' . DS);  // 核心文件
define('LROOT', str_replace('\\', DS, dirname(__FILE__)) . DS . 'library' . DS);  // 库文件
define('SROOT', str_replace('\\', DS, dirname(__FILE__)) . DS . 'cache' . DS);  // 库文件
// ini_set('display_errors', true);  // 显示所有错误信息
#
####### 文件引入 #######
$c_func = CROOT . 'common' . DS . 'core.function.php'; // 核心方法库
file_exists($c_func) ? include_once($c_func) : die('--ERROR: CFunc File Not Found!');
$a_func = CROOT . 'common' . DS . 'addons.function.php'; // 附加方法库
file_exists($a_func) ? include_once($a_func) : die('--ERROR: AFunc File Not Found!');
$u_func = MROOT . 'common' . DS . 'function.php'; // 用戶方法库
file_exists($u_func) && include_once($u_func);


// 对用户输入进行处理
isset($_GET) && stop_attack($_GET, $getfilter);
isset($_POST) && stop_attack($_POST, $postfilter);
isset($_COOKIE) && stop_attack($_COOKIE, $cookiefilter);
isset($_REQUEST) && stop_attack($_REQUEST, $getfilter);
#
####### 引入 模型和控制器 #######
$core_path = CROOT . 'controller' . PATH_SEPARATOR . CROOT . 'model';  // 核心
$app_path = MROOT . 'controller' . PATH_SEPARATOR . MROOT . 'model';  // 应用
set_include_path(get_include_path() . PATH_SEPARATOR . $core_path . PATH_SEPARATOR . $app_path); // 引入路径
spl_autoload_extensions('.class.php');  // 文件后缀
spl_autoload_register(); // 注册
#
####### 控制器与方法调用 #######
// 操作方法 与 控制器
if (OPEN_SLINK) {
	$ctrl_arr = explode('/', filter_input(INPUT_GET, '_q_'));  // 获取查询条件
	$count = count($ctrl_arr);  // 总数
	if ($count >= 2) {
		if (preg_match('/^\w+$/', $ctrl_arr[0]) && preg_match('/^\w+$/', $ctrl_arr[1])) {
			$controller = strtolower(strip_tags($ctrl_arr[0]));
			$action = strtolower(strip_tags($ctrl_arr[1]));
		}
		if ($count > 2) {
			// 传递的数据
			for ($i = 2; $i < $count; $i+=2) {
				$value = $ctrl_arr[$i];
				(is_string($value) && isset($ctrl_arr[$i + 1]) ) && $_GET[$value] = urldecode($ctrl_arr[$i + 1]);
			}
		}
	}
} else {
	$controller = strtolower(strip_tags(filter_input(INPUT_GET, 'c')));
	$action = strtolower(strip_tags(filter_input(INPUT_GET, 'a')));
}
$controller = $GLOBALS['c'] = empty($controller) ? c('DEFAULT_CONTROLLER') : $controller;
$action = $GLOBALS['a'] = empty($action) ? c('DEFAULT_ACTION') : $action;

// 控制器与方法调用
$obj = ucwords($controller) . 'Controller';  // 组装类名
if (!method_exists($obj, $action)) {  // 判断类的方法十分存在
	APP_DEBUG && die('--ERROR: Method - ' . $controller . '::' . $action . ' Not Found!');
	call_user_func(array(new CoreController(), '_empty'));   // 抑制所有错误
}
// 代码是否压缩
if (strpos(filter_input(INPUT_SERVER, 'HTTP_ACCEPT_ENCODING'), 'gzip') !== FALSE && @ini_get("zlib.output_compression")) {
	ob_start("ob_gzhandler");
}
// 调用类方法
call_user_func(array(new $obj, $action));
