<?php

define('DB_CLS_ROOT', str_replace('\\', '/', dirname(__FILE__)) . '/');   // 定义 数据库操作类 路径

/**
 * 数据库 操作类库
 *
 * @author itaken <regelhh@gmail.com>
 * @since 2014-5-16
 * @version 1.0 Beta
 */
class M_Db
{

    /**
     * @var object 类对象
     */
    private static $_oClsObj = null;

    /**
     * 唯一入口
     */
    public static function ini()
    {
        $config = include(DB_CLS_ROOT . 'db.config.php');  // 载入配置
        $db_type = $config['DB_TYPE'];  // 数据库类型
        if (empty($db_type)) {
            exit("ERROR: DB type `{$db_type}` not found!");
        }
        include(DB_CLS_ROOT . 'lib/db.interface.php');  // 载入接口文件
        $cls_file = DB_CLS_ROOT . 'lib/' . $db_type . '.class.php';
        if (file_exists($cls_file)) {
            include($cls_file);
            if (class_exists($db_type)) {
                self::$_oClsObj = new $db_type($config);
                return new $db_type($config);
            }
        }
        $msg = APP_DEBUG ? 'ERROR: "' . $cls_file . '" file not found OR "' . $db_type . '" class not exist! ' :
                'Server error, please contact the server administrator!';
        exit($msg);
    }

    /**
     * 禁止 初始化
     */
    private function __construct()
    {
    }

    /**
     * 禁止 克隆
     */
    private function __clone()
    {
    }
}
