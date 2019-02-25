<?php

/**
 * 欢迎 使用 mzPHP
 *
 * @author itaken<regelhh@gmail.com>
 * @since 2014-3-21
 */
!version_compare(PHP_VERSION, '5.6.0', '<') or die('ERROR: mzPHP require PHP > 5.6 !');

define('APP_DEBUG', true);  // 系统调试设置 ( 项目正式部署后请设置为 false )
if (APP_DEBUG) {
    ini_set('display_errors', 'On');
    error_reporting(E_ALL);
} else {
    error_reporting(E_ERROR);
}

// 定义项目常量
define('MROOT', str_replace('\\', '/', __DIR__) . '/'); // 框架路径
define('SITE_URL', 'http://' . filter_input(INPUT_SERVER, 'HTTP_HOST') . '/');  // 网址
define('APATH', MROOT . 'application/');  // 应用路径

// 载入 mzPHP 框架
require(MROOT. '_mzp/mzp.ini.php');
