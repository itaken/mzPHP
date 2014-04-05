# mzPHP 简介
More ZZZ, Let's PHP!!

mzPHP 是一个轻框架,目的是:更少的工作,更快的建站.

mzPHP 使用一个核心方法库,以及一个附加方法库. 核心方法库与附加方法库都是七八个常用函数,不足二十个函数不需要花费太多时间去了解.

这几个方法的使用在样例中有,在函数方法中也有详细的注释.容易上手开发网站.

## 前端控制器 (FrontController)
mzPHP中,页面的所有访问请求,都会经过前端控制器,然后处理分发,响应请求。该功能的好处是可以统一管理,方便控制.

相当于用户所有访问都是在index.php中发生的,但实际上用户看到的是由前端控制器获取请求,分发处理后的结果.

示例:

```php
/index.php?_q=index/index  // 开启路由后效果
/index/index.html   // apache伪静态
/index.php?c=index&a=index  // 未开启路由后效果
```

该示例将告诉前端控制器:用户访问IndexController控制器中的index方法.前端控制器将自动加载相应的controller,和调用action方法.

## 控制器示例 (Controller)

以下为 Controller Class 的示例：

```php
class IndexController extends CoreController{
	public function __construct(){
		parent::__construct();
	}

	public function index(){
		$data['meta_title'] = '首页';
		assign(data);
		render();
	}
}
```
## 模型示例 (Model)

以下为 Model Class 的示例：

```php
class TestModel extends CoreModel {
	
	public function queryList(){
		$sql = 'SELECT * FROM test1';
		$list = $this->query($sql);
		return $list;
	}
}
```

## 视图 (View)
采用了layout系统,所以可以更方便的对模板标题和页脚的重用。

布局模板默认存放在view目录的public文件中,可自由命名,在调用render渲染时,传递"L:存放目录/布局模板名"参数.
例如: "L:public/extend2" 或 "L:public/extend2.tpl.html"

具体使用看示例,layout原理见下.

## 布局系统 (layout)
使用布局系统,方便代码重用,只需在对应的目录下建立控制器方法模板,即可使用同一布局模板渲染.

<pre>
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
</pre>

## MVC结构

mzPHP是遵循MVC模式的,即业务逻辑和视图逻辑是完全分离的。

逻辑模型:存放在model目录中,处理程序数据逻辑(主要与数据库交互);

视图:存放在view文件中,模板显示,使用render函数渲染呈现于用户界面.

业务控制器:交互处理,页面显示与底层逻辑模型的交互,存放在controller目录中.

## 替代语法

由于没有没有使用模板引擎,所以在页面模板中,只能采用PHP原生语法书写.

示例:

```php
<?php if ($a<0): ?>
	是负数拉
<?php endif; ?>
```
上面的语句等同于
```php
<?php if ($a<0){ ?>
	是负数拉
<?php } ?>
```

注: if ,for，foreach都可以这样写。

# 常用函数
mzPHP中的函数主要有3类:魔术方法,功能函数和库函数。

## 魔术方法

<pre>
function c( $str ) // 读取配置文件中数据 $GLOBALS['config'][$str]
function g( $str ) // 取得全局变量 $GLOBALS[$str] 的数据
function u( $path, $param ) // 组装URL
</pre>

## 核心函数
<pre>
function assign() // 数据分发
function render()  // 渲染模板
function info_page($info, $title = '系统消息', $meta_title = '系统提示') // 系统提示页面
function json_return($data, $info = '', $status = '')  // json返回数据
</pre>

## 附加方法

<pre>
function ajax_echo( $data ) // 异步返回数据
function is_mobile_request()   // 判断是否移动端请求
function mk_dir($dir, $mode = 0777)  // 循环创建目录
function xor_encrypt($string, $key = '')  // 加密
function xor_decrypt($string, $key = '')  // 解密
</pre>

## 用户方法

<pre>
function tpl($path, $data = FALSE)  // 引入模板
function img($name, $dir = NULL)  // 引入图片
</pre>

## 库函数

数据库函数
<pre>
function db() // 使用config目录下的数据库设置,创建并返回数据库链接
function get_data( $sql , $db = NULL ) // 获取多个结果集
function get_line( $sql , $db = NULL ) // 获取单个结果集
function run_sql( $sql , $db = NULL ) // 运行sql,不返回结果集
function insert_id($db)  // 最后一个插入ID
function affected_rows($db)   // 影响行数
function close_db( $db ) // 显式关闭数据库链接
</pre>

分页函数
<pre>
// TODO::待填充...
</pre>

# 页面前端

样式：采用前端框架BootStrap，[前往详情介绍](http://www.bootcss.com/)。

页面响应：使用JQuery2.0。[前往参考手册](http://api.jquery.com/)。

OK，暂时就说这么多了，希望mzPHP能让你愉悦编程,快速建站。

[END]
