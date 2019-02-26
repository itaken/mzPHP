<?php

/**
 * 一些私有的 方法库
 *
 * @author itaken<regelhh@gmail.com>
 * @since 2014-5-14
 * @version 1.0
 */
defined('INI') or die('--PFunc--');

/**
 * 类自动加载
 *
 * @param string $class 类名
 * @return void
 */
function __mzp_autoload($class)
{
    $matchs = array();
    if (preg_match('/(controller|model)$/i', $class, $matchs)) {
        $name = str_replace(array('controller', 'model'), '', $class);
        if ($name) {
            $patch = strtolower($matchs[0]) . DS . $name . '.class.php';
            $core_file = CROOT . $patch;
            if (file_exists($core_file)) {
                include($core_file);
            }
            $app_file = APATH . $patch;
            if (file_exists($app_file)) {
                include($app_file);
            }
        }
    }
}

/**
 * 通过反射 匹配参数
 *
 * @param string $controller
 * @param string $action
 * @param array $param
 * @return array
 */
function __refle($controller, $action, $param)
{
    if (empty($param)) {
        return $param;
    }
    if (class_exists('ReflectionClass', false)) {   // 反射
        //		$RC = new ReflectionClass($obj);
        //		$parameters = $RC->getMethod($action)->getParameters();  // 获取方法参数
        //		foreach ($parameters as $param) {
        //			$name = $param->name;
        //			$parameter[$name] = isset($parameter[$name]) ? $parameter[$name] : null;
        //		}
        $matches = $mch = array();
        $func_export = ReflectionMethod::export($controller, $action, true);  // 整个方法输出
        preg_match_all('/\[\s*\<optional\>\s*\$(\w)+\s*\=\s*(\w|\')+\s*]/isU', $func_export, $matches);
        foreach ($matches[0] as $value) {
            preg_match_all('/\$(\w+) \=(.*)]/', $value, $mch);
            $name = $mch[1][0];
            $value = trim($mch[2][0]);
            if (strcasecmp($value, 'NULL') == 0) {
                $value = null;
            } else {
                $value = trim(trim($value, '\''), '"');
            }
            $param[$name] = isset($param[$name]) ? $param[$name] : $value;
        }
    }
    return $param;
}

/**
 * 解析 URL, 调用相关操作
 *
 * @return mixed
 */
function __mzp_run()
{
    $param = array();
    if (OPEN_SLINK) {
        $path_arr = explode('/', str_replace('.html', '', trim(filter_input(INPUT_SERVER, 'REQUEST_URI'), '/')));  // 获取路劲信息
        //		$query_arr = explode('&', filter_input(INPUT_SERVER, 'QUERY_STRING'));  // 查询字串
        $count = count($path_arr);
        if ($count >= 2) {
            if (preg_match('/^\w+$/', $path_arr[0]) && preg_match('/^\w+$/', $path_arr[1])) {
                $controller = strtolower(strip_tags($path_arr[0]));
                $action = strtolower(strip_tags($path_arr[1]));
            }
            if ($count > 2) {
                // 传递的数据
                for ($i = 2; $i < $count; $i+=2) {
                    $key = $path_arr[$i];
                    if (is_string($key)) {  //  && isset($path_arr[$i + 1])
                        $value = isset($path_arr[$i + 1]) ? urldecode($path_arr[$i + 1]) : '';
                        $_GET[$key] = $value;
                    }
                }
            }
        }
    } else {
        $controller = strtolower(strip_tags(filter_input(INPUT_GET, '_c')));
        $action = strtolower(strip_tags(filter_input(INPUT_GET, '_a')));
    }
    foreach ($_GET as $k => $v) {
        if ($k == '_a' || $k == '_c') {
            continue;
        }
        $param[$k] = $v;
    }
    $controller = $GLOBALS['c'] = empty($controller) ? c('DEFAULT_CONTROLLER') : $controller;
    $action = $GLOBALS['a'] = empty($action) ? c('DEFAULT_ACTION') : $action;
    // 控制器与方法调用
    $obj = ucfirst($controller) . 'Controller';  // 组装类名
    if (!is_callable(array($obj, $action))) {   // 判断是否是可回调函数 或使用 method_exists
        APP_DEBUG && die('ERROR: Method - ' . $obj . '::' . $action . ' Not Found!');
        call_user_func(array(new CoreController(), '_empty'));   // 显示空操作
    }
    call_user_func_array(array(new $obj, $action), __refle($obj, $action, $param));
}

/**
 * 回溯 解析
 *
 * @return array
 */
function __tarce()
{
    $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);  // 获取回溯
    $back = array();
    foreach ($trace as $value) {
        $func = $value['function'];
        if (strcmp($func, __FUNCTION__) == 0) {
            continue;
        }
        if (isset($value['file'])) {
            if (strpos($value['file'], 'mzp.ini.php') !== false) {
                break;
            }
        }
        $cls = isset($value['class']) ? $value['class'] : null;
        $back[] = array(
            'function' => $func,
            'class' => $cls,
        );
    }
    return $back;
}

/**
 * 方法参数 数据整合
 *
 * @param array $args 参数列表
 * @param array $data 存储变量
 * @return array
 */
function __args_handle($args, $data = array())
{
    if (empty($args)) {
        return $data;
    }
    $i = 0;
    $num = count($args);
    foreach ($args as $arg) {
        $i ++;
        if (($num == 1 || $i == 0) && is_array($arg)) {
            $data = empty($data) ? $arg : array_merge($data, $arg);
            continue;
        }
        if (is_string($arg)) {
            if (preg_match('#\w+[/\:\|\.]+\w+|^l\:[a-z]+#i', $arg)) {
                $data[] = $arg;
                continue;
            }
            $value = isset($args[$i]) ? $args[$i] : null;
            $data[$arg] = $value;
        } elseif (is_array($arg)) {
            if (!isset($args[$i - 1])) {
                continue;
            }
            $pre_arg = $args[$i - 1];
            if (is_string($pre_arg) && preg_match('#\w+[/\:\|\.]+\w+|^l\:[a-z]+#i', $pre_arg) || is_array($pre_arg)) {
                $data = array_merge($data, $arg);
            }
        }
    }
    return $data;
}

/**
 * 渲染前 处理
 *
 * @param array $args 方法参数
 * @return array
 */
function __render_handle($args)
{
    $tpl_depr = c('TPL_FILE_DEPR');  // 文件分割符号
    $tpl_suffix = c('TPL_FILE_SUFFIX');  // 模板后缀
    $layout_file = str_replace($tpl_suffix, '', c('DEFAULT_LAYOUT_FILE')) . $tpl_suffix;  // 默认布局文件
    $seo = array(// SEO
        'meta_title' => c('DEFAULT_META_TITLE'),
        'meta_keywords' => c('DEFAULT_META_KEYWORDS'),
        'meta_description' => c('DEFAULT_META_DESCRIPTION')
    );
    $globals = isset($GLOBALS['__assign']) ? array_merge($seo, $GLOBALS['__assign']) : $seo;
    $data = __args_handle($args, $globals); // 参数数据处理
    $param = array();
    $_tpl = $tpl_file = null;
    foreach ($data as $k => $v) {
        if (is_int($k)) {
            if (!is_string($v)) {
                continue;
            }
            if (stripos($v, 'l:') === 0) {
                $layout_tpl = preg_replace('#[/\:\|\.]+#', $tpl_depr, substr(str_replace($tpl_suffix, '', $v), 2));
                $layout_file = $layout_tpl . $tpl_suffix;
                continue;
            } elseif (preg_match('#\w+[/\:\|\.]+\w+#', $v)) {
                $_tpl = preg_replace('#[/\:\|\.]+#', $tpl_depr, str_replace($tpl_suffix, '', $v));
                list($controller, $action) = explode($tpl_depr, $_tpl);
                $tpl_file = $_tpl . $tpl_suffix;
                continue;
            }
        }
        if (is_string($k)) {
            $param[$k] = $v;
            continue;
        }
    }
    $exception = (empty($_tpl) || !in_array($_tpl, c('PATH_RENDER_CUSTOM'))) ? false : true;  // 是否例外
    if (empty($tpl_file) || $exception) {
        // 没有模板文件, 使用回溯信息
        $trace = __tarce();
        foreach ($trace as $value) {
            if (empty($value['class'])) {
                continue;
            }
            $controller = strtolower(str_replace('Controller', '', $value['class']));
            $action = $value['function'];
            $tpl_file = $exception ? $tpl_file : $controller . $tpl_depr . $action . $tpl_suffix;  // 回溯组装模板
            break;
        }
    }
    // var_dump($tpl_file,$layout_file,$controller,$action);exit;
    $GLOBALS['c'] = $controller;
    $GLOBALS['a'] = $action;
    $GLOBALS['__assign'] = $param;
    return array(
        'layout' => $layout_file, // 布局文件
        'tpl' => $tpl_file, // 模板文件
        'param' => $param, // 参数
    );
}
