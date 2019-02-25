<?php

/**
 * 欢迎 使用 mzPHP
 * 
 * @author itaken<regelhh@gmail.com>
 * @since 2014-3-21
 * @version 1.0 RC1
 */
!version_compare(PHP_VERSION, '5.3.0', '<') or die('ERROR: mzPHP require PHP > 5.6 !');

define('APP_DEBUG', FALSE);  // 系统调试设置 ( 项目正式部署后请设置为 false )
if(APP_DEBUG){
    error_reporting(E_ALL);
}else{
    error_reporting(E_ERROR);
}

/**
 * 是否开启路由模式  - TRUE 开启
 */
define('OPEN_SLINK', TRUE);

/**
 * 定义项目常量
 */
define('MROOT', str_replace('\\', '/', dirname(__FILE__)) . '/'); // 框架路径
define('SITE_URL', 'http://' . filter_input(INPUT_SERVER, 'HTTP_HOST') . '/');  // 网址
define('APATH', MROOT . 'application');  // 应用路径

/**
 * 载入 mzPHP 框架
 */
require( '_mzp/mzp.ini.php' );
