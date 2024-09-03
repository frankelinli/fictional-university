<div class="post-item">
  <!-- 显示文章标题 -->
  <h2 class="headline headline--medium headline--post-title">
    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
  </h2>

  <!-- 显示摘要 -->
  <div class="generic-content">
  <?php the_excerpt(); ?>
    <p><a class="btn btn--blue" href="<?php the_permalink(); ?>">查看学科 &raquo;</a></p>
  </div>
</div>