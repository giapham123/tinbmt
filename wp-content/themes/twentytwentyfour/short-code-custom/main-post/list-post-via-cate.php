<?php
function category_news_shortcode($atts) {
  $atts = shortcode_atts([
    'category' => '',
    'count'    => 4,
  ], $atts, 'category_news');

  ob_start();

  // Get one or all categories
  $categories = [];
  if (!empty($atts['category'])) {
    $category = get_category_by_slug($atts['category']);
    if (!$category) $category = get_category((int)$atts['category']);
    if ($category) $categories[] = $category;
  } else {
    $categories = get_categories(['orderby' => 'name', 'order' => 'ASC']);
  }

  foreach ($categories as $category) :
    ?>
    <div class="category-section">
      <!-- <h2 class="cat-title"><?php echo esc_html($category->name); ?></h2> -->

      <?php
      $posts = new WP_Query([
        'cat' => $category->term_id,
        'posts_per_page' => intval($atts['count'])
      ]);

      if ($posts->have_posts()) :
        $count = 0;
        ?>
        <div class="category-news-wrapper">
          <?php while ($posts->have_posts()) : $posts->the_post(); ?>
            <?php if ($count == 0) : ?>
              <!-- Main big post (left) -->
              <div class="main-post">
                <a href="<?php the_permalink(); ?>" class="main-link">
                  <div class="main-thumb">
                    <?php if (has_post_thumbnail()) the_post_thumbnail('large'); ?>
                  </div>
                  <h3 class="main-title"><?php the_title(); ?></h3>
                  <p class="main-excerpt"><?php echo wp_trim_words(get_the_excerpt(), 35, '...'); ?></p>
                </a>
              </div>
              <div class="side-posts">
            <?php else : ?>
                <!-- Small posts (right side) -->
                <div class="side-item">
                  <div class="side-thumb">
                    <a href="<?php the_permalink(); ?>">
                      <?php if (has_post_thumbnail()) the_post_thumbnail('thumbnail'); ?>
                    </a>
                  </div>
                  <div class="side-info">
                    <a href="<?php the_permalink(); ?>" class="side-title"><?php the_title(); ?></a>
                    
                  </div>
                </div>
            <?php endif; ?>
          <?php $count++; endwhile; ?>
          </div><!-- /.side-posts -->
        </div><!-- /.category-news-wrapper -->
        <?php
        wp_reset_postdata();
      endif;
      ?>
    </div>
  <?php
  endforeach;

  return ob_get_clean();
}
add_shortcode('category_news', 'category_news_shortcode');


// === CSS Styling ===
function category_news_styles() {
  ?>
  <style>
    .category-section {
      margin-bottom: 50px;
      padding-bottom: 0px;
      border-bottom: 0px solid #eee;
    }

    .cat-title {
      font-size: 22px;
      font-weight: 700;
      color: #222;
      border-left: 4px solid #e63946;
      padding-left: 10px;
      margin-bottom: 20px;
      text-transform: uppercase;
    }

    /* Layout wrapper */
    .category-news-wrapper {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      gap: 10px;
      flex-wrap: wrap;
    }

    /* === LEFT MAIN POST === */
    .main-post {
      flex: 1 1 55%;
    }

    .main-link {
      text-decoration: none;
      color: inherit;
    }

    .main-thumb img {
      width: 100%;
      /* height: 240px; smaller image */
      height:auto;
      object-fit: cover;
      border-radius: 6px;
      display: block;
    }

    .main-title {
      font-size: 19px;
      font-weight: 700;
      margin: 10px 0 6px;
      color: #222;
      line-height: 1.4em;
      transition: color 0.3s ease;
    }

    .main-title:hover {
      color: #e63946;
    }

    .main-excerpt {
      font-size: 15px;
      color: #555;
      line-height: 1.6;
      margin-top: 5px;
    }

    /* === RIGHT SMALL POSTS === */
    .side-posts {
      flex: 1 1 40%;
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .side-item {
      display: flex;
      align-items: flex-start;
      gap: 10px;
      border-bottom: 0px solid #f5f5f5;
      padding-bottom: 10px;
    }

    .side-thumb img {
      width: 90px;
      height: 70px;
      object-fit: cover;
      border-radius: 4px;
      flex-shrink: 0;
      display: block;
    }

    .side-info {
      flex: 1;
      display: flex;
      align-items: center;
    }

    .side-title {
      font-size: 15px;
      font-weight: 600;
      color: #333;
      text-decoration: none;
      line-height: 1.4;
      transition: color 0.3s ease;
    }

    .side-title:hover {
      color: #e63946;
    }

    /* Responsive design */
    @media (max-width: 768px) {
      .category-news-wrapper {
        flex-direction: column;
      }

      .main-post,
      .side-posts {
        flex: 1 1 100%;
      }

      .main-thumb img {
        height: 200px;
      }

      .side-thumb img {
        width: 80px;
        height: 60px;
      }
    }
  </style>
  <?php
}
add_action('wp_head', 'category_news_styles');
