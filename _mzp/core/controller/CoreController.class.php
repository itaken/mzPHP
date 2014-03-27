<?php

defined('INI') or die('--CoreCtrl--');

/**
 * 核心控制器
 * 
 * @author regel chen<regelhh@gmail.com>
 * @since 2014-3-21
 * @version 1.0 Beta
 */
class CoreController {

	/**
	 * 初始化
	 */
	public function __construct() {
		// To do something...
	}

	/**
	 * 空操作
	 */
	public function _empty(){
		$data = array(
			// SEO
			'meta_title' => $GLOBALS['meta']['default_title'],
			'meta_keywords' => $GLOBALS['meta']['default_keywords'],
			'meta_description' => $GLOBALS['meta']['default_description']
		);
		$GLOBALS['__assign'] = $data;
		$layout_file = CROOT . 'view/error.tpl.html';
		require( $layout_file );
		exit;
	}
	
}
