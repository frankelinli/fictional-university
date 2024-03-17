<?php
  get_header();
?>

  <div class="page-banner">
    <div
      class="page-banner__bg-image"
      style="background-image: url(<?php echo get_theme_file_uri('/images/ocean.jpg'); ?>)"
    ></div>
    <div class="page-banner__content container container--narrow">
      <h1 class="page-banner__title">所有活动</h1>
      <div class="page-banner__intro">
        <p>了解我们的世界正在发生什么</p>
      </div>
    </div>
  </div>

  <div class="container container--narrow page-section">
    <?php
      while(have_posts()) {
        the_post();
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
            <p><?php echo wp_trim_words(get_the_content(), 100); ?><a href="<?php the_permalink(); ?>" class="nu gray">了解更多</a></p>
          </div>
        </div>
    <?php
      }
      echo paginate_links(); // 输出分页器
    ?>
    
    <hr class="section-break">

    <p>想回顾一下过去的活动吗？<a href="<?php echo site_url('/past-events'); ?>">查看我们过去的活动档案</a>。</p>
  </div>

<?php
  get_footer();
?>