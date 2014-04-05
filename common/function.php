<?php

/**
 * 用户方法库
 * 
 * @author regel chen<regelhh@gmail.com>
 * @since 2014-4-5
 * @version 1.0 Beta
 */
defined('INI') or die('--UFunc--');

/**
 * 是否 缓存
 * 
 * @return boolean -true 缓存 -false 不缓存
 */
function is_cache() {
	return isset(filter_input(INPUT_GET, '_cache')) ? TRUE : FALSE;
}

/**
 * 获取 图片
 * 
 * @param string $name 图片名称
 * @param string $dir 附加目录
 * @return string
 */
function img($name, $dir = NULL) {
	$name = strtolower(trim($name));
	if (empty($name)) {
		return SITE_URL . '/static/imgs/public/default.jpg';
	}
	$param = MROOT . 'static/imgs/' . (empty($dir) ? $name : trim($dir) . '/' . $name) . '.*';
	$files = glob($param, GLOB_NOSORT);  // 匹配
	if (empty($files)) {
		return SITE_URL . '/static/imgs/public/default.jpg';
	}
	return str_replace(MROOT, SITE_URL, $files[0]);
}

/**
 * 引入模板文件
 * 
 * @param string $path 模板路径
 * @param boolean $data 是否加载数据
 * @return string 模板内容
 */
function tpl($path, $data = FALSE) {
	$layout_file = MROOT . 'view/' . $path . '.tpl.html';  // 完整路径
	if (file_exists($layout_file)) {
		$data && extract($GLOBALS['__assign']);
		require( $layout_file );  // 引入文件
	} elseif (APP_DEBUG) {
		// 文件不存在
		exit('--"' . $path . '.tpl.html" ERROR: ' . 'FILE Not Found!!');
	}
}

