<?php

defined('INI') or die('--ImgConf--');

/**
 * 图片 配置
 * 
 * @author regel chen <regelhh@gmail.com>
 * @since 2013-11-16
 * @version 1.0 Beta
 */
return array(
	/* 图片保存位置 */
	'IMAGE_PATH' => MROOT . 'static/imgs/',
	/* 图片缩略图 */
	'IMAGE_THUMB' => array(
		'path' => MROOT . 'static/imgs/thumb/', // 缩略图保存位置
		'maxWidth' => 600, // 最大长度
		'maxHeight' => 400, // 最大高度
		'openFixed' => TRUE, // 开启固定大小
		'fixedConf' => array(
			'width' => 600,
			'height' => 400,
			'direction' => 'MC', // 位置  LT左上 MT中上 RT右上 LM左中 MC居中 RM右中 LB左下 MB中下 RB右下 
			'constrain' => TRUE, // 是否 等比压缩
		), // 固定大小 配置
		'onMark' => TRUE, // 是否添加水印
	),
	/* 图片水印 */
	'IMAGE_MARK' => array(
		'file' => MROOT . 'static/imgs/public/watermark.png', // 水印图片位置
		'textMark' => TRUE, // 是否 添加 文字水印
		'textConf' => array(
			'text' => '爱我中国', // 文字文本
			'color' => 'blue', // 字体颜色
			'font' => MROOT . 'static/fonts/lxksz.ttf', // 字体 ( 必须,非中文 )
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
