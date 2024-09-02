<?php
  get_header();
  pageBanner([
    'title' => '过去的活动',
    'subtitle' => '回顾我们过去的活动'
  ]);
  ?>

  <div class="container container--narrow page-section">
    <?php
      $today = date('Ymd');
      $pastEvents = new WP_Query([
        'paged' => get_query_var('paged', 1), // 获取url查询页码，默认值设置为1
        'post_type' => 'event',
        'meta_key' => 'event_date', // 自定义字段名
        'orderby' => 'meta_value_num', // 通过自定义字段的值来排序
        'order' => 'DESC', // 降序
        'meta_query' => [ // 多条件查询
          [
            'key' => 'event_date', // 字段名
            'compare' => '<', // 比较符
            'value' => $today, // 比较值
            'type' => 'numeric' // 指定类型为数值
          ]
        ]
      ]);
      while ($pastEvents->have_posts()) {
        $pastEvents->the_post();
        get_template_part('template-parts/content', 'event');
      }
      echo paginate_links([
        'total' => $pastEvents->max_num_pages // 将自定义查询得到的所有页面数量传给分页器
      ]); // 输出分页器
      wp_reset_postdata();
      ?>
  </div>

<?php
  get_footer();
?>