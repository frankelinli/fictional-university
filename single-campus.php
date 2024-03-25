<?php
  get_header();
  // 将当前文章设置为全局变量
  the_post();
  pageBanner();
  $mapLocation = get_field('map_location');
?>

  <div class="container container--narrow page-section">
    <div class="metabox metabox--position-up metabox--with-home-link">
      <p>
        <a
          class="metabox__blog-home-link"
          href="<?php echo get_post_type_archive_link('campus'); ?>"
        >
          <i class="fa fa-home" aria-hidden="true"></i>
          所有校区
        </a>
        <span class="metabox__main"><?php the_title(); ?></span>
      </p>
    </div>

    <div class="generic-content">
      <?php the_content(); ?>
    </div>

    <div class="acf-map">
      <div
        class="marker"
        data-lat="<?php echo $mapLocation['lat']; ?>"
        data-lng="<?php echo $mapLocation['lng']; ?>"
        data-title="<?php echo get_the_title(); ?>"
        style="display: none;"
      ><?php echo $mapLocation['address']; ?></div>
    </div>

    <?php
      // 校区相关学科查询
      $relatedPrograms = new WP_Query([
        'posts_per_page' => -1,
        'post_type' => 'program',
        'orderby' => 'title',
        'order' => 'ASC',
        'meta_query' => [ // 多条件查询
          // 筛选当前学科的活动
          [
            'key' => 'related_campus',
            'compare' => 'LIKE',
            // wordpress数据库存储的是序列化之后的数组，类似 a:2:{i:0;s:2:"60";i:1;s:2:"59";}
            // 因此需要加上双引号来模糊匹配
            'value' => '"' . get_the_ID() . '"'
          ]
        ]
      ]);

      if ($relatedPrograms->have_posts()) {
    ?>
        <hr class="section-break">
        <h2 class="headline headline--medium">该校区的学科</h2>
        <ul class="link-list min-list">
      <?php
        while ($relatedPrograms->have_posts()) {
          $relatedPrograms->the_post();
      ?>
          <li>
            <a href="<?php echo get_the_permalink(get_the_ID()); ?>">
              <?php the_title(); ?>
            </a>
          </li>
    <?php
        }
        echo '</ul>';
      }
      wp_reset_postdata();
    ?>
  </div>
<?php
  get_footer();
?>