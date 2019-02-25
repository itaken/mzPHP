<?php

defined('INI') or die('--Conf--');
/**
 * 整个项目配置
 * 
 * @author regel chen<regelhh@gmail.com>
 * @since 2014-3-21
 * @version 1.0 RC1
 */
$cm = include('common.config.php');  // 基础配置
$db = include('db.config.php');  // 数据库配置
$img = include('img.config.php');  // 图片配置

return array_merge($cm, $db, $img);
