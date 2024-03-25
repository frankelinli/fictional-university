<?php
  get_header();
  // 将当前文章设置为全局变量
  the_post();
  // 加载 banner
  pageBanner();
?>
  <div class="container container--narrow page-section">
    <div class="generic-content">
      <div class="row group">
        <div class="one-third">
          <?php the_post_thumbnail('教授纵向缩略图'); ?>
        </div>
        <div class="two-thirds">
          <?php the_content(); ?>
        </div>
      </div>
    </div>

    <?php
      $relatedPrograms = get_field('related_programs');
      if ($relatedPrograms) {
        // 如果教授绑定了相关学科，则显示相关学科的列表
    ?>
        <hr class="section-break">
        <h2 class="headline headline--medium">所教学科</h2>
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