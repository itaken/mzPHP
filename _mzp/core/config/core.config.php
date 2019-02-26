<?php

defined('INI') or die('--CConf--');

/**
 * 基础程序配置文件
 *
 * @author itaken<regelhh@gmail.com>
 * @since 2014-3-21
 * @version 1.0
 */
return array(
    // 默认设定
    'DEFAULT_CONTROLLER' => 'index', // 默认控制器
    'DEFAULT_ACTION' => 'index', // 默认操作方法
    'DEFAULT_LAYOUT_FILE' => 'public/extend.tpl.html', // 默认布局模板
    // 默认SEO
    'DEFAULT_META_TITLE' => 'mzPHP 1.0',
    'DEFAULT_META_KEYWORDS' => 'mzPHP,PHP,轻框架,PHP轻框架',
    'DEFAULT_META_DESCRIPTION' => 'More ZZZ, Let\'s PHP!',
    // 模板文件设定
    'TPL_FILE_PATH' => APATH . 'view/', // 模板文件存放路径 ( 不建议修改 )
    'TPL_FILE_DEPR' => '/', // TPL文件分隔符 ( /:目录 )
    'TPL_FILE_SUFFIX' => '.tpl.html', // TPL模板后缀
    // 缓存设定
    'CACHE_ENABLED' => false,  // 是否缓存
    'CACHE_EXPIRE' => 60, // 缓存有效期 ( 单位: 分钟 )
    // 其他设定
    'STATIC_FILE_PATH' => MROOT . 'data/static/', // 静态文件路径
    'STRIP_INDEX_TAG' => true,  // 去除 URL 中 index.php
    'PATH_RENDER_CUSTOM' => array('public/info'), // 路径例外,该路径没有模板文件,使用自定义模板渲染
    'EXTENSION_LOAD' => array('db'),  // 默认加载的扩展
    'LIB_CLASS_PREFIX' => 'M_',   // 类库 类前缀
    'SLINK_URL_SUFFIX' => '.html',   // URL 后缀
);
