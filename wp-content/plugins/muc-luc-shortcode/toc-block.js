jQuery(document).ready(function ($) {
    // Toggle TOC collapse
    $('.toc-toggle').on('click', function () {
        var $list = $(this).closest('.toc-block').find('.toc-list');
        $list.slideToggle(300);

        // Đổi icon +/−
        $(this).text($(this).text() === '−' ? '+' : '−');
    });

    // Smooth scroll with offset 153px
    $('.toc-item a').on('click', function (e) {
        e.preventDefault();
        var target = $($(this).attr('href'));
        if (target.length) {
            $('html, body').animate({
                scrollTop: target.offset().top - 190
            }, 500);
        }
    });

    // Highlight active item on scroll
    $(window).on('scroll', function () {
    var scrollPos = refElement.position().top; // your offset
    $('.toc-item a').each(function () {
        var currLink = $(this);
        var refElement = $(currLink.attr('href'));
        if (refElement.length &&
            refElement.offset().top <= scrollPos &&
            refElement.offset().top + refElement.outerHeight() > scrollPos) {
            $('.toc-item a').removeClass('active');
            currLink.addClass('active');
        }
    });
});

});
