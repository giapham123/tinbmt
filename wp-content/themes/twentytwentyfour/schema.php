<?php
/**
 * Schema for News Website (Tin BMT)
 * Author default: GiaPham
 */

/**
 * Helper: logo publisher
 */
function tinbmt_get_publisher_logo() {
    $logo = get_site_icon_url();
    if ($logo) return $logo;

    // fallback logo (nên thay bằng logo thật)
    return get_template_directory_uri() . '/assets/logo.webp';
}

/**
 * 1️⃣ ORGANIZATION + WEBSITE (Homepage)
 */
add_action('wp_head', function () {
    if (!is_front_page()) return;
?>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Organization",
  "@id": "https://tinbmt.com/#organization",
  "name": "Tin BMT",
  "url": "https://tinbmt.com",
  "logo": {
    "@type": "ImageObject",
    "url": "<?php echo esc_url( tinbmt_get_publisher_logo() ); ?>",
    "width": 600,
    "height": 60
  }
}
</script>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebSite",
  "@id": "https://tinbmt.com/#website",
  "url": "https://tinbmt.com",
  "name": "Tin BMT",
  "publisher": {
    "@id": "https://tinbmt.com/#organization"
  }
}
</script>
<?php
});

/**
 * 2️⃣ NEWS ARTICLE (Single Post)
 */
add_action('wp_head', function () {
    if (!is_single()) return;

    global $post;

    $image = get_the_post_thumbnail_url($post, 'full');
    if (!$image) {
        $image = tinbmt_get_publisher_logo();
    }

    $data = [
        "@context" => "https://schema.org",
        "@type" => "NewsArticle",
        "mainEntityOfPage" => [
            "@type" => "WebPage",
            "@id" => get_permalink($post)
        ],
        "headline" => get_the_title($post),
        "image" => [$image],
        "datePublished" => get_the_date('c', $post),
        "dateModified" => get_the_modified_date('c', $post),
        "author" => [
            "@type" => "Person",
            "@id" => home_url('/tac-gia/giapham#person'),
            "name" => "GiaPham"
        ],
        "publisher" => [
            "@id" => "https://tinbmt.com/#organization"
        ]
    ];

    echo '<script type="application/ld+json">'
        . wp_json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        . '</script>';
});

/**
 * 3️⃣ PERSON (Author Page)
 */
add_action('wp_head', function () {
    if (!is_author()) return;

    $author = get_queried_object();
?>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Person",
  "@id": "<?php echo esc_url( get_author_posts_url($author->ID) ); ?>#person",
  "name": "<?php echo esc_js( $author->display_name ); ?>",
  "url": "<?php echo esc_url( get_author_posts_url($author->ID) ); ?>",
  "worksFor": {
    "@id": "https://tinbmt.com/#organization"
  }
}
</script>
<?php
});

/**
 * 4️⃣ BREADCRUMB (Category + Single)
 */
add_action('wp_head', function () {
    if (!is_single() && !is_category()) return;

    $items = [];
    $position = 1;

    $items[] = [
        "@type" => "ListItem",
        "position" => $position++,
        "name" => "Trang chủ",
        "item" => home_url('/')
    ];

    if (is_category()) {
        $cat = get_queried_object();
        $items[] = [
            "@type" => "ListItem",
            "position" => $position++,
            "name" => $cat->name,
            "item" => get_category_link($cat)
        ];
    }

    if (is_single()) {
        $cats = get_the_category();
        if ($cats) {
            $items[] = [
                "@type" => "ListItem",
                "position" => $position++,
                "name" => $cats[0]->name,
                "item" => get_category_link($cats[0])
            ];
        }

        $items[] = [
            "@type" => "ListItem",
            "position" => $position,
            "name" => get_the_title()
        ];
    }
?>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": <?php echo wp_json_encode($items, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>
}
</script>
<?php
});
