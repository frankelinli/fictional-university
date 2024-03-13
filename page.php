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

    <?php
      $self_id = get_the_ID();
      $parent_post_id = wp_get_post_parent_id($self_id);
      if ($parent_post_id) {
    ?>
      <div class="metabox metabox--position-up metabox--with-home-link">
        <p>
          <a
            class="metabox__blog-home-link"
            href="<?php echo get_permalink($parent_post_id); ?>"
          >
            <i class="fa fa-home" aria-hidden="true"></i>
            返回<?php echo get_the_title($parent_post_id); ?>
          </a>
          <span class="metabox__main"><?php echo the_title(); ?></span>
        </p>
      </div>
    <?php } ?>

    <?php
      // 如果页面有父页面或者页面有子页面，则展示子页面菜单
      if ($parent_post_id || get_pages(['child_of' => $self_id])) {
    ?>
      <div class="page-links">
        <h2 class="page-links__title"><a href="<?php echo get_permalink($parent_post_id); ?>"><?php echo get_the_title($parent_post_id); ?></a></h2>
        <ul class="min-list">
          <?php
            // 当前页面有父级页面时，展示父级页面的子页面
            // 否则展示当前页面的子页面
            if ($parent_post_id) {
              $thisChildOf = $parent_post_id;
            } else {
              $thisChildOf = $self_id;
            }

            wp_list_pages(
              array(
                'title_li' => null, // 不显示pages字段
                'child_of' => $thisChildOf,
                'sort_column' => 'menu_order', // 使用页面属性排序
              )
            );
          ?>
        </ul>
      </div>
    <?php } ?>

    <div class="generic-content">
      <?php the_content(); ?>
    </div>
  </div>
    
<?php
  get_footer();
?>