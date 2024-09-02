<?php
  get_header();
  pageBanner([
    'title' => '所有活动',
    'subtitle' => '了解我们的世界正在发生什么'
  ]);
  ?>

  <div class="container container--narrow page-section">
    <?php
    while (have_posts()) {
      the_post();
      get_template_part('template-parts/content', 'event');
    }
        echo paginate_links(); // 输出分页器
    ?>
    
    <hr class="section-break">

    <p>想回顾一下过去的活动吗？<a href="<?php echo site_url('/past-events'); ?>">查看我们过去的活动档案</a>。</p>
  </div>

<?php get_footer(); ?>
