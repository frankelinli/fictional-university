<?php
  define( 'FIC_UNI_PATH', plugin_dir_path( __FILE__ ) );
  include FIC_UNI_PATH . 'includes/dotenv.php';

  // banner模块
  function pageBanner($args=[]) {
    if (empty($args['title'])) {
      $args['title'] = get_the_title();
    }

    if (empty($args['subtitle'])) {
      $args['subtitle'] = get_field('page_banner_subtitle');
    }

    if (empty($args['photo'])) {
      if (get_field('page_banner_background_image')) {
        $args['photo'] = get_field('page_banner_background_image')['sizes']['页面顶部栏'];
      } else {
        $args['photo'] = get_theme_file_uri('/images/ocean.jpg');
      }
    }
?>
    <div class="page-banner">
      <div
        class="page-banner__bg-image"
        style="background-image: url(<?php echo $args['photo']; ?>)"
      ></div>
      <div class="page-banner__content container container--narrow">
        <h1 class="page-banner__title"><?php echo $args['title']; ?></h1>
        <div class="page-banner__intro">
          <p><?php echo $args['subtitle']; ?></p>
        </div>
      </div>
    </div>
<?php
  }


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
    
    // 加载百度地图api
    wp_enqueue_script('baiduMap', '//api.map.baidu.com/api?v=1.0&type=webgl&callback=initBaiduMapFunction&ak=H4pCsPnqtBTQ9JHRgp1ARvwUIX6QbQtr' . getenv('BAIDU_MAP_KEY'), null, '1.0.0', true);
  }

  // 添加加载样式表的钩子
  add_action('wp_enqueue_scripts', 'university_files');

  
  // 添加自定义的meta信息
  function university_features() {
    // 标题通过wordpress生成
    add_theme_support('title-tag');
    // 启用文章特色图
    add_theme_support('post-thumbnails');
    // 图像尺寸设置：尺寸名（无特殊要求）, 图像宽, 图像高, 是否裁切（默认根据中心裁切）
    // 第四个参数可以传入数组，来控制裁切中心：array('left', 'top')
    add_image_size('教授横向缩略图', 400, 260, true);
    add_image_size('教授纵向缩略图', 480, 650, true);
    add_image_size('页面顶部栏', 1500, 350, true);
  }

  add_action('after_setup_theme', 'university_features');

  // 修改存档页面默认查询配置
  function university_adjust_queries($query) {
    // 如果当前不是管理员界面，且是事件存档页面，且是主查询，则修改查询条件
    if (!is_admin() and is_post_type_archive('event') and $query->is_main_query()) {
      $today = date('Ymd');
      
      $query->set('meta_key', 'event_date'); // 自定义字段名
      $query->set('orderby', 'meta_value_num'); // 通过自定义字段的值来排序
      $query->set('order', 'ASC'); // 升序
      $query->set('meta_query', [ // 多条件查询
        [
          'key' => 'event_date', // 字段名
          'compare' => '>=', // 比较符
          'value' => $today, // 比较值
          'type' => 'numeric' // 指定类型为数值
        ]
      ]);
    }
    
    // 如果当前不是管理员界面，且是学科存档页面，且是主查询，则修改查询条件
    if (!is_admin() and is_post_type_archive('program') and $query->is_main_query()) {
      $query->set('posts_per_page', -1); // 不限制每页大小
      $query->set('orderby', 'title');
      $query->set('order', 'ASC');
    }
    
    // 如果当前不是管理员界面，且是校区存档页面，且是主查询，则修改查询条件
    if (!is_admin() and is_post_type_archive('campus') and $query->is_main_query()) {
      $query->set('posts_per_page', -1); // 不限制返回的校区数量
    }
  }

  // 在查询前执行函数
  add_action('pre_get_posts', 'university_adjust_queries');

  // 获取主题目录对应的url路径
  function fic_uni_get_url( $filename = '' ) {
    if ( ! defined( 'FIC_UNI_URL' ) ) {
      define( 'FIC_UNI_URL', get_template_directory_uri() );
    }
    return FIC_UNI_URL . ltrim( $filename, '/' );
  }

  // 获取主题目录对应的path路径
  function fic_uni_get_path( $filename = '' ) {
    if ( ! defined( 'FIC_UNI_PATH' ) ) {
      define( 'FIC_UNI_PATH', plugin_dir_path( __FILE__ ) );
    }
    return FIC_UNI_PATH . ltrim( $filename, '/' );
  }

  // 设置百度地图API密钥
  add_filter('acf/fields/baidu_map/api', function($api) {
    $api['ak'] = getenv('BAIDU_MAP_API_KEY');
    return $api;
  });

  // 使用ACF钩子添加百度地图的字段类型
  include_once FIC_UNI_PATH . "includes/class-acf-field-baidu-map.php";
?>