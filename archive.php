<?php
  get_header();
  pageBanner([
    'title' => get_the_archive_title(),
    'subtitle' => get_the_archive_description()
  ]);
?>

  <div class="container container--narrow page-section">
    <?php
      while(have_posts()) {
        the_post();
    ?>
        <div class="post-item">
          <!-- 显示文章标题 -->
          <h2 class="headline headline--medium headline--post-title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
          </h2>

          <!-- 显示作者、发布日期、类型 -->
          <div class="metabox">
            <p>
              由
              <?php the_author_posts_link(); ?>
              于
              <?php the_time('Y年m月d日 H:i'); ?>
              发布在
              <?php echo get_the_category_list(', '); ?>
              中
            </p>
          </div>

          <!-- 显示摘要 -->
          <div class="generic-content">
            <?php the_excerpt(); ?>
            <p><a class="btn btn--blue" href="<?php the_permalink(); ?>">阅读更多 &raquo;</a></p>
          </div>
        </div>
    <?php
      }
      echo paginate_links(); // 输出分页器
    ?>
  </div>

<?php
  get_footer();
?>