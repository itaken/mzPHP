<?php

defined('INI') or die('--IndexCtrl--');

/**
 * 应用 主入口
 *
 * @author itaken<regelhh@gmail.com>
 * @since 2014-3-21
 */
class IndexController extends HomeController
{

    /**
     * 网站首页
     */
    public function index()
    {
        $test_mod = new TestModel();
        $bar = $test_mod->sidebar();
        assign('sidebar', $bar);
        render(array('meta_title' => '首页'));
    }

    /**
     * 关于我们
     */
    public function about()
    {
        info_page('mzPHP,感谢有你!', '关于mzPHP', '关于mzPHP');
    }

    /**
     * 使用文档
     */
    public function document()
    {
        assign('meta_title', '使用文档 --mzPHP');
        render();
    }
}
