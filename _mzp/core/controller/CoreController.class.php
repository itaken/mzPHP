<?php

defined('INI') or die('--CoreCtrl--');

/**
 * 核心控制器
 * 
 * @author regel chen<regelhh@gmail.com>
 * @since 2014-3-21
 * @version 1.0 RC1
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
	public function _empty() {
		$cache_name = __CLASS__ . __FUNCTION__ . g('c') . g('a');  // 缓存文件
		$contents = SFile($cache_name);
		if (!empty($contents)) {
			HTTPCache($contents);  // 缓存 推送到浏览器
		}
		$error_file = CROOT . 'view/error.tpl.html';  // 模板文件
		$layout_tpl = c('TPL_FILE_PATH') . c('DEFAULT_LAYOUT_FILE');  // 布局文件
		if (!file_exists($error_file) || !file_exists($layout_tpl)) {  // 文件不存在
			exit('ERROR: <a href="https://github.com/itaken/mzPHP" title="mzPHP on GIT!">mzPHP</a> system error!');
		}
		$data = array(  // SEO 优化
			'meta_title' => c('DEFAULT_META_TITLE'),
			'meta_keywords' => c('DEFAULT_META_KEYWORDS'),
			'meta_description' => c('DEFAULT_META_DESCRIPTION')
		);
		extract($data, EXTR_OVERWRITE);
		ob_start();
		ob_implicit_flush(FALSE);
		include($error_file);
		$tpl_contents = ob_get_clean();  // 获取并清空缓存
		$reg = '"<style[^>]*>(.*?)</style>"isU';
		$match = array();
		preg_match_all($reg, $tpl_contents, $match, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);  // 匹配
		foreach ($match as $value) {
			$css = $value[0][0];
			@$GLOBALS['___CONTENT___'] = substr_replace($tpl_contents, '', $value[0][1], strlen($css));
			@$GLOBALS['___CSS___'] .= $css . PHP_EOL;
		}
		ob_start();
		require( $layout_tpl );
		$cache_data = ob_get_contents();
		ob_end_flush();
		SFile($cache_name, $cache_data, 24 * 60);  // 设定缓存
		exit();
	}

}
