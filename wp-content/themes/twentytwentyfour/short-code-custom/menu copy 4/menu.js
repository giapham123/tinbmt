jQuery(document).ready(function($) {

    const maxPerCol = 6;

    // Multi-column submenu for desktop
    $('.vnexpress-main-menu ul.sub-menu').each(function() {
        const $submenu = $(this);
        const $items = $submenu.children('li');

        if ($items.length > maxPerCol) {
            $submenu.addClass('multi-columns');
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

    // Hamburger toggle
    $('.vnexpress-hamburger').on('click', function() {
        $('.vnexpress-mobile-menu-wrapper').toggleClass('open');
        $('body').toggleClass('menu-open'); // disable body scroll
    });

    // Mobile submenu toggle
    $('.vnexpress-main-menu li.menu-item-has-children > a').on('click', function(e) {
        if ($(window).width() <= 1024) {
            e.preventDefault();
            const $submenu = $(this).next('.sub-menu');
            $submenu.slideToggle(200, function() {
                $submenu.css('max-height', 'calc(100vh - 100px)');
            });
        }
    });

});
