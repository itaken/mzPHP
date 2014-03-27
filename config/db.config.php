<?php

defined('INI') or die('--DbConf--');
/**
 * 数据库配置
 * 
 * @author regel chen<regelhh@gmail.com>
 * @since 2014-3-21
 * @version 1.0 Beta
 */
$GLOBALS['config']['db']['db_type'] = 'mysql';
$GLOBALS['config']['db']['db_host'] = 'localhost';
// SAE 可添加 [db_host_read] 项
$GLOBALS['config']['db']['db_port'] = 3306;
$GLOBALS['config']['db']['db_user'] = '';
$GLOBALS['config']['db']['db_password'] = '';
$GLOBALS['config']['db']['db_name'] = '';
$GLOBALS['config']['db']['tbl_prefix'] = '';  // 表前缀
//$GLOBALS['config']['db']['tbl_suffix'] = '';  // 表后缀

