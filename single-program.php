<?php
  get_header();
  // 将当前文章设置为全局变量
  the_post();
  pageBanner();
?>

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
      // 学科相关教授查询
      $relatedProfessors = new WP_Query([
        'posts_per_page' => -1,
        'post_type' => 'professor',
        'orderby' => 'title',
        'order' => 'ASC',
        'meta_query' => [ // 多条件查询
          // 筛选当前学科的活动
          [
            'key' => 'related_programs',
            'compare' => 'LIKE',
            'value' => '"' . get_the_ID() . '"'
          ]
        ]
      ]);

      if ($relatedProfessors->have_posts()) {
        echo '<hr class="section-break">';
        echo '<h2 class="headline headline--medium">' . get_the_title() . '教授</h2>';
        echo '<ul class="professor-cards">';

        while ($relatedProfessors->have_posts()) {
          $relatedProfessors->the_post();
    ?>
          <li class="professor-card__list-item">
            <a class="professor-card" href="<?php echo get_the_permalink(); ?>">
              <img class="professor-card__image" src="<?php the_post_thumbnail_url('教授横向缩略图'); ?>">
              <span class="professor-card__name"><?php the_title(); ?></span>
            </a>
          </li>
    <?php
        }
        echo '</ul>';
      }
      /**
       * 如果未重置文章数据，则自定义查询学科相关活动所用的 get_the_ID()
       * 将会引用为最后一个教授页面的id，导致查询数据失败，因此在这里需要重置数据
       */
      wp_reset_postdata();

      // 学科相关活动查询
      $today = date('Ymd');
      $relatedEvents = new WP_Query([
        'posts_per_page' => -1,
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
          get_template_part('template-parts/content', 'event');
        }
      }
      wp_reset_postdata();

      $relatedCampuses = get_field('related_campus');
      if ($relatedCampuses) {
        echo '<hr class="section-break">';
        echo '<h2 class="headline headline--medium">这些校区开设' . get_the_title() . '</h2>';
        echo '<ul class="link-list min-list">';
        foreach($relatedCampuses as $campus) {
      ?>
          <li>
            <a href="<?php echo get_the_permalink($campus); ?>">
              <?php echo $campus->post_title; ?>
            </a>
          </li>
      <?php
        }
        echo '</ul>';
      }
    ?>
  </div>
<?php
  get_footer();
?>