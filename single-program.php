<?php
  get_header();
  // 将当前文章设置为全局变量
  the_post();
?>
    
  <div class="page-banner">
    <div
      class="page-banner__bg-image"
      style="background-image: url(<?php echo get_theme_file_uri('/images/ocean.jpg'); ?>)"
    ></div>
    <div class="page-banner__content container container--narrow">
      <h1 class="page-banner__title"><?php the_title(); ?></h1>
      <div class="page-banner__intro">
        <p>不要忘记在稍后替换我。</p>
      </div>
    </div>
  </div>

  <div class="container container--narrow page-section">
    <div class="metabox metabox--position-up metabox--with-home-link">
      <p>
        <a
          class="metabox__blog-home-link"
          href="<?php echo get_post_type_archive_link('program'); ?>"
        >
          <i class="fa fa-home" aria-hidden="true"></i>
          所有学科
        </a>
        <span class="metabox__main"><?php the_title(); ?></span>
      </p>
    </div>

    <div class="generic-content">
      <?php the_content(); ?>
    </div>

    <?php
      $today = date('Ymd');
      $relatedEvents = new WP_Query([
        'post_type' => 'event',
        'meta_key' => 'event_date',
        'orderby' => 'meta_value_num', // 通过自定义字段的值来排序
        'order' => 'ASC',
        'meta_query' => [ // 多条件查询
          // 筛选尚未过去的活动
          [
            'key' => 'event_date',
            'compare' => '>=',
            'value' => $today,
            'type' => 'numeric'
          ],
          // 筛选当前学科的活动
          [
            'key' => 'related_programs',
            'compare' => 'LIKE',
            // wordpress数据库存储的是序列化之后的数组，类似 a:2:{i:0;s:2:"60";i:1;s:2:"59";}
            // 因此需要加上双引号来模糊匹配
            'value' => '"' . get_the_ID() . '"'
          ]
        ]
      ]);

      if ($relatedEvents->have_posts()) {
        echo '<hr class="section-break">';
        echo '<h2 class="headline headline--medium">即将开始的' . get_the_title() . '活动</h2>';

        while ($relatedEvents->have_posts()) {
          $relatedEvents->the_post();
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
              <p><?php
                if (has_excerpt()) {
                  echo get_the_excerpt(); // 如果有摘要，则显示摘要
                } else {
                  echo wp_trim_words(get_the_content(), 36); // 否则显示内容的前36个字符
                }
              ?><a href="<?php the_permalink(); ?>" class="nu gray">了解更多</a></p>
            </div>
          </div>
    <?php
        }
      }
      wp_reset_postdata();
    ?>
  </div>
<?php
  get_footer();
?>