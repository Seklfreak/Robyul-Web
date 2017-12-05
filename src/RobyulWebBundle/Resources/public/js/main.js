$(function () {
    // Activate tooltips
    $('[data-toggle="tooltip"]').tooltip();
    // Lazy load lazy images
    $("img.lazy").lazyload({
        effect: "fadeIn"
    });
    // Activate clipboard.js
    new Clipboard('.clipboard-button');
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
            effect: "fadeIn"
        });
        $('img.random-pictures-grid-item').on('load', function () {
            $randomPicturesGridContainer.isotope('layout');
        });
    });
    // Statistics Counter
    $('.statistics-counter').each(function () {
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
        window.setInterval(function () {
            apiRequest(serverStatisticsCountEndpoint, function (msg) {
                $number.html(msg.Count);
            });
        }, refresh);
    });
    // Statistics Chart
    var $combinedChart = $('#combined-chart');
    if (typeof $combinedChart !== 'undefined' && $combinedChart.length > 0) {
        var guildID = $combinedChart.data('guild-id');
        var serverStatisticsMessagesHourlyEndpoint = "statistics/" + guildID + "/messages/hour/histogram";

        apiRequest(serverStatisticsMessagesHourlyEndpoint, function (msg) {
            console.debug(msg);
        });

        var combinedChartContext = $combinedChart[0].getContext('2d');
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
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
    }
    // Chatlog submit
    var $chatlogAroundMessageIDForm = $('#chatlog-around-messageid-form');
    var $chatlogChannelSelect = $('#chatlog-channel-select');
    var $chatlogAroundMessageIDInput = $('#chatlog-around-messageid-input');
    var $chatlogResultTBody = $('#chatlog-result-tbody');
    var $chatlogSubmitButton = $('#button-chatlog-submit');
    $chatlogAroundMessageIDForm.on('submit', function (event) {
        event.preventDefault();
        $chatlogSubmitButton.prop('disabled', true);
        var guildID = $chatlogAroundMessageIDForm.data('guild-id');
        var channelID = $chatlogChannelSelect.find('option:selected').val();
        var aroundMessageID = $chatlogAroundMessageIDInput.val();

        if (aroundMessageID === "") {
            aroundMessageID = "last";
        }

        var serverStatisticsCountEndpoint = "chatlog/" + guildID + "/" + channelID + "/around/" + aroundMessageID;

        apiRequest(serverStatisticsCountEndpoint, function (msg) {
            if (aroundMessageID === "last") {
                aroundMessageID = msg[msg.length-1].ID;
            }

            var resultHTML = '';
            $.each(msg, function (i, message) {
                var classes = '';
                if (message.ID === aroundMessageID) {
                    classes = 'selected';
                }
                resultHTML += '<tr class="' + classes + '"><td scope="row"><a href="#" class="chatlog-messageid-click" data-message-id="' + escapeHTML(message.ID) + '" id="message-' + escapeHTML(message.ID) + '">#' + escapeHTML(message.ID) + '</a></td><td>' + escapeHTML(message.CreatedAt) + '</td><td>' + escapeHTML(message.AuthorUsername) + ' (#' + escapeHTML(message.AuthorID) + ')</td><td>' + escapeHTML(message.Content) + "<br>" + escapeHTML(message.Attachments) + '</td></tr>';
            });
            $chatlogResultTBody.html(resultHTML);
            if ($("#message-" + aroundMessageID).length > 0) {
                $(document).scrollTop($("#message-" + aroundMessageID).offset().top - 200);
            }
            $chatlogSubmitButton.prop('disabled', false);
            $('.chatlog-messageid-click').on('click', function () {
                var $chatlogMessageIDClick = $(this);
                var aroundMessageId = $chatlogMessageIDClick.data('message-id');
                $chatlogAroundMessageIDInput.val(aroundMessageId);
                $chatlogAroundMessageIDForm.submit();
            });
        });
    });
    // Vanity Invite Chart Chart
    var $vanityInviteChart = $('#vanityinvite-chart');
    var $vanityInviteReferers = $('#vanityinvite-referers');
    if (typeof $vanityInviteChart !== 'undefined' && $vanityInviteChart.length > 0) {
        var guildID = $vanityInviteChart.data('guild-id');
        var guildName = $vanityInviteChart.data('guild-name');
        var vanityInviteName = $vanityInviteChart.data('vanity-invite-name');
        var vanityInviteUrl = $vanityInviteChart.data('vanity-invite-url');

        var data = {
            labels: ["Please wait…", "Please wait…"],
            datasets: [
                {
                    title: "Clicks",
                    values: [0, 0]
                },
                {
                    title: "Joins",
                    values: [0, 0]
                }
            ]
        };

        var chart = new Chart({
            parent: $vanityInviteChart[0], // or a DOM element
            title: "Stats for " + vanityInviteUrl + " on " + guildName,
            data: data,
            type: 'line',
            height: 250,

            colors: ['#7cd6fd', '#743ee2'],

            show_dots: 1,
            heatline: 1,
            region_fill: 1
        });

        // VanityStats submit
        var $vanityInviteRangeForm = $('#vanityinvite-range-form');
        var $vanityInviteCountInput = $('#count-vanityinvite-input');
        var $vanityInviteIntervalSelect = $('#interval-vanityinvite-select');
        var $vanityInviteSubmitButton = $('#button-vanityinvite-submit');
        $vanityInviteRangeForm.on('submit', function (event) {
            event.preventDefault();
            $vanityInviteSubmitButton.prop('disabled', true);
            var count = $vanityInviteCountInput.val();
            var interval = $vanityInviteIntervalSelect.find('option:selected').val();

            if (!$.isNumeric(count) || parseInt(count) < 3) {
                return
            }

            var vanityInviteStatisticsEndpoint = "statistics/" + guildID + "/vanityinvite/" + interval + "/histogram/" + count;

            apiRequest(vanityInviteStatisticsEndpoint, function (msg) {
                var valuesClicks = [];
                var valuesJoins = [];
                var labels = [];
                var referers = {};

                $.each(msg.reverse(), function(key, value) {
                    valuesClicks.push(value.Count1);
                    valuesJoins.push(value.Count2);

                    var date = moment(value.Time);

                    //console.debug(date);

                    switch (interval) {
                        case "minute":
                            labels.push(date.format('HH:mm'));
                            break;
                        case "hour":
                            labels.push(date.format('Do HH:00'));
                            break;
                        case "day":
                            labels.push(date.format('MMM DD'));
                            break;
                        case "week":
                            labels.push(date.format('MMM DD'));
                            break;
                        case "month":
                            labels.push(date.format('YYYY MMM'));
                            break;
                        default:
                            labels.push(value.Time);
                    }

                    $.each(value.SubItems, function(subKey, subValue) {
                        //console.debug(subValue.Key, subValue.Value);

                        if (subValue.Key in referers) {
                            referers[subValue.Key] = referers[subValue.Key] + parseInt(subValue.Value);
                        } else {
                            referers[subValue.Key] = parseInt(subValue.Value);
                        }
                    });
                });

                //console.debug(valuesClicks);
                //console.debug(valuesJoins);
                //console.debug(labels);
                //console.debug(referers);

                chart.update_values(
                    [
                        {values: valuesClicks},
                        {values: valuesJoins}
                    ],
                    labels
                );

                var newReferersText = "";
                $.each(referers, function(key, value) {
                    if (key !== "") {
                        newReferersText = newReferersText + "<a href=\"" + key + "\" target=\"_blank\"  rel=\"nofollow\">" + key + "</a> " + "(" + value + ") ";
                    }
                });
                if (newReferersText === "") {
                    newReferersText = "None"
                }
                $vanityInviteReferers.html(newReferersText);

                $vanityInviteSubmitButton.prop('disabled', false);
            });
        });
        $vanityInviteRangeForm.submit();
    }

    // helpers
    function escapeHTML(text) {
        var htmlEscapes = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#x27;',
            '/': '&#x2F;'
        };
        var htmlEscaper = /[&<>"'\/]/g;
        return ('' + text).replace(htmlEscaper, function (match) {
            return htmlEscapes[match];
        });
    }

    function apiRequest(endpoint, callback) {
        $.ajax({
            method: "POST",
            url: window.parameters.session_url,
            cache: false,
            dataType: "text"
        })
            .done(function (msg) {
                sessionID = msg;

                $.ajax({
                    method: "GET",
                    url: window.parameters.bot_api_base_url + endpoint,
                    cache: false,
                    dataType: "json",
                    headers: {
                        "Authorization": "PHP-Session " + sessionID
                    }
                })
                    .done(function (msg) {
                        callback(msg)
                    });
            });
    }
});
