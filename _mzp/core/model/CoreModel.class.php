<?php

defined('INI') or die('--CoreModel--');

/**
 * 核心 模型类
 *
 * @author regel chen<regelhh@gmail.com>
 * @since 2014-3-22
 * @version 1.0 RC2
 */
class CoreModel
{

    /**
     * @var object 数据库对象
     */
    private $_oDb = null;

    /**
     * @var string 真实 数据表名称
     */
    protected $_sTrueTbl = null;

    /**
     * @var array 方法对象
     */
    private $_aMethod = array();

    /**
     * 初始化
     */
    public function __construct()
    {
        $load = c('EXTENSION_LOAD');
        foreach ($load as $ext) {
            $this->_loadExtension($ext);
        }
    }

    /**
     * 载入 扩展
     *
     * @param string $name 扩展名称
     * @return void
     */
    private function _loadExtension($name = null)
    {
        $lib_prefix = c('LIB_CLASS_PREFIX');
        switch ($name) {
            case 'db':  // 数据库 操作
                $true_tbl = $this->_sTrueTbl;  // 定义 真实表名
                if (empty($true_tbl)) {
                    $mod = trim(preg_replace('/([A-Z]{1})/', '_$1', get_class($this)), '_'); // 子类名称
                    $tbl_name = c('TBL_PREFIX') . str_replace('_model', '', strtolower($mod)) . c('TBL_SUFFIX');   // 表名
                } else {
                    $tbl_name = $true_tbl;
                }
                Mlib('db:db.class');   // 载入 数据库操作
                $lib_class = $lib_prefix . ucfirst(strtolower($name));  // 类名
                $db = $lib_class::ini();
                $db->select_tbl($tbl_name, c('DB_NAME'));
                $this->_oDb = $db;
                break;
            default:
                break;
        }
    }

    /**
     * 调用方法
     *
     * @param string $func 调用方法
     * @param mixed $params 对象
     * @return mixed
     */
    public function __call($func, $params)
    {
        if ($this->_oDb) {
            // 数据库 处理操作
            if (method_exists($this->_oDb, $func)) {
                return call_user_func_array(array($this->_oDb, $func), $params);
            }
        }
        $msg = APP_DEBUG ? 'ERROR: ' . get_class($this->_oDb) . ':' . $func . ' function not found!' :
            'Server busy, please try again later!';
        exit($msg);
    }

    /**
     * 析构函数
     */
    public function __destruct()
    {
        $this->_oDb = null;
        $this->_sTrueTbl = null;
    }
}
