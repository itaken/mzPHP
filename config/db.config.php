<?php

defined('INI') or die('--DbConf--');
/**
 * 数据库配置
 * 
 * @author regel chen<regelhh@gmail.com>
 * @since 2014-3-21
 * @version 1.0 Beta
 */
return array(
	'DB_TYPE' => 'mysql',		// 数据库类型
	'DB_HOST' => 'localhost',	// 数据库主机地址
	'DB_PORT' => 3306,		// 数据库端口号
	'DB_USER' => 'root',		// 数据库用户
	'DB_PSW' => '',			// 数据库密码
	'DB_NAME' => '',		// 数据库名称
	'TBL_PREFIX' => '',		// 表前缀
	'TBL_SUFFIX' => '',		// 表后缀
);

