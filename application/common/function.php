<?php

/**
 * 用户方法库
 *
 * @author itaken<regelhh@gmail.com>
 * @since 2014-4-5
 * @version 1.01
 */
defined('INI') or die('--UFunc--');

/**
 * 获取 图片
 *
 * @param string $name 图片名称
 * @param string $dir 附加目录
 * @return string
 */
function img($name, $dir = null)
{
    $name = strtolower(trim($name));
    if (empty($name)) {
        return str_replace(MROOT, SITE_URL, c('STATIC_FILE_PATH')) . 'imgs/public/default.jpg';
    }
    $param = c('STATIC_FILE_PATH') . 'imgs/' . (empty($dir) ? $name : trim($dir) . '/' . $name) . '.*';
    $files = glob($param, GLOB_NOSORT);  // 匹配
    if (empty($files)) {
        return str_replace(MROOT, SITE_URL, c('STATIC_FILE_PATH')) . 'imgs/public/default.jpg';
    }
    return str_replace(MROOT, SITE_URL, $files[0]);
}

/**
 * 引入 模板文件
 *
 * @param string $path 模板路径
 * @param boolean $data 是否加载数据
 * @param boolean $return 是否返回数据 ( 类似于TP的 fetch )
 * @return mixed 模板内容
 */
function tpl($path, $data = false, $return = false)
{
    $layout_file = c('TPL_FILE_PATH') . $path . '.tpl.html';  // 完整路径
    if (file_exists($layout_file)) {
        $data && extract($GLOBALS['__assign']);
        if ($return) {
            ob_start();
            require($layout_file);
            $contents = ob_get_contents();  // 获取缓存时间
            ob_end_flush();
            return $contents;
        }
        require($layout_file);  // 引入文件
    } elseif (APP_DEBUG) {
        // 文件不存在
        exit('--"' . $path . '.tpl.html" ERROR: ' . 'FILE Not Found!!');
    }
}

/**
 * 获取static内容
 *
 * @param string $file 文件
 * @param string $dir 目录
 * @return string
 */
function stc($file, $dir = 'css')
{
    $path = c('STATIC_FILE_PATH') . $dir . '/' . $file;
    if (file_exists($path)) {
        return str_replace(MROOT, SITE_URL, $path);
    }
    return '#';
}

/**
 * 系统非常规MD5加密方法
 *
 * @param  string $str 要加密的字符串
 * @param string $key 加密密钥
 * @return string
 */
function encrypt_md5($str, $key = 'mzPHP:)')
{
    return '' == $str ? '' : md5(sha1($str) . md5($str) . $key);
}
