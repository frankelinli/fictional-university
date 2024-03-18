<?php
  get_header();
?>
  
  <div class="page-banner">
    <div
      class="page-banner__bg-image"
      style="background-image: url(<?php echo get_theme_file_uri('/images/library-hero.jpg'); ?>)"
    ></div>
    <div class="page-banner__content container t-center c-white">
      <h1 class="headline headline--large">欢迎!</h1>
      <h2 class="headline headline--medium">我们认为你会喜欢这里。</h2>
      <h3 class="headline headline--small">为什么不看看你感兴趣的<strong>专业</strong>呢？</h3>
      <a href="<?php echo get_post_type_archive_link('program'); ?>" class="btn btn--large btn--blue">寻找你的专业</a>
    </div>
  </div>

  <div class="full-width-split group">
    <div class="full-width-split__one">
      <div class="full-width-split__inner">
        <h2 class="headline headline--small-plus t-center">即将举行的活动</h2>
        <?php
          $today = date('Ymd');
          $homepageEvents = new WP_Query([
            'posts_per_page' => 2,
            'post_type' => 'event',
            'meta_key' => 'event_date', // 自定义字段名
            'orderby' => 'meta_value_num', // 通过自定义字段的值来排序
            'order' => 'ASC', // 升序
            'meta_query' => [ // 多条件查询
              [
                'key' => 'event_date', // 字段名
                'compare' => '>=', // 比较符
                'value' => $today, // 比较值
                'type' => 'numeric' // 指定类型为数值
              ]
            ]
          ]);

          while ($homepageEvents->have_posts()) {
            $homepageEvents->the_post();
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
                <p><?php
                  if (has_excerpt()) {
                    echo get_the_excerpt(); // 如果有摘要，则显示摘要
                  } else {
                    echo wp_trim_words(get_the_content(), 36); // 否则显示内容的前36个字符
                  }
                ?><a href="<?php the_permalink(); ?>" class="nu gray">了解更多</a></p>
              </div>
            </div>
        <?php
          }
          wp_reset_postdata();
        ?>

        <p class="t-center no-margin"><a href="<?php echo get_post_type_archive_link('event'); ?>" class="btn btn--blue">查看所有活动</a></p>
      </div>
    </div>
    <div class="full-width-split__two">
      <div class="full-width-split__inner">
        <h2 class="headline headline--small-plus t-center">来自我们的博客</h2>
        <?php
          $homepagePosts = new WP_Query(array(
            'posts_per_page' => 2, // 指定每页显示的博客数量
            'post_type' => 'post', // 指定博客类型为文章
            'post_status' => 'publish', // 指定博客状态'
          ));

          while($homepagePosts->have_posts()) {
            $homepagePosts->the_post();
        ?>
            <div class="event-summary">
              <a class="event-summary__date event-summary__date--beige t-center" href="<?php the_permalink(); ?>">
                <span class="event-summary__month"><?php the_time('M'); ?></span>
                <span class="event-summary__day"><?php the_time('d'); ?></span>
              </a>
              <div class="event-summary__content">
                <h5 class="event-summary__title headline headline--tiny">
                  <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </h5>
                <p><?php
                  if (has_excerpt()) {
                    echo get_the_excerpt(); // 如果有摘要，则显示摘要
                  } else {
                    echo wp_trim_words(get_the_content(), 36); // 否则显示内容的前36个字符
                  }
                ?><a href="<?php the_permalink(); ?>" class="nu gray">阅读更多</a></p>
              </div>
            </div>
        <?php
          }
          wp_reset_postdata(); // 重置文章数据
        ?>

        <p class="t-center no-margin"><a href="<?php echo site_url('/blog'); ?>" class="btn btn--yellow">查看所有博客文章</a></p>
      </div>
    </div>
  </div>

  <div class="hero-slider">
    <div data-glide-el="track" class="glide__track">
      <div class="glide__slides">
        <div class="hero-slider__slide" style="background-image: url(<?php echo get_theme_file_uri('/images/bus.jpg'); ?>)">
          <div class="hero-slider__interior container">
            <div class="hero-slider__overlay">
              <h2 class="headline headline--medium t-center">免费交通</h2>
              <p class="t-center">所有学生均可免费无限次搭乘巴士。</p>
              <p class="t-center no-margin"><a href="#" class="btn btn--blue">了解更多</a></p>
            </div>
          </div>
        </div>
        <div class="hero-slider__slide" style="background-image: url(<?php echo get_theme_file_uri('/images/apples.jpg'); ?>)">
          <div class="hero-slider__interior container">
            <div class="hero-slider__overlay">
              <h2 class="headline headline--medium t-center">一天一颗苹果</h2>
              <p class="t-center">我们的牙科计划建议吃苹果。</p>
              <p class="t-center no-margin"><a href="#" class="btn btn--blue">了解更多</a></p>
            </div>
          </div>
        </div>
        <div class="hero-slider__slide" style="background-image: url(<?php echo get_theme_file_uri('/images/bread.jpg'); ?>)">
          <div class="hero-slider__interior container">
            <div class="hero-slider__overlay">
              <h2 class="headline headline--medium t-center">免费食物</h2>
              <p class="t-center">虚构大学为有需要的人提供午餐计划。</p>
              <p class="t-center no-margin"><a href="#" class="btn btn--blue">了解更多</a></p>
            </div>
          </div>
        </div>
      </div>
      <div class="slider__bullets glide__bullets" data-glide-el="controls[nav]"></div>
    </div>
  </div>

<?php
  get_footer();
?>