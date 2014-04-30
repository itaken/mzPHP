<?php

/**
 * 框架 统一入口
 * 
 * @author regel chen<regelhh@gmail.com>
 * @since 2014-3-21
 * @version 1.0 Beta
 * @todo 开启php.ini文件中的short_open_tag设置,设定为On
 */
#
####### 变量定义 #######
defined('MROOT') or die('--ERROR: NO MROOT');
define('INI', TRUE);  // 定义是否已初始化
!defined('DS') && define('DS', DIRECTORY_SEPARATOR); // 定义分割符号
!defined('APP_DEBUG') && define('APP_DEBUG', FALSE);  // 开启调试 ( 默认:关闭 )
!defined('OPEN_SLINK') && define('OPEN_SLINK', FALSE);  // 开启短链 ( 默认:关闭 )
define('CROOT', str_replace('\\', DS, dirname(__FILE__)) . DS . 'core' . DS);  // 核心文件夹
define('LROOT', str_replace('\\', DS, dirname(__FILE__)) . DS . 'library' . DS);  // 库文件夹
#
####### 文件引入 #######
$c_func = CROOT . 'common' . DS . 'core.function.php'; // 核心方法库
file_exists($c_func) ? include_once($c_func) : die('--ERROR: CFunc File Not Found!');
$a_func = CROOT . 'common' . DS . 'addons.function.php'; // 附加方法库
file_exists($a_func) ? include_once($a_func) : die('--ERROR: AFunc File Not Found!');
$u_func = MROOT . 'common' . DS . 'function.php'; // 用戶方法库
file_exists($u_func) && include_once($u_func);

isset($_SESSION) || session_start();  // 开启 SESSION
// 对用户输入进行处理
isset($_GET) && stop_attack($_GET, $getfilter);
isset($_POST) && stop_attack($_POST, $postfilter);
isset($_COOKIE) && stop_attack($_COOKIE, $cookiefilter);
//isset($_REQUEST) && stop_attack($_REQUEST, $getfilter);
#
####### 引入 模型和控制器 #######
//$core_path = CROOT . 'controller' . PATH_SEPARATOR . CROOT . 'model';  // 核心
//$app_path = MROOT . 'controller' . PATH_SEPARATOR . MROOT . 'model';  // 应用
//set_include_path(get_include_path() . PATH_SEPARATOR . $core_path . PATH_SEPARATOR . $app_path); // 引入路径
//spl_autoload_extensions('.class.php');  // 文件后缀
//spl_autoload_register(); // 注册

if (!defined('MZP_AUTOLOAD')) {
	define('MZP_AUTOLOAD', 1);
	spl_autoload_register('__mzp_autoload');
}

// 创建 缓存文件
$sroot = MROOT . 'cache';
if (!is_dir($sroot)) {
	$oldumask = umask(0);
	if (@mkdir($sroot)) {
		chmod($sroot, 0777);
		define('SROOT', $sroot . DS);  // 缓存文件夹
	}
	umask($oldumask);
}
#
####### 控制器与方法调用 #######
// 代码是否压缩
if (strpos(filter_input(INPUT_SERVER, 'HTTP_ACCEPT_ENCODING'), 'gzip') !== FALSE && @ini_get("zlib.output_compression")) {
	ob_start("ob_gzhandler");
}

// 操作方法 与 控制器
$ctrl = __deU();
// 调用类方法
call_user_func_array(array(new $ctrl['controller'], $ctrl['action']), $ctrl['parameter']);
