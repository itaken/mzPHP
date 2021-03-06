<?php

defined('INI') or die('--ImgConf--');

/**
 * 图片 配置
 *
 * @author itaken <regelhh@gmail.com>
 * @since 2013-11-16
 * @version 1.01
 */
return array(
    /* 图片操作 */
    'IMAGE_HANDLING' => array(
        'path' => MROOT . 'data/static/imgs/', // 图片路径
        'name' => '', // 图片名称
        'nameFunc' => 'uniqid_string', // 图片命名方法
        'maxSize' => 0, // 最大尺寸
        'formatLimts' => array('jpg','jpeg','png','gif'), // 图片限制
        'doThumb' => false, // 缩略图
        'overwrite' => false, // 重复是否覆盖
    ),
    /* 图片缩略图 */
    'IMAGE_THUMB' => array(
        'path' => MROOT . 'data/static/imgs/thumb/', // 缩略图保存位置
        'maxWidth' => 600, // 最大长度
        'maxHeight' => 400, // 最大高度
        'openFixed' => true, // 开启固定大小
        'fixedConf' => array(
            'width' => 600,
            'height' => 400,
            'direction' => 'MC', // 位置  LT左上 MT中上 RT右上 LM左中 MC居中 RM右中 LB左下 MB中下 RB右下
            'constrain' => true, // 是否 等比压缩
        ), // 固定大小 配置
        'onMark' => false, // 是否添加水印
        'overwrite' => false, // 重复是否覆盖
        'retainedOriginal' => true, // 保留原图
    ),
    /* 图片水印 */
    'IMAGE_MARK' => array(
        'file' => MROOT . 'data/static/imgs/public/watermark.png', // 水印图片位置
        'textMark' => true, // 是否 添加 文字水印
        'textConf' => array(
            'text' => '爱我中国', // 文字文本
            'color' => 'blue', // 字体颜色
            'font' => MROOT . 'data/static/fonts/fontello.ttf', // 字体 ( 必须,非中文 )
            'size' => 20, // 字体大小 ( 单位 点 )
            'angle' => 0, // 文字角度
            'direction' => 'MB', // 文本位置  LT左上 MT中上 RT右上 MC居中 LB左下 MB中下 RB右下
            'padding' => 20, // 上边距 ( 单位 px )
        ), // 水印文字配置
        'width' => 270, // 水印 宽度 ( 单位 px )
        'height' => 129, // 水印 高度 ( 单位 px )
        'direction' => 'RB', // 水印位置 LT左上 MT中上 RT右上 LM左中 MC居中 RM右中 LB左下 MB中下 RB右下 MT 中上 MB 中下
        'margin' => 10, // 距离边框 ( 单位 px )
    ),
);
