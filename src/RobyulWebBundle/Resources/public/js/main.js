$(function () {
    // Activate tooltips
    $('[data-toggle="tooltip"]').tooltip();
    // Lazy load lazy images
    $("img.lazy").lazyload({
        effect: "fadeIn"
    });
    // Enable grid for background list
    var $backgroundsContainer = $('.backgrounds-container');
    $backgroundsContainer.isotope({
        // options
        itemSelector: '.background-item',
        layoutMode: 'fitRows',
        getSortData: {
            name: '[data-name]'
        },
        sortBy: 'name'
    });
    $backgroundsContainer.imagesLoaded().progress(function () {
        $backgroundsContainer.isotope('layout');
    });
    // Lazy load background images
    $backgroundsContainer.one('layoutComplete', function () {
        $("img.background-lazy").lazyload({
            failure_limit: 100000
        });
    });
    // Filtering for background list
    $('.backgrounds-filter-button-tag a').click(function (e) {
        e.preventDefault();
        var tag = $(this).data('tag');

        if (tag.toString() === ''.toString()) {
            $('.backgrounds-container').isotope({
                filter: function () {
                    return true
                }
            })
        } else {
            $('.backgrounds-container').isotope({
                filter: function () {
                    var result = false;
                    jQuery.each($(this).data('tags').split(","), function (i, item) {
                        if (item.toString() === tag.toString()) {
                            result = true;
                            return
                        }
                    });
                    return result
                }
            })
        }
    });
    // Enable profile iframe hovers
    $('.profile-hover').each(function () {
        var $tileItem = $(this);
        var iframeUrl = $tileItem.data('profile-iframe-url');

        $tileItem.popover({
            content: "<iframe width=\"400\" height=\"300\" frameborder=\"0\" scrolling=\"no\" src=\"" + iframeUrl + "\"></iframe>",
            html: true,
            trigger: "hover",
            delay: 500
        });
    });
    // Enable grid fo random pictures history
    var $randomPicturesGridContainer = $('.random-pictures-grid-container');
    $randomPicturesGridContainer.isotope({
        // options
        itemSelector: '.random-pictures-grid-item',
        percentPosition: true,
        masonry: {
            columnWidth: '.random-pictures-grid-sizer'
        }
    });
    $randomPicturesGridContainer.imagesLoaded().progress(function () {
        $randomPicturesGridContainer.isotope('layout');
    });
    // Lazy load random picture images
    $randomPicturesGridContainer.one('layoutComplete', function () {
        $("img.random-pictures-lazy").lazyload({
            failure_limit: 100,
            effect : "fadeIn"
        });
        $('img.random-pictures-grid-item').on('load', function () {
            $randomPicturesGridContainer.isotope('layout');
        });
    });
});
