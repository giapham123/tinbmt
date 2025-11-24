jQuery(document).ready(function ($) {
    const maxPerCol = 6;

    // Multi-column submenu
    $('.vnexpress-main-menu ul.sub-menu').each(function () {
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

    // Hamburger toggle for mobile
    $('.vnexpress-hamburger').on('click', function () {
        $('.vnexpress-menu-wrapper').slideToggle(300).toggleClass('open');
    });

    // Initialize submenus
    function initSubmenus() {
        if ($(window).width() <= 1024) {
            // Mobile: hide submenus initially
            $('.vnexpress-main-menu li.menu-item-has-children > .sub-menu').hide();
        }
    }

    initSubmenus();

    // Reinitialize on window resize
    $(window).resize(function () {
        initSubmenus();
    });

    // Mobile submenu toggle on click
    $('.vnexpress-main-menu li.menu-item-has-children > a').on('click', function (e) {
        if ($(window).width() <= 1024) {
            e.preventDefault(); // prevent navigation on mobile
            $(this).siblings('.sub-menu').stop(true, true).slideToggle(200);
        }
    });
});
