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
?>