<?php
  get_header();
  pageBanner(
    [
      'title' => '我们的校区',
      'subtitle' => '我们有几个位置便利的校区。'
    ]
  );
  ?>

  <div class="container container--narrow page-section">
    <div class="acf-map">
      <?php
      while (have_posts()) {
        the_post();
        $mapLocation = get_field('map_location');
        ?>
          <div
            class="marker"
            data-lat="<?php echo $mapLocation['lat']; ?>"
            data-lng="<?php echo $mapLocation['lng']; ?>"
            data-title='<?php echo "<a href=\"" . get_the_permalink() . "\">" . get_the_title() . "</a>"; ?>'
            style="display: none;"
          ><?php echo $mapLocation['address']; ?></div>
      <?php } ?>
    </div>
  </div>

<?php get_footer(); ?>
