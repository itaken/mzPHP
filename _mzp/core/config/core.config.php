<?php
/**
 * 基础程序配置文件
 * 
 * @author regel chen<regelhh@gmail.com>
 * @since 2014-3-21
 * @version 1.0 Beta
 */
defined('INI') or die('--CConf--');

return array(
	'DEFAULT_CONTROLLER' => 'index',  // 默认控制器
	'DEFAULT_ACTION' => 'index',  // 默认操作方法
	'TPL_FILE_DEPR' => '/',  // TPL文件分隔符 ( /:目录 )
	'CACHE_EXPIRE' => 60,  // 缓存有效期 ( 单位: 分 )
);
