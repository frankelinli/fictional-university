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
  </div>
<?php
  get_footer();
?>