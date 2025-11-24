<?php
// === Hiển thị tất cả chuyên mục kèm mô tả (loại trừ một số chuyên mục) ===
function show_all_chuyen_muc() {
  // 🔸 Nhập slug của các category bạn muốn loại trừ
  $excluded_slugs = array('chinh-sach-moi', 'ban-an','qa','nghien-cuu','thu-tuc-hanh-chinh-online','bieu-mau','van-ban-phap-luat','hoi-dap','tin-noi-bat');

  // Lấy danh sách tất cả chuyên mục
  $categories = get_categories(array(
    'hide_empty' => false,
  ));

  // Lọc ra những chuyên mục KHÔNG nằm trong danh sách loại trừ
  $categories = array_filter($categories, function($cat) use ($excluded_slugs) {
    return !in_array($cat->slug, $excluded_slugs);
  });

  ob_start();

  if (!empty($categories)) {
    echo '<div style="display: flex; flex-wrap: wrap; gap: 20px; margin: 30px 0;">';
    foreach ($categories as $cat) {
      // 🔹 Tạo link tùy chỉnh (không có /category/)
      $custom_link = home_url('/' . $cat->slug . '/');

      echo '<div style="flex: 1 1 250px; border: 1px solid #ddd; border-radius: 10px; padding: 20px; background: #fafafa; transition: all 0.3s ease;">';
      echo '<h3 style="color: #0073aa; margin-bottom: 10px;">';
      echo '<a href="' . esc_url($custom_link) . '" style="text-decoration: none; color: inherit;">' . esc_html($cat->name) . '</a>';
      echo '</h3>';
      echo '<p style="color: #555; font-size: 14px;">' . esc_html($cat->description ?: 'Chưa có mô tả.') . '</p>';
      echo '</div>';
    }
    echo '</div>';
  } else {
    echo '<p>Không có chuyên mục nào để hiển thị.</p>';
  }

  return ob_get_clean();
}

// === Đăng ký shortcode ===
add_shortcode('chuyen_muc_list', 'show_all_chuyen_muc');
