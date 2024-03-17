<?php
  get_header();
?>

  <div class="page-banner">
    <div
      class="page-banner__bg-image"
      style="background-image: url(<?php echo get_theme_file_uri('/images/ocean.jpg'); ?>)"
    ></div>
    <div class="page-banner__content container container--narrow">
      <h1 class="page-banner__title">过去的活动</h1>
      <div class="page-banner__intro">
        <p>回顾我们过去的活动</p>
      </div>
    </div>
  </div>

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
      while($pastEvents->have_posts()) {
        $pastEvents->the_post();
    ?>
        <div class="event-summary">
          <a class="event-summary__date t-center" href="<?php the_permalink(); ?>">
            <span class="event-summary__month"><?php
              $eventDate = new DateTime(get_field('event_date'));
              echo $eventDate->format('n月');
            ?></span>
            <span class="event-summary__day"><?php echo $eventDate->format('d'); ?></span>
          </a>
          <div class="event-summary__content">
            <h5 class="event-summary__title headline headline--tiny"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>
            <p><?php echo wp_trim_words(get_the_content(), 100); ?><a href="<?php the_permalink(); ?>" class="nu gray">了解更多</a></p>
          </div>
        </div>
    <?php
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