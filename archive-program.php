<?php
  get_header();
  pageBanner(
    [
      'title' => '所有学科',
      'subtitle' => '有适合每个人的东西，随便看看。'
    ]
  );
  ?>

  <div class="container container--narrow page-section">
    <ul class="link-list min-list">
      <?php
      while (have_posts()) {
          the_post();
        ?>
          <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
        <?php
      }
        echo paginate_links(); // 输出分页器
      ?>
    </ul>
  </div>

<?php
  get_footer();
?>
