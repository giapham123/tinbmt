jQuery(document).ready(function($) {
    const maxPerCol = 6;

    $('.vnexpress-main-menu ul.sub-menu').each(function() {
        const $submenu = $(this);
        const $items = $submenu.children('li');

        if ($items.length > maxPerCol) {
            $submenu.addClass('multi-columns');

            // Tạo các cột nhưng không thay đổi display
            const numCols = Math.ceil($items.length / maxPerCol);
            const $cols = [];

            for (let i = 0; i < numCols; i++) {
                const $col = $('<div class="submenu-col"></div>');
                $items.slice(i * maxPerCol, (i + 1) * maxPerCol).appendTo($col);
                $cols.push($col);
            }

            $submenu.empty().append($cols);
        }
    });

    // Hover fallback không cần add class, CSS đã handle
});
