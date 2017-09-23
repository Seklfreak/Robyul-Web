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
    // Statistics Counter
    $('.statistics-counter').each(function() {
        var $statisticsCounter = $(this);
        var type = $statisticsCounter.data('type');
        var interval = $statisticsCounter.data('interval');
        var refresh = $statisticsCounter.data('refresh');
        var guildID = $statisticsCounter.data('guild-id');
        var $number = $(this).find('#number');

        var serverStatisticsCountEndpoint = "statistics/" + guildID + "/" + type + "/" + interval + "/count";

        apiRequest(serverStatisticsCountEndpoint, function (msg) {
            $number.html(msg.Count);
        });
        window.setInterval(function() {
            apiRequest(serverStatisticsCountEndpoint, function (msg) {
                $number.html(msg.Count);
            });
        }, refresh);
    });
    // Statistics Chart
    var $combinedChart = $('#combined-chart');
    if (typeof $combinedChart != 'undefined' && $combinedChart.length > 0) {
        var guildID = $combinedChart.data('guild-id');
        var serverStatisticsMessagesHourlyEndpoint = "statistics/" + guildID + "/messages/hour/histogram";
        var sessionID = "";

        apiRequest(serverStatisticsMessagesHourlyEndpoint, function (msg) {
            console.debug(msg);
        });

        var combinedChartContext = $combinedChart[0].getContext('2d')
        var combinedChart = new Chart(combinedChartContext, {
            type: 'bar',
            data: {
                labels: ["Red", "Blue", "Yellow", "Green", "Purple", "Orange"],
                datasets: [{
                    label: '# of Votes',
                    data: [12, 19, 3, 5, 2, 3],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255,99,132,1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero:true
                        }
                    }]
                }
            }
        });
    }
    // Do API Requests
    function apiRequest(endpoint, callback) {
        $.ajax({
            method: "POST",
            url: window.parameters.session_url,
            cache: false,
            dataType: "text"
          })
            .done(function( msg ) {
                sessionID = msg;

                $.ajax({
                    method: "GET",
                    url: window.parameters.bot_api_base_url + endpoint,
                    cache: false,
                    dataType: "json",
                    headers: {
                        "Authorization": "PHP-Session "+sessionID
                    }
                })
                    .done(function( msg ) {
                        callback(msg)
                    });
            });
    }
});
