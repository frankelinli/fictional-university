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
          href="<?php echo get_post_type_archive_link('event'); ?>"
        >
          <i class="fa fa-home" aria-hidden="true"></i>
          所有活动
        </a>
        <span class="metabox__main"><?php the_title(); ?></span>
      </p>
    </div>

    <div class="generic-content">
      <?php the_content(); ?>
    </div>

    <?php
      $relatedPrograms = get_field('related_programs');
      if ($relatedPrograms) {
        // 如果活动绑定了相关学科，则显示相关学科的列表
    ?>
        <hr class="section-break">
        <h2 class="headline headline--medium">相关学科</h2>
        <ul class="link-list min-list">
          <?php
            foreach($relatedPrograms as $program) {
          ?>
              <li>
                <a href="<?php echo get_the_permalink($program->ID); ?>">
                  <?php echo $program->post_title; ?>
                </a>
              </li>
          <?php
            }
          ?>
        </ul>
    <?php
      }
    ?>
  </div>
<?php
  get_footer();
?>