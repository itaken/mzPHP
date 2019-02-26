<?php

/**
 * 核心方法库
 *
 * @author itaken<regelhh@gmail.com>
 * @since 2014-3-21
 * @version 1.0
 */
defined('INI') or die('--CFunc--');
include('private.function.php');   // 引入一些私有方法

/**
 * 获取 配置内容
 * @example c('CACHE_EXPIRE') -获取缓存有效期
 *
 * @param string $str 配置名称
 * @return mixed 配置值
 */
function c($str)
{
    $conf = isset($GLOBALS['__Mconf']) ? $GLOBALS['__Mconf'] : array();
    if (empty($conf)) {
        $c_conf_file = CROOT . 'config' . DS . 'core.config.php';  // 核心配置
        $c_conf = file_exists($c_conf_file) ? include($c_conf_file) : array();
        // 引入项目配置
        $app_conf_file = APATH . 'config' . DS . 'conf.inc.php';
        $app_conf = file_exists($app_conf_file) ? include($app_conf_file) : array();
        $conf = array_merge($c_conf, $app_conf);
        $GLOBALS['__Mconf'] = $conf;
    }
    return isset($conf[$str]) ? $conf[$str] : null;
}

/**
 * 获取 gloab 变量
 * @example g('a') -获取全局变量a
 *
 * @param string $name  变量名称
 * @return mixed
 */
function g($name)
{
    return isset($GLOBALS[$name]) ? $GLOBALS[$name] : null;
}

/**
 * 生成 URL
 * @example u('index/index') -生成指向 index 控制器 index 操作的链接
 *    ( 支持的自定义模板连接符号: "/"、":"、"|"、"." 四种 )
 *
 * @param string $path url路径
 * @param array $param url参数
 * @return string
 */
function u($path = null, $param = array())
{
    if (is_null($path) || $path == '#' || empty($path)) {
        // 当前 URL
        $curpath = trim(SITE_URL, '/') . filter_input(INPUT_SERVER, 'REQUEST_URI');
        if (empty($param)) {
            return $curpath;
        }
        $param = http_build_query($param);
        return strpos($curpath, '?') > 0 ? $curpath . '&' . $param : $curpath . '?' . $param;
    }
    // 正则匹配, 更自由的定义规则
    $url_path = preg_split('#[/\:\#\-\|\\\&=,\s]+#', $path, 0, PREG_SPLIT_NO_EMPTY);
    if (count($url_path) !== 2) {
        return SITE_URL;
    }
    $controller = $url_path[0];
    $action = $url_path[1];
    if (!OPEN_SLINK) {
        // 未开启 URL重写
        $url = SITE_URL . '?_c=' . $controller . '&_a=' . $action;
        return empty($param) ? $url : ($url . '&' . http_build_query($param));
    }
    $param_str = '';
    if (!empty($param) && is_array($param)) {
        foreach ($param as $key => $value) {
            $param_str .= '/' . $key . '/' . urlencode($value);
        }
    }
    $index = c('STRIP_INDEX_TAG') ? '' :  'index.php/' ;  // 是否 去除 index.php
    return SITE_URL. $index . $controller . '/' . $action . $param_str . c('SLINK_URL_SUFFIX');
}

/**
 * 数据分发 ( 任意个参数 )
 * @example array('name'=>'regel') | 'name','regel' | 'name','regel','msg','test'
 *
 * @args mixed 不固定参数
 * @return void
 */
function assign()
{
    $args = func_get_args();   // 获取所有
    $data = isset($GLOBALS['__assign']) ? $GLOBALS['__assign'] : array();
    $GLOBALS['__assign'] = __args_handle($args, $data);
}

/**
 * 模板输出 类似ThinkPHP的display
 * @example info.tpl.html' | array('meta_title'=>'regel') | array('msg'=>'Just Test!'),'public/info.tpl.html' | 'public','info' | array(...),'public','info'
 *   ( 数据必须使用array类型, 模板必须使用string类型 , 更改布局使用 L:public/extend | L:public/extend.tpl.html 格式)
 *   ( 自定义模板 支持的自定义模板连接符号: "/"、":"、"|"、"." 四种. 例如: index/index )
 *
 * @args mixed 内容数据 | 模板子目录/模板名称
 * @return void
 */
function render()
{
    $args = func_get_args(); // 获取参数
    $handle = __render_handle($args);
    $cache_name = var_export($handle, true);
    $contents = SFile($cache_name);
    if (!empty($contents)) {
        HTTPCache($contents);
    }
    $layout = $handle['layout'];
    $tpl = $handle['tpl'];
    $_path = c('TPL_FILE_PATH');
    $tpl_file = $_path . $tpl;  // 完整 模板路径
    if (!file_exists($tpl_file)) {  // 模板文件 不存在
        APP_DEBUG && exit("ERROR: TEMPLET FILE `{$tpl}` Not Found!");
        @call_user_func(array(new CoreController(), '_empty'));
    }
    $layout_file = $_path . $layout;  // 完整 布局文件
    if (!file_exists($layout_file)) {   // 布局文件 不存在
        APP_DEBUG && exit('"' . $layout . '" ERROR: LAYOUT FILE Not Found!!');
        @call_user_func(array(new CoreController(), '_empty'));
    }
    $param = $handle['param'];
    if (is_array($param)) {
        extract($param, EXTR_OVERWRITE);  // 如果是数组,则导入到当前的符号表中
    }
    $_css_ = $_js_ = array();   // js / css 文件集
    ob_start();
    ob_implicit_flush(false);  // 打开/关闭绝对刷送
    require($tpl_file);  // 引入模板
    $GLOBALS['___CONTENT___'] = ob_get_clean();  // 获取并清空缓存
    foreach ($_css_ as $css) {
        $file = c('STATIC_FILE_PATH') . 'css/' . $css;
        if (file_exists($file)) {
            @$GLOBALS['___CSS___'] .= '<link href="' . str_replace(MROOT, SITE_URL, $file) . '" type="text/css" rel="stylesheet">' . PHP_EOL;
        }
    }
    foreach ($_js_ as $js) {
        $file = c('STATIC_FILE_PATH') . 'js/' . $js;
        if (file_exists($file)) {
            @$GLOBALS['___JS___'] .= '<script type="text/javascript" src="' . str_replace(MROOT, SITE_URL, $file) . '"></script>' . PHP_EOL;
        }
    }
    ob_start();
    require($layout_file);
    $cache_data = ob_get_contents();  // 获取缓存时间
    ob_end_flush();
    SFile($cache_name, $cache_data, c('CACHE_EXPIRE'));
    exit;
}

/**
 * 推送缓存内容
 *
 * @param string $contents 输出内容
 * @return void
 */
function HTTPCache($contents)
{
    header('Content-Type:text/html;charset=utf-8');
    header('Cache-Control: public, must-revalidate');
    $expires = gmdate('l d F Y H:i:s', time() + c('CACHE_EXPIRE') * 60) . ' GMT';
    header('Expires:' . $expires);
    $Etag = md5($contents);  // 设定 Etag key
    if (array_key_exists('HTTP_IF_NONE_MATCH', $_SERVER) && filter_input(INPUT_SERVER, 'HTTP_IF_NONE_MATCH') == $Etag) {
        header('HTTP/1.1 304 Not Modified');
    } else {
        header('Etag:' . $Etag);
        echo $contents;
    }
    exit;
}

/**
 * 信息页
 *
 * @param string $info 信息内容
 * @param string $title 信息标题
 * @param string $meta_title 标题栏标题
 * @return void
 */
function info_page($info, $title = '系统消息', $meta_title = '系统提示')
{
    $data['title'] = $title;
    $data['info'] = $info;
    $data['meta_title'] = $meta_title;
    render($data, 'public/info');
}

/**
 * json 返回
 *
 * @param mixed $data 返回的数据
 * @param string $info 提示信息
 * @param string $status 状态
 * @return void
 */
function json_return($data, $info = '', $status = '')
{
    $return_arr = array(
        'data' => $data,
        'info' => $info,
        'status' => $status
    );
    header('Content-Type:application/json; charset=utf-8');  // 定义返回格式
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');  // 不缓存
    header('Expires: 0');
    exit(preg_replace('#\":\s*(null|false)#iUs', '":""', json_encode($return_arr)));
}

/**
 * 文件缓存
 *
 * @param string $name 缓存名称
 * @param string $data 缓存数据
 * @param int $expire 有效期 ( 单位: 分钟 )
 * @return mixed
 */
function SFile($name, $data = '', $expire = null)
{
    $cache_file = SROOT . md5($name . OPEN_SLINK) . '.cache';
    if (!is_cache() || APP_DEBUG || !is_writable(SROOT) || !c('CACHE_ENABLED')) {  // 不缓存
        if (file_exists($cache_file)) {
            unlink($cache_file);
        }
        return false;
    }
    if (is_null($data)) {  // 数据为 null 时, 表示 清空缓存
        if (file_exists($cache_file)) {
            unlink($cache_file);
        }
        return true;
    }
    if ('' === $data) {  // 未传递 数据时, 表示 获取缓存
        if (file_exists($cache_file)) {
            if (0 === $expire) {
                return file_get_contents($cache_file);
            }
            // 判断 缓存是否过期
            $mtime = filemtime($cache_file);
            $expire = is_null($expire) ? c('CACHE_EXPIRE') : $expire;
            if (time() - $mtime < intval($expire) * 60) {
                return file_get_contents($cache_file);
            }
        }
        return false;
    }
    file_put_contents($cache_file, $data, LOCK_EX);
    return true;
}

/**
 * 载入库
 * @example db:mysql.function [, images:image.class]
 *
 * @param string $libname 库名称
 * @return void
 */
function Mlib($libname)
{
    $lib_arr = explode(',', $libname);
    foreach ($lib_arr as $lib) {
        $lib = str_replace(':', DS, trim($lib));
        $func = LROOT . $lib . '.php';
        if (file_exists($func)) {
            include_once($func);
            continue;
        }
        if (APP_DEBUG) {
            die('ERROR: ' . $lib . '.php File Not Found!');
        }
        die('Server busy, please try again later!');
    }
}
