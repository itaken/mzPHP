<?php
/**
 * 欢迎 使用 mzPHP , 本框架遵循 BSD协议, 保留署名可自由分发修改或商用
 * 
 * @author regel chen<regelhh@gmail.com>
 * @since 2014-3-21
 * @version 1.0 Beta
 */
error_reporting(E_ALL);  // 显示所有错误信息

/**
 * 系统调试设置 ( 项目正式部署后请设置为 false )
 */
define('APP_DEBUG', TRUE);

/**
 * 是否开启路由模式  - TRUE 开启
 */
define('OPEN_SLINK', TRUE);

/**
 * 定义项目常量
 */
define('MROOT', str_replace('\\', '/', dirname(__FILE__)) . '/'); // 路径
define('SITE_URL', 'http://' . filter_input(INPUT_SERVER,'HTTP_HOST'));  // 网址

/** 
 * 载入 mzPHP 框架
 */
require( '_mzp/mzp.ini.php' );
