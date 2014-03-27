<?php

defined('INI') or die('--IndexCtrl--');

/**
 * 应用 主入口
 * 
 * @author regel chen<regelhh@gmail.com>
 * @since 2014-3-21
 * @version 1.0 Beta
 */
class IndexController extends HomeController {

	/**
	 * 网站首页
	 */
	public function index() {
//		var_dump($_GET['name']);
		$test_mod = new TestModel();
		$bar = $test_mod->sidebar();
		assign('sidebar',$bar);
		render();
	}

	/**
	 * 关于我们
	 */
	public function about() {
		info_page('mzPHP,感谢有你!', '关于mzPHP', '关于mzPHP');
	}

	/**
	 * 联系我们
	 */
	public function contact() {
		$msg = '微博 - <a href="http://weibo.com/u/3217812867" target="_blank">@此处应有字幕</a>';
		info_page($msg, '想找人瞎扯?', '联系我');
	}

}
