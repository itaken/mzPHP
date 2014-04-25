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
	public function _empty() {
		$data = array(
			// SEO
			'meta_title' => c('DEFAULT_META_TITLE'),
			'meta_keywords' => c('DEFAULT_META_KEYWORDS'),
			'meta_description' => c('DEFAULT_META_DESCRIPTION')
		);
		$cache_file = SROOT . md5(serialize($data).OPEN_SLINK) . '.cache';  // 缓存文件
		if (file_exists($cache_file)) {
			if (time() - filemtime($cache_file) > 7 * 24 * 60 * 60) {
				unlink($cache_file);
			} else {
				include($cache_file);
				exit;
			}
		}
		$tpl_file = CROOT . 'view/error.tpl.html';  // 模板文件
		$layout_tpl = MROOT . 'view/public/extend.tpl.html';  // 布局文件
		if (file_exists($tpl_file) && file_exists($layout_tpl)) {
			$_css_ = $match = array();
			$___CSS___ = $___JS___ = null;
			extract($data, EXTR_OVERWRITE);
			ob_start();
			ob_implicit_flush(FALSE);
			include($tpl_file);
			$contents = ob_get_clean();  // 获取并清空缓存
			if (is_array($_css_)) {
				foreach ($_css_ as $v) {
					$file = MROOT . 'static/css/' . $v;
					file_exists($file) && $___CSS___ .= '<link href="' . str_replace(MROOT, SITE_URL, $file) . '" type="text/css" rel="stylesheet">' . PHP_EOL;
				}
			}
			$reg = "'<\s*style\s*>(.*?)<\s*/\s*style\s*>'is";
			preg_match_all($reg, $contents, $match, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);  // 匹配样式
			foreach ($match as $value) {
				$css = $value[0][0];
				$contents = substr_replace($contents, '', $value[0][1], strlen($css));
				$___CSS___ .= $css . PHP_EOL;
			}
			$___CONTENT___ = $contents;
			ob_start();
			require( $layout_tpl );
			$cache_data = ob_get_contents();
			ob_end_flush();
			file_put_contents($cache_file, $cache_data, LOCK_EX);  // 写入 独占锁
			exit();
		}
		exit('ERROR: <a href="https://github.com/itaken/mzPHP" title="mzPHP on GIT!">mzPHP</a> system error!');
	}

}
