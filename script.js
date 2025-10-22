(function($) {
    'use strict';
    
    $(document).ready(function() {
        // Smooth scroll to comments
        $('.bp-meta-comments').on('click', function(e) {
            if ($(this).attr('href')) return;
            
            e.preventDefault();
            $('html, body').animate({
                scrollTop: $('#comments').offset().top - 100
            }, 500);
        });
        
        // Share button animations
        $('.bp-share-btn').on('click', function(e) {
            const href = $(this).attr('href');
            if (href && href.indexOf('http') === 0) {
                e.preventDefault();
                window.open(href, 'share', 'width=600,height=400');
            }
        });
    });
    
})(jQuery);

