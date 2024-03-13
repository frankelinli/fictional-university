# 目录

[TOC]

# WordPress 主题特殊文件的作用

## `index.php`

主题的默认界面，如果 WordPress 未找到需要找的文件，就会返回该文件

在该文件中可以使用以下方法在首页循环显示文章

```php
<?php
	while(have_posts()) {
    the_post();
?>
  	<!-- 显示当前文章标题 -->
    <h2>
  		<a href="<?php the_permalink(); ?>">
  			<?php the_title(); ?>
  		</a>
  	</h2>

    <!-- 显示当前文章内容 -->
  	<?php the_content(); ?>
  	<hr>
<?php
  }
?>
```

## `style.css`

WordPress 会自动加载其中的注释，以得到主题相关的信息

```css
/*
  Theme Name: 主题名
  Author: 作者名
  Version: 1.0.0
*/
```

## `screenshot.[png|jpg|jpeg]`

WordPress 会加载该文件作为主题预览图，若存在同名不同格式文件优先加载 `png` 格式

推荐宽高尺寸为 `1200x900px`

## `single.php`

单篇文章的默认载体（在首页点击文章详情跳转到文章页面时），使用方法如下：

```php
<?php
  // 获取头部区域
  get_header();
	// 将当前文章设置为全局变量
  the_post();
?>
  <!-- 文章标题 -->
  <h1><?php the_title(); ?></h1>
  <?php
    // 输出文章内容
    the_content();
		// 获取脚部区域
    get_footer();
  ?>
```

## `page.php`

WordPress 页面的载体，使用方法：

```php
<?php
  // 获取头部区域
  get_header();
	// 将当前文章设置为全局变量
  the_post();
?>
  <!-- 文章标题 -->
  <h1><?php the_title(); ?></h1>
  <?php
    // 输出文章内容
    the_content();
		// 获取脚部区域
    get_footer();
  ?>
```

## `header.php`

当前网站的头部区域，`get_header()` 函数会加载该文件

```php
<!DOCTYPE html>
<!-- 通过wordpress设置网站语言 -->
<html <?php language_attributes(); ?>>
  <head>
  	<!-- 通过wordpress设置网页字符集 -->
    <meta name="charset" content="<?php bloginfo('charset'); ?>">
  	<!-- 设置网页显示尺寸，响应式布局建议加上 -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
  	<!--
      通过wordpress获取head区域内容，通过该函数也可以将其他插件注册的文件加载进来，
      不建议手动写link加载css以及js文件，而是通过wordpress钩子在functions.php中注册
  	-->
    <?php wp_head(); ?>
  </head>
  <!-- wordpress会给网站设置一些类名 -->
  <body <?php body_class(); ?>>
    <header>
      <!-- 网站头部区域内容 -->
    </header>
```

## `footer.php`

当前网站的脚部区域，`get_footer()` 函数会加载该文件

```php
		<!--
      通过wordpress获取footer区域内容，如functions.php或其他插件注册的脚部文件，
      不建议手动写script加载js文件，而是通过wordpress钩子在functions.php中注册
  	-->
		<?php wp_footer(); ?>
  </body>
</html>
```

## `functions.php`

在此调用wordpress钩子函数注册一些事件，比如加载css、js文件，注册菜单位置等

```php
<?php
  // 加载样式表
  function university_files() {
    // 第一个参数为id，第二个参数为文件路径
    wp_enqueue_style('custom-google-fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
    wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
    wp_enqueue_style('university_main_style', get_theme_file_uri('/build/style-index.css'));
    wp_enqueue_style('university_extra_style', get_theme_file_uri('/build/index.css'));
    
    // 第三个参数为依赖项，wordpress会先加载依赖项
    // 第四个参数为当前注入的脚本的版本
    // 第五个参数声明是否需要在head标签里加载，如果为false则在body标签里加载
    wp_enqueue_script('university_main_js', get_theme_file_uri('/build/index.js'), array('jquery'), '1.0.0', true);
  }

  // 添加加载样式表的钩子
  add_action('wp_enqueue_scripts', 'university_files');


  // 添加自定义的meta信息
  function university_features() {
    // 注册wordpress管理界面主题菜单设置
    register_nav_menu('headerMenuLocation', '头部菜单位置');
    register_nav_menu('footerLocationOne', '脚部菜单位置1');
    register_nav_menu('footerLocationTwo', '脚部菜单位置2');

    // 标题通过wordpress生成
    add_theme_support('title-tag');
  }

  add_action('after_setup_theme', 'university_features');
?>
```

## `front-page.php`

网站首页，如果想要设置网站首页不是博客，而是一个定制首页，可以在该文件中设置

1. 新建两个空白页面：
   1. 博客，链接地址为 `/blog/`
   2. 首页，链接地址为 `/home/`
2. 在WordPress管理界面 `设置 => 阅读`，设置主页和文章页显示静态页面
   1. 主页：首页
   2. 文章：博客

3. 新建 `front-page.php`文件

随后，WordPress将做出变化：

- 由 `index.php` 文件接管博客的地址`/blog/`
- 由 `front-page.php` 文件接管首页的地址 `/`
- 访问 `/home/` 会自动重定向到 `/`

# 显示页面导航菜单（父子页面）

一般在 `page.php` 文件中使用，用于展示当前页面的父子页面关系导航菜单，类似于章节导航目录

```php
<?php
  // 当前文章id
  $self_id = get_the_ID();
  // 当前文章的父页面id，如果没有父页面，就会得到0
	$parent_post_id = wp_get_post_parent_id($self_id);
  // 如果当前页面有父页面或者子页面，则展示页面导航菜单
  if ($parent_post_id || get_pages(['child_of' => $self_id])) {
?>
  <!-- 子页面菜单区域，类似于侧边导航菜单，可以将当前页面的父子页面关系展示出来 -->
  <div class="page-links">
  	<!-- 导航菜单父页面标题 -->
    <h2 class="page-links__title">
      <a href="<?php echo get_permalink($parent_post_id); ?>">
        <?php echo get_the_title($parent_post_id); ?>
      </a>
    </h2>
  	<!-- 导航菜单子页面列表 -->
    <ul class="min-list">
      <?php
        // 当前页面有父级页面时，展示父级页面的子页面
        // 否则展示当前页面的子页面
        if ($parent_post_id) {
          $thisChildOf = $parent_post_id;
        } else {
          $thisChildOf = $self_id;
        }

        wp_list_pages(
          array(
            'title_li' => null, // 不显示pages字段
            'child_of' => $thisChildOf,
            'sort_column' => 'menu_order', // 使用页面属性排序
          )
        );
      ?>
    </ul>
  </div>
<?php } ?>
```

# 动态菜单

使用 `add_action('after_setup_theme')` 钩子在主题初始化之后执行自定义函数

并在自定义函数中使用 `register_nav_menu` 注册菜单位置名称

```php
function university_features() {
  // 注册wordpress管理界面主题菜单设置
  register_nav_menu('headerMenuLocation', '头部菜单位置');
  register_nav_menu('footerLocationOne', '脚部菜单位置1');
  register_nav_menu('footerLocationTwo', '脚部菜单位置2');
}

add_action('after_setup_theme', 'university_features');
```

在需要该菜单的地方使用 `wp_nav_menu` 加载菜单，比如在 `header.php` 中：

```php
<nav class="main-navigation">
  <?php
    wp_nav_menu(array(
      'theme_location' => 'headerMenuLocation'
    ));
		/* 
      函数生成动态菜单，该菜单来自wordpress管理界面设置的菜单
      headerMenuLocation 是在functions.php中注册的菜单名称
      使用动态菜单的好处是在访问菜单项指向的页面时wordpress会给该项
      设置 current-menu-itme class类名，可以给该类名设置单独的样式以突出显示
    */
	?>
</nav>
```

在 WordPress 后台管理界面设置菜单：`外观 => 菜单`

# 其他

## WordPress 函数

所有函数查看：https://developer.wordpress.org/reference/functions/

### `wp_get_post_parent_id()`

`wp_get_post_parent_id($post=null)`

返回帖子的父级ID，`int`或`false`

### `have_posts()`

返回是否有文章需要渲染，一般用在循环文章：

```php
<?php
  while(have_posts()) {
    the_post(); // 准备文章数据
?>
  	<!-- 输出文章标题 -->
  	<h1><?php the_title(); ?></h1>
  	<!-- 输出文章内容 -->
  	<p><?php the_content(); ?></p>
<?php
	}
?>
```

### `the_title()`

`the_title(string $before='', string $after='', bool $display=true): void|string`

输出帖子的标题

`$before` 在标题前拼接内容

`$after` 在标题后拼接内容

`$display` 是否输出到页面上

### `the_content()`

输出文章内容

### `the_excerpt()`

输出文章摘要

### `the_permalink()`

输出帖子的永久链接

### `the_author_posts_link()`

输出当前文章作者名称，点击可以跳转到该作者发布的所有文章的地址

### `the_time()`

输出帖子的发布时间

### `get_the_category_list()`

`get_the_category_list(string $separator='', string $parents='', int $post_id=false): string`

获取当前帖子所有的标签，并以 `$separator` 进行拼接

`$parents` 如何展现父分类。接受 `'multiple'` 、 `'single'` 或空。

### `paginate_links()`

获取博客分页器，需要加上 `echo` 进行输出

当帖子数量不足分页数量时，不会输出分页器

### `site_url()`

返回站点路径的绝对地址，会自动拼接站点部署的域名

### `get_theme_file_uri()`

`get_theme_file_uri(string $file=''):string`

检索并返回主题中文件的 URL

`$file` 的根目录是当前主题文件夹

## 关于 WordPress 内置函数是否输出到页面上

例如以下函数

```php
the_title()
get_the_title()

the_ID()
get_the_id()
```
以 `get` 开头的函数不会输出到页面上，以 `the` 开头的函数会输出到页面上