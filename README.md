mzPHP 简介
============
>More ZZZ, Let's PHP!!
* * *
mzPHP 是一个__轻框架__。让更少的工作，更快的建站。

* 一个**核心方法库** (10个)
* 一个**附加方法库** (16个,使用`4个`)
* 一个**私有方法库** (6个)

提供一个用户`自定义`的方法库，方便用户DIY。每个函数方法有较为详细的注释。

# 一些便捷的设定

为了更便捷编码，更快速上手，mzPHP借鉴使用了一些很不错的机制。

>## 布局系统 (layout)
	使用布局系统，主要是方便代码重用。
  例如：需要重用某模板的标题和页脚，这时候就很有用了，只需要新建一个相应控制器方法模板即可，系统可以自动调用该模板。

  如果需要*使用不同模板*,则只要依照样例新建一个新的布局模板,在渲染的时候调用这个布局模板即可.

```html
<html>
<!-- 模板标题 -->
	<body>
		<!-- 页面内容 -->
		<?=$___CONTENT___;?>
		<!-- 模板页脚 -->
		<footer>
		</footer>
	</body>
</html>
```
>## 更改模板
  mzPHP借鉴采用了layout系统,以方便对模板的重用。如果想要修改模板，在调用render渲染时,传递"L：存放目录/布局模板名"参数("L"可以为小写的"l")即可。

  例如: `"L:public/extend2"` 或 `"L:public/extend2.tpl.html"` ：表示使用public目录下extend2布局模板。

>## 前端控制器 (FrontController)
  mzPHP中，页面的所有访问请求，都会经过前端控制器，然后处理分发，响应请求。该功能的好处是可以统一管理，方便控制。

相当于用户所有访问都是在`index.php`中发生的,但实际上_用户看到的_ 是由前端控制器获取请求,分发处理后的结果.

示例:

```php
/index.php/index/index  // OPEN_SLINK = TRUE 效果

/index/index.html   // apache伪静态

/index.php?_c=index&_a=index  // 未开启路由后效果

```


>## 连贯操作
	习惯连贯操作的我,自然会在mzPHP中添加这样的功能.
  连贯操作可以有效的提高数据存取的代码清晰度和开发效率, 方便操作, 容易上手.

示例: 

```php
$where = array('id' => 3);
$rs = $this->where($where)->find();   // 查询id=3的数据

// 左关联test2,查询t1中id=3的数据
$rs = $this->_as('t1')->join(array('test2 as `t2` ON t1.id = t2.id',))->where('t1.id = 3')->select();

```

>## 替代语法
   由于没有没有使用模板引擎,所以在页面模板中只能采用PHP原生语法书写.
   正是因为在HTML页面中使用了PHP原生语法,所以为了方便阅读使用替代方法.
   
   更多内容,请查看 [流程控制-php.net](http://www.php.net/manual/zh/control-structures.alternative-syntax.php "流程控制")

示例:

```php
<?php if ($a<0): ?>
	其实,我是负数!
<?php endif; ?>
```

上面的语句等同于

```php
<?php if ($a<0){ ?>
	其实,我是负数!
<?php } ?>
```

注: if ,for，foreach都可以这样写。

>## 魔术方法 (自定义魔术方法)
  为了方便调用,在核心库中自定义了一些魔术方法。用户也可以_自定义魔术方法_。


>## Bootstrap框架
  mzPHP采用前端框架BootStrap，[前往详情介绍](http://www.bootcss.com/)。
  为什么呢？好用！

页面响应：使用JQuery2.0。[前往参考手册](http://api.jquery.com/)。


*****
希望mzPHP能让你愉悦编程,快速建站。

[END]
