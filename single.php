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
          href="<?php echo site_url('/blog'); ?>"
        >
          <i class="fa fa-home" aria-hidden="true"></i>
          博客主页
        </a>
        <span class="metabox__main">
          由
          <?php the_author_posts_link(); ?>
          于
          <?php the_time('Y年m月d日 H:i'); ?>
          发布在
          <?php echo get_the_category_list(', '); ?>
          中
        </span>
      </p>
    </div>

    <div class="generic-content">
      <?php the_content(); ?>
    </div>
  </div>
<?php
  get_footer();
?>