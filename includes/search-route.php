<?php

add_action('rest_api_init', 'universityRegisterSearch');

function universityRegisterSearch()
{
  // 注册新的路由
  // 参数1：路由命名空间（第一段path）
  // 参数2：路由
  register_rest_route('university/v1', 'search', [
    'methods' => WP_REST_SERVER::READABLE, // WordPress 常量，表示 GET
    'callback' => 'universitySearchResults'
  ]);
}

function universitySearchResults($data)
{
  $results = [
    'generalInfo' => [],
    'professors' => [],
    'programs' => [],
    'events' => [],
    'campuses' => [],
  ];


  // 多类型查询
  $mainQuery = new WP_Query([
    'post_type' => ['post', 'page', 'professor', 'program', 'event', 'campus'],
    's' => sanitize_text_field($data['s']), // 搜索字段
  ]);

  while ($mainQuery->have_posts()) {
    $mainQuery->the_post();
    $thisPostData = [
      'title' => get_the_title(),
      'permalink' => get_the_permalink(),
    ];

    if (get_post_type() === 'post' || get_post_type() === 'page') {
      $thisPostData['postType'] = get_post_type();
      $thisPostData['authorName'] = get_the_author();
      array_push($results['generalInfo'], $thisPostData);
    } elseif (get_post_type() === 'professor') {
      $thisPostData['image'] = get_the_post_thumbnail_url(0, '教授横向缩略图');
      array_push($results['professors'], $thisPostData);
    } elseif (get_post_type() === 'program') {
      // 存储学科ID，用来查询关联的教授
      $thisPostData['id'] = get_the_ID();
      array_push($results['programs'], $thisPostData);

      // 获取与该学科关联的校区
      $programRelationCampuses = get_field('related_campus');

      foreach ($programRelationCampuses as $campus) {
        array_push($results['campuses'], [
          'title' => get_the_title($campus->ID),
          'permalink' => get_the_permalink($campus->ID),
        ]);
      }
    } elseif (get_post_type() === 'event') {
      $thisPostData['date'] = get_field('event_date');
      // 有摘要就显示摘要，否则显示内容的前36个字符
      $thisPostData['excerpt'] = has_excerpt() ? get_the_excerpt() : wp_trim_words(get_the_content(), 36);
      array_push($results['events'], $thisPostData);
    } elseif (get_post_type() === 'campus') {
      array_push($results['campuses'], $thisPostData);
    }
  }

  // 如果搜索结果包含学科，则查询学科关联的教授和活动
  if ($results['programs']) {
    // 指示查询的关系，只要有一个条件满足即可，默认为 AND
    $programMetaQuery = ['relation' => 'OR'];

    foreach ($results['programs'] as $item) {
      array_push($programMetaQuery, [
        'key' => 'related_programs',
        'compare' => 'LIKE',
        'value' => '"' . $item['id'] . '"'
      ]);
    }

    // 查询学科关联的教授和活动
    $programRelationshipQuery = new WP_Query([
      'post_type' => ['professor', 'event'],
      'meta_query' => $programMetaQuery,
    ]);

    while ($programRelationshipQuery->have_posts()) {
      $programRelationshipQuery->the_post();
      $thisPostData = [
        'title' => get_the_title(),
        'permalink' => get_the_permalink(),
      ];

      if (get_post_type() === 'professor') {
        $thisPostData['image'] = get_the_post_thumbnail_url(0, '教授横向缩略图');
        array_push($results['professors'], $thisPostData);
      } elseif (get_post_type() === 'event') {
        $thisPostData['date'] = get_field('event_date');
        // 有摘要就显示摘要，否则显示内容的前36个字符
        $thisPostData['excerpt'] = has_excerpt() ? get_the_excerpt() : wp_trim_words(get_the_content(), 36);
        array_push($results['events'], $thisPostData);
      }
    }

    // 去除重复数组项，因为主查询和关联查询可能会返回重复项
    $results['professors'] = array_values(array_unique($results['professors'], SORT_REGULAR));
    $results['events'] = array_values(array_unique($results['events'], SORT_REGULAR));
  }

  // 去除重复的校区，因为多个学科可能都关联了同一个校区
  $results['campuses'] = array_values(array_unique($results['campuses'], SORT_REGULAR));

  return $results;
}
