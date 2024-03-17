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
    // 标题通过wordpress生成
    add_theme_support('title-tag');
  }

  add_action('after_setup_theme', 'university_features');

  // 修改存档页面默认查询配置
  function university_adjust_queries($query) {
    // 如果当前是存档页面，且是事件存档页面，且是主查询，则修改查询条件
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
  }

  // 在查询前执行函数
  add_action('pre_get_posts', 'university_adjust_queries');
?>