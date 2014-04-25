<?php

defined('INI') or die('--Conf--');
/**
 * 整个项目配置
 * 
 * @author regel chen<regelhh@gmail.com>
 * @since 2014-3-21
 * @version 1.0 Beta
 */
$cm = include(MROOT . 'config/common.config.php');  // 基础配置
$db = include(MROOT . 'config/db.config.php');  // 数据库配置
$img = include(MROOT . 'config/img.config.php');  // 图片配置

return array_merge($cm, $db, $img);
