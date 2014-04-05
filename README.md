# mzPHP 简介
More ZZZ, Let's PHP!!

mzPHP 是一个轻框架,目的是:更少的工作,更快的建站.

mzPHP 使用一个核心方法库,以及一个附加方法库. 每个库都是七八个函数,不需要花费太多时间去了解,容易上手!

提供一个用户自定义的方法库,方便用户DIY. 每个函数方法有详细的注释.作用、参数和返回值都有注释.

## 前端控制器 (FrontController)
mzPHP中,页面的所有访问请求,都会经过前端控制器,然后处理分发,响应请求。该功能的好处是可以统一管理,方便控制.

相当于用户所有访问都是在index.php中发生的,但实际上用户看到的是由前端控制器获取请求,分发处理后的结果.

示例:

```php
/index.php?_q_=index/index  // 开启路由后效果
/index/index.html   // apache伪静态
/index.php?c=index&a=index  // 未开启路由后效果
```

该示例将告诉前端控制器:用户访问IndexController控制器中的index方法.前端控制器将自动加载相应的controller并调用action方法.

## 业务逻辑 (Model)

业务逻辑是最底层与数据库交互的一层. 以下为 Model Class 的示例：

```php
class TestModel extends CoreModel {
	
	/** 查询test1数据表列表 */
	public function queryList(){
		$sql = 'SELECT * FROM test1';
		$list = $this->query($sql);
		return $list;
	}
}
```

## 控制器 (Controller)

控制器接受用户输入,控制用户界面的数据显示以及更新模型对象的状态,可以看成是"视图"与"模型"之间的中转站.
以下为 Controller Class 的示例：

```php
class IndexController extends CoreController{
	/**
	 * 初始化
	 */
	public function __construct(){
		parent::__construct();
	}
	
	/**
	 * 首页显示
	 */
	public function index(){
		$data['meta_title'] = '首页';
		assign(data);   // 数据分发
		render();  // 页面渲染
	}
}
```

## 视图 (View)
视图可以看成用户看到并与之交互的界面.

mzPHP更新之后,采用了layout系统,以方便的对模板的重用。
布局模板默认存放在view目录的public文件中,也可以可自由命名.

如果想要修改模板,在调用render渲染时,传递"L:存放目录/布局模板名"参数("L"可以为小写的"l").

例如: "L:public/extend2" 或 "L:public/extend2.tpl.html" :表示使用extend2布局

具体使用看示例,layout原理见下.

# 一些亮点
为了更快捷使用,更快速上手,mzPHP使用了一些很不错的机制. 

## 布局系统 (layout)

使用布局系统,主要是方便代码重用.

例如: 需要重用某模板的标题和页脚,这时候就很有用了,只需要新建一个相应控制器方法模板即可,系统可以自动调用该模板.

如果需要使用不同模板,则只要依照样例新建一个新的布局模板,在渲染的时候调用这个布局模板即可.

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

## 连贯操作

习惯连贯操作的我,自然会在mzPHP中添加这样的功能.

连贯操作可以有效的提高数据存取的代码清晰度和开发效率, 方便操作, 容易上手.

示例: 
```php
$where = array('id' => 3);
$rs = $this->where($where)->find();   // 查询id=3的数据

// 左关联test2,查询t1中id=3的数据
$rs = $this->_as('t1')->join(array('test2 as `t2` ON t1.id = t2.id',))->where('t1.id = 3')->select();
```

## 替代语法

由于没有没有使用模板引擎,所以在页面模板中只能采用PHP原生语法书写.

正是因为在HTML页面中使用了PHP原生语法,所以为了方便阅读使用替代方法.

更多内容,请查看[流程控制-php.net](http://www.php.net/manual/zh/control-structures.alternative-syntax.php)

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

## 自定义魔术方法

为了方便调用,在核心库中自定义了一些魔术方法(详情,见下). 用户也可以自定义.

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
function info_page($info, $title, $meta_title) // 系统提示页面
function json_return($data, $info = '', $status = '')  // json返回数据
</pre>

## 附加方法

<pre>
function is_mobile_request()   // 判断是否移动端请求
function ajax_echo( $data ) // 异步返回数据
function mk_dir($dir, $mode)  // 循环创建目录
function xor_encrypt($string, $key)  // 字符串加密
function xor_decrypt($string, $key)  // 字符串解密
</pre>

## 用户方法

<pre>
function tpl($path, $data = FALSE)  // 引入模板文件
function img($name, $dir = NULL)  // 引入图片文件
function stc($file, $dir)  // 获取static中文件
</pre>

## 数据库函数

<pre>
protected function data($array)   // 传递数据
protected function insert($data)  // 插入数据 
protected function where($where)   // 判断条件
protected function update($data, $where)  // 更新数据
protected function delect($where )   // 删除数据
protected function field($field)  // 查询字段
protected function order($order)  // 查询排序
protected function find($where = null, $field = null, $order = null)  // 查询数据(单条)
protected function limit($limit)  // 限制个数
protected function select($where = null, $field = null, $order = null, $limit = null)  // 查询数据
protected function _as($name)  // 表别名
protected function join()   // 表关联
protected function query($sql)  // 执行SQL
</pre>

# 页面前端

采用前端框架BootStrap，[前往详情介绍](http://www.bootcss.com/)。

页面响应：使用JQuery2.0。[前往参考手册](http://api.jquery.com/)。

OK，暂时就说这么多了，希望mzPHP能让你愉悦编程,快速建站。

[END]
