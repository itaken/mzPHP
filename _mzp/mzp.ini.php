<?php

/**
 * 框架 统一入口
 *
 * @author itaken<regelhh@gmail.com>
 * @since 2014-3-21
 * @version 1.0
 */
defined('MROOT') or die('ERROR: NO MROOT');

/**
 * @var boolean 定义是否已初始化
 */
define('INI', true);

/**
 * 开启 SESSION
 */
isset($_SESSION) || session_start();

/**
 * 开启 短标签
 */
ini_set('short_open_tag', true);

/**
 * @var string 定义分割符号
 */
!defined('DS') && define('DS', DIRECTORY_SEPARATOR);
!defined('APATH') && define(MROOT . 'app/', DS);

/**
 * @var boolean 配置是否使用默认定义
 */
!defined('APP_DEBUG') && define('APP_DEBUG', false);  // 开启调试 ( 默认:关闭 )
!defined('OPEN_SLINK') && define('OPEN_SLINK', true);  // 开启短链 ( 默认:开启 )

/**
 * @var string 定义文件路径
 */
define('CROOT', str_replace('\\', DS, dirname(__FILE__)) . DS . 'core' . DS);  // 核心文件夹
define('LROOT', str_replace('\\', DS, dirname(__FILE__)) . DS . 'library' . DS);  // 库文件夹
define('AROOT', str_replace('\\', DS, realpath(MROOT . APATH)) . DS);  // 应用路径

/**
 * 引入 核心方法库 / 附加方法库 以及 用户方法库
 */
$cfunc = CROOT . 'common' . DS . 'core.function.php'; // 核心方法库
file_exists($cfunc) ? include_once($cfunc) : die('ERROR: Core Function File Not Found!');
$afunc = CROOT . 'common' . DS . 'addons.function.php'; // 附加方法库
file_exists($afunc) ? include_once($afunc) : die('ERROR: Addons Function File Not Found!');
$ufunc = AROOT . 'common' . DS . 'function.php'; // 用戶方法库
file_exists($ufunc) && include_once($ufunc);

/**
 * @var string 缓存文件夹
 */
define('SROOT', mk_dir(MROOT . 'data/cache') . DS);

/**
 * 对用户输入进行处理
 */
isset($_GET) && stop_attack($_GET, $getfilter);
isset($_POST) && stop_attack($_POST, $postfilter);
isset($_COOKIE) && stop_attack($_COOKIE, $cookiefilter);

/**
 * 注册类
 */
if (!defined('MZP_AUTOLOAD')) {
    define('MZP_AUTOLOAD', 1);
    spl_autoload_register('__mzp_autoload');
}

/**
 * 调用 类方法 与 控制器
 */
call_user_func('__mzp_run');
