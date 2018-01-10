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
    // ServerActivity Charts
    var $messageActivityChart = $('#messageactivity-chart');
    var $joinsAndLeavesActivityChart = $('#joinsandleavesactivity-chart');
    if (typeof $messageActivityChart !== 'undefined' && $messageActivityChart.length > 0 &&
        typeof $joinsAndLeavesActivityChart !== 'undefined' && $joinsAndLeavesActivityChart.length > 0) {
        var guildID = $messageActivityChart.data('guild-id');
        var guildName = $messageActivityChart.data('guild-name');

        var dataMessages = {
            labels: ["Please wait…", "Please wait…"],
            datasets: [
                {
                    title: "Messages",
                    values: [0, 0]
                }
            ]
        };
        var dataJoinsAndLeaves = {
            labels: ["Please wait…", "Please wait…"],
            datasets: [
                {
                    title: "Joins",
                    values: [0, 0]
                },
                {
                    title: "Leaves",
                    values: [0, 0]
                }
            ]
        };

        var messageChart = new Chart({
            parent: $messageActivityChart[0],
            title: "Message Activity on " + guildName,
            data: dataMessages,
            type: 'line',
            height: 250,

            colors: ['#7cd6fd'],

            show_dots: 1,
            heatline: 1,
            region_fill: 1
        });

        var joinsAndLeavesChart = new Chart({
            parent: $joinsAndLeavesActivityChart[0],
            title: "Joins and Leaves on " + guildName,
            data: dataJoinsAndLeaves,
            type: 'line',
            height: 250,

            colors: ['#28a745', '#ff5858'],

            show_dots: 1,
            heatline: 1,
            region_fill: 1
        });
        // ServerActivitySubmit submit
        var $serverActivityRangeForm = $('#serveractivity-range-form');
        var $serverActivityCountInput = $('#count-serveractivity-input');
        var $serverActivityIntervalSelect = $('#interval-serveractivity-select');
        var $serverActivitySubmitButton = $('#button-serveractivity-submit');
        $serverActivityRangeForm.on('submit', function (event) {
            event.preventDefault();
            $serverActivitySubmitButton.prop('disabled', true);
            var count = $serverActivityCountInput.val();
            var interval = $serverActivityIntervalSelect.find('option:selected').val();

            if (!$.isNumeric(count) || parseInt(count) < 3) {
                return
            }

            var serverActivityStatisticsEndpoint = "statistics/" + guildID + "/serveractivity/" + interval + "/histogram/" + count;

            apiRequest(serverActivityStatisticsEndpoint, function (msg) {
                var valuesMessages = [];
                var valuesJoins = [];
                var valuesLeaves = [];
                var labels = [];

                $.each(msg.reverse(), function (key, value) {
                    valuesMessages.push(value.Count1);
                    valuesJoins.push(value.Count2);
                    valuesLeaves.push(value.Count3);

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
                });

                //console.debug(valuesMessages);
                //console.debug(valuesJoins);
                //console.debug(valuesLeaves);
                //console.debug(labels);

                messageChart.update_values(
                    [
                        {values: [0, 0]}
                    ],
                    ["Please wait…", "Please wait…"]
                );

                joinsAndLeavesChart.update_values(
                    [
                        {values: [0, 0]},
                        {values: [0, 0]}
                    ],
                    ["Please wait…", "Please wait…"]
                );

                messageChart.update_values(
                    [
                        {values: valuesMessages}
                    ],
                    labels
                );

                joinsAndLeavesChart.update_values(
                    [
                        {values: valuesJoins},
                        {values: valuesLeaves}
                    ],
                    labels
                );

                $serverActivitySubmitButton.prop('disabled', false);
            });
        });
        $serverActivityRangeForm.submit();
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
                aroundMessageID = msg[msg.length - 1].ID;
            }

            var resultHTML = '';
            $.each(msg, function (i, message) {
                var classes = '';
                if (message.ID === aroundMessageID) {
                    classes = 'selected';
                }
                if (message.Deleted === true) {
                    classes = 'deleted';
                }

                var contentText1 = message.Content;
                var contentText2 = "";
                if (Array.isArray(contentText1) && contentText1.length > 1) {
                    $.each(contentText1, function (contentI, contentText) {
                        if (contentI > 0) {
                            contentText2 += " <i class=\"fa fa-arrow-right\" aria-hidden=\"true\"></i> Edited:<br>" + escapeHTML(contentText)
                        }
                    });
                    contentText1 = escapeHTML(contentText1[0]);
                }
                var attachmentsText = "";
                if (Array.isArray(message.Attachments) && message.Attachments.length > 0) {
                    $.each(message.Attachments, function (attachmentI, attachmentText) {
                        attachmentsText += "<a href=\"" + attachmentText + "\" target=\"_blank\" rel=\"nofollow\">" + attachmentText + "</a>";
                        if (attachmentI < message.Attachments.length) {
                            attachmentsText += "<br>"
                        }
                    });
                    attachmentsText = "<br><i class=\"fa fa-arrow-right\" aria-hidden=\"true\"></i> Attachment(s): " + attachmentsText;
                }

                resultHTML += '<tr class="' + classes + '"><td scope="row"><a href="#" class="chatlog-messageid-click" data-message-id="' + escapeHTML(message.ID) + '" id="message-' + escapeHTML(message.ID) + '">#' + escapeHTML(message.ID) + '</a></td><td>' + escapeHTML(message.CreatedAt) + '</td><td>' + escapeHTML(message.AuthorUsername) + ' (#' + escapeHTML(message.AuthorID) + ')</td><td>' + contentText1 + attachmentsText + contentText2 + '</td></tr>';
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
            parent: $vanityInviteChart[0],
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

                $.each(msg.reverse(), function (key, value) {
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

                    $.each(value.SubItems, function (subKey, subValue) {
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
                        {values: [0, 0]},
                        {values: [0, 0]}
                    ],
                    ["Please wait…", "Please wait…"]
                );

                chart.update_values(
                    [
                        {values: valuesClicks},
                        {values: valuesJoins}
                    ],
                    labels
                );

                var newReferersText = "";
                $.each(referers, function (key, value) {
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
    // profile user bar
    var $profileBarChart = $('#profile-bar-chart');
    if (typeof $profileBarChart !== 'undefined' && typeof window.profileBarRankingData !== 'undefined' && window.profileBarRankingData.length > 0) {
        var data = {
            labels: [],

            datasets: [
                {
                    title: "EXP",
                    values: []
                }
            ]
        };

        $.each(window.profileBarRankingData, function (_, value) {
            if (value.GuildID === "global" || value.Level <= 0) {
                return
            }

            data.labels.push(value.GuildName);
            data.datasets[0].values.push(value.EXP)
        });

        var chart = new Chart({
            parent: $profileBarChart[0],
            data: data,
            type: 'percentage',

            colors: ['light-blue', 'violet', 'red', 'yellow', 'light-green',
                'purple', 'grey']
        });
    }
    // discord embed creator
    var $discordEmbedForm = $('#discord-embed-form');
    if (typeof $discordEmbedForm !== 'undefined') {
        updateDiscordEmbedCreator(true);
        $('#discord-embed-form textarea, #discord-embed-form input').on('input', function () {
            updateDiscordEmbedCreator(true);
        });
        $('#discord-embed-form input[type="checkbox"]').change(function () {
            updateDiscordEmbedCreator(true);
        });
        var $discordEmbedAddFieldButton = $('#buttonDiscordEmbedAddField');
        $discordEmbedAddFieldButton.click(function () {
            addDiscordEmbedFieldForm(true);
        })
    }
    var $discordOutputForm = $('#outputEmbedCode');
    if (typeof $discordOutputForm !== 'undefined') {
        $discordOutputForm.on('input', function () {
            importDiscordEmbedCreator();
        });
    }

    function addDiscordEmbedFieldForm(updateCode) {
        var newIndex = $('.embed-field').length;
        var $newElement = $('<div class="form-row">' +
            '<div class="form-group col-md-6">' +
            '<label for="inputDiscordEmbedFieldTitle' + newIndex + '">Field Title</label>' +
            '<input type="text" class="form-control form-control-sm inputDiscordEmbedFieldTitles" id="inputDiscordEmbedFieldTitle' + newIndex + '">' +
            '</div>' +
            '<div class="form-group col-md-5">' +
            '<label for="inputDiscordEmbedFieldValue' + newIndex + '">Field Value</label>' +
            '<textarea class="form-control form-control-sm inputDiscordEmbedFieldValues" id="inputDiscordEmbedFieldValue' + newIndex + '" rows="3"></textarea>' +
            '</div>' +
            '<div class="form-group col-md-1">' +
            '<input class="form-check-input inputDiscordEmbedFieldInlines" type="checkbox" value="" id="inputDiscordEmbedFieldInline' + newIndex + '" checked>' +
            '<label class="form-check-label" for="inputDiscordEmbedFieldInline' + newIndex + '">' +
            'Inline' +
            '</label>' +
            '</div>' +
            '</div>').insertBefore($discordEmbedAddFieldButton);
        $newElement.find('textarea, input').on('input', function () {
            updateDiscordEmbedCreator(updateCode);
        });
        $newElement.find('input[type="checkbox"]').change(function () {
            updateDiscordEmbedCreator(updateCode);
        });
        updateDiscordEmbedCreator(updateCode);
    }

    function updateDiscordEmbedCreator(updateCode) {
        var embedContent = $('#inputDiscordEmbedContent').val();
        var embedAuthorName = $('#inputDiscordEmbedAuthorName').val();
        var embedAuthorPicture = $('#inputDiscordEmbedAuthorPicture').val();
        var embedAuthorLink = $('#inputDiscordEmbedAuthorLink').val();
        var embedTitleText = $('#inputDiscordEmbedTitleText').val();
        //var embedTitleLink = $('#inputDiscordEmbedTitleLink').val();
        var embedDescription = $('#inputDiscordEmbedDescription').val();
        var embedThumbnailLink = $('#inputDiscordEmbedThumbnailLink').val();
        var embedImageLink = $('#inputDiscordEmbedImageLink').val();
        var embedFooterText = $('#inputDiscordEmbedFooterText').val();
        var embedFooterLink = $('#inputDiscordEmbedFooterLink').val();
        var embedColor = $('#inputDiscordEmbedColor').val();

        var embedFieldData = [];
        $('.inputDiscordEmbedFieldTitles').each(function (index) {
            if (typeof embedFieldData[index] === 'undefined') {
                embedFieldData[index] = {};
            }
            embedFieldData[index]['title'] = $(this).val();
        });
        $('.inputDiscordEmbedFieldValues').each(function (index) {
            if (typeof embedFieldData[index] === 'undefined') {
                embedFieldData[index] = {};
            }
            embedFieldData[index]['value'] = $(this).val();
        });
        $('.inputDiscordEmbedFieldInlines').each(function (index) {
            if (typeof embedFieldData[index] === 'undefined') {
                embedFieldData[index] = {};
            }
            embedFieldData[index]['checked'] = $(this).is(':checked');
        });

        var $outputEmbedCode = $('#outputEmbedCode');
        var commandPrefix = $outputEmbedCode.data('command-prefix');

        var command = commandPrefix;

        $('.message-text > .markup').html(parseDiscordMarkdown(embedContent));
        if (embedContent.length > 0) {
            command += 'ptext=' + embedContent + ' | ';
        }
        if (embedAuthorLink.length > 0) {
            if (embedAuthorPicture.length > 0) {
                $('.embed-author').html('<a target="_blank" rel="noreferrer" href="' + embedAuthorLink + '" class="embed-author-name"><img src="' + embedAuthorPicture + '" role="presentation" class="embed-author-icon">' + embedAuthorName + '</a>')
                command += 'author=name=' + embedAuthorName + ' icon=' + embedAuthorPicture + ' url=' + embedAuthorLink + ' | ';
            } else {
                $('.embed-author').html('<a target="_blank" rel="noreferrer" href="' + embedAuthorLink + '" class="embed-author-name">' + embedAuthorName + '</a>');
                command += 'author=name=' + embedAuthorName + ' url=' + embedAuthorLink + ' | ';
            }
        } else {
            if (embedAuthorPicture.length > 0) {
                $('.embed-author').html('<div class="embed-author-name"><img src="' + embedAuthorPicture + '" role="presentation" class="embed-author-icon">' + embedAuthorName + '</div>');
                command += 'author=name=' + embedAuthorName + ' icon=' + embedAuthorPicture + ' | ';
            } else {
                $('.embed-author').html('<div class="embed-author-name">' + embedAuthorName + '</div>');
                if (embedAuthorName.length > 0) {
                    command += 'author=' + embedAuthorName + ' | ';
                }
            }
        }
        $('.embed-title').replaceWith('<div class="embed-title">' + parseDiscordMarkdown(embedTitleText) + '</div>')
        if (embedTitleText.length > 0) {
            command += 'title=' + embedTitleText + ' | ';
        }
        $('.embed-description.markup').html(parseDiscordMarkdown(embedDescription));
        if (embedDescription.length > 0) {
            command += 'description=' + embedDescription + ' | ';
        }
        if (embedThumbnailLink.length > 0) {
            $('.embed-rich-thumb').attr('src', embedThumbnailLink).show();
            command += 'thumbnail=' + embedThumbnailLink + ' | ';
        } else {
            $('.embed-rich-thumb').hide();
        }
        if (embedImageLink.length > 0) {
            $('.embed-thumbnail-rich img').attr('src', embedImageLink);
            $('.embed-thumbnail-rich').show();
            command += 'image=' + embedThumbnailLink + ' | ';
        } else {
            $('.embed-thumbnail-rich').hide();
        }

        var numberOfEmbedFieldsInHTML = $('.embed-field').length;
        if (embedFieldData.length - numberOfEmbedFieldsInHTML > 0) {
            for (i = 0; i < embedFieldData.length - numberOfEmbedFieldsInHTML; i++) {
                $('.embed-fields').append('<div class="embed-field">\n' +
                    '<div class="embed-field-name"></div><div class="embed-field-value markup"></div>\n' +
                    '</div>');
            }
        }
        $.each(embedFieldData, function (index) {
            $('.embed-field-name').eq(index).html(parseDiscordMarkdown(this.title));
            $('.embed-field-value.markup').eq(index).html(parseDiscordMarkdown(this.value));
            if (this.checked) {
                $('.embed-field').eq(index).attr('class', 'embed-field embed-field-inline');
                if (this.title.length > 0 || this.value.length > 0) {
                    command += 'field=name=' + this.title + ' value=' + this.value + ' | ';
                }
            } else {
                $('.embed-field').eq(index).attr('class', 'embed-field');
                if (this.title.length > 0 || this.value.length > 0) {
                    command += 'field=name=' + this.title + ' value=' + this.value + ' inline=no | ';
                }
            }
        });
        $('.embed-footer').html(embedFooterText);
        if (embedFooterLink.length > 0) {
            $('.embed-footer-icon').attr('src', embedFooterLink).show();
            command += 'footer=name=' + embedFooterText + ' icon=' + embedFooterLink + ' | ';
        } else {
            $('.embed-footer-icon').hide();
            if (embedFooterText.length > 0) {
                command += 'footer=' + embedFooterText + ' | ';
            }
        }
        $('.embed-color-pill').css('background-color', embedColor);
        command += 'color=' + embedColor + ' | ';

        command = command.replace(/\| $/g, '');
        command = command.trim();

        if (updateCode) {
            $outputEmbedCode.val(command);
        }
    }

    function importDiscordEmbedCreator() {
        var inputValue = $('#outputEmbedCode').val();
        var inputParts = inputValue.split(' ');
        if (inputParts[0].indexOf('=') < 0) {
            inputValue = inputValue.replace(inputParts[0], '');
        }
        if (inputParts[0].indexOf('=') < 0) {
            inputValue = inputValue.replace(inputParts[1], '');
        }
        inputValue = inputValue.trim();

        if (inputValue.length <= 0) {
            return;
        }

        // Code ported from https://github.com/appu1232/Discord-Selfbot/blob/master/cogs/misc.py#L146
        // Reference https://github.com/Seklfreak/Robyul2/blob/master/modules/plugins/embedpost.go#L74
        var messageContent = "", authorName = "", authorPicture = "", authorLink = "", title = "", description = "",
            thumbnail = "", image = "", footerText = "",
            footerLink = "", color = "";

        var inputValues = [];

        $.each(inputValue.split('|'), function () {
            inputValues.push($.trim(this));
        });

        $.each(inputValues, function () {
            var embedValue = this;
            if (embedValue.indexOf('ptext=') === 0) {
                messageContent = embedValue.substr(6, embedValue.length).trim();
            } else if (embedValue.indexOf('title=') === 0) {
                title = embedValue.substr(6, embedValue.length).trim();
            } else if (embedValue.indexOf('description=') === 0) {
                description = embedValue.substr(12, embedValue.length).trim();
            } else if (embedValue.indexOf('desc=') === 0) {
                description = embedValue.substr(5, embedValue.length).trim();
            } else if (embedValue.indexOf('image=') === 0) {
                image = embedValue.substr(6, embedValue.length).trim();
            } else if (embedValue.indexOf('thumbnail=') === 0) {
                thumbnail = embedValue.substr(10, embedValue.length).trim();
            } else if (embedValue.indexOf('colour=') === 0) {
                color = embedValue.substr(7, embedValue.length).trim();
            } else if (embedValue.indexOf('color=') === 0) {
                color = embedValue.substr(6, embedValue.length).trim();
            } else if (embedValue.indexOf('footer=') === 0) {
                footerText = embedValue.substr(7, embedValue.length).trim();
            } else if (embedValue.indexOf('author=') === 0) {
                authorName = embedValue.substr(7, embedValue.length).trim();
            } else if (description.length <= 0 && embedValue.indexOf('field=') !== 0) {
                description = embedValue
            }
        });

        if (authorName.length > 0) {
            if (authorName.indexOf('icon=') >= 0) {
                var authorValues = authorName.split('icon=', 2);
                if (authorValues.length >= 2) {
                    if (authorValues[1].indexOf('url=') >= 0) {
                        var iconValues = authorValues[1].split('url=', 2);
                        if (iconValues.length >= 2) {
                            authorName = authorValues[0].substr(5, authorValues[0].length).trim();
                            authorPicture = iconValues[0].trim();
                            authorLink = iconValues[1].trim();
                        }
                    } else {
                        authorName = authorValues[0].substr(5, authorValues[0].length).trim();
                        authorPicture = authorValues[1].trim();
                    }
                }
            } else {
                if (authorName.indexOf('url=') >= 0) {
                    var authorValues = authorName.split('url=', 2);
                    if (authorValues.length >= 2) {
                        authorName = authorValues[0].substr(5, authorValues[0].length).trim();
                        authorLink = authorValues[1].trim();
                    }
                }
            }
        }

        if (footerText.length > 0) {
            if (footerText.indexOf('icon=') > 0) {
                var footerValues = footerText.split('icon=', 2);
                if (footerValues.length >= 2) {
                    footerText = footerValues[0].substr(5, footerValues[0].length).trim();
                    footerLink = footerValues[1].trim();
                }
            }
        }

        var embedFieldData = [];
        $.each(inputValues, function () {
            var embedValue = this;
            if (embedValue.indexOf('field=') === 0) {
                var currentIndex = embedFieldData.length;
                embedFieldData[currentIndex] = {
                    title: '',
                    value: '',
                    checked: true
                };
                embedValue = embedValue.substr(6, embedValue.length).trim();
                var fieldValues = embedValue.split('value=', 2);
                if (fieldValues.length >= 2) {
                    embedFieldData[currentIndex]['title'] = fieldValues[0].trim();
                    embedFieldData[currentIndex]['value'] = fieldValues[1].trim();
                } else if (fieldValues.length >= 1) {
                    embedFieldData[currentIndex]['title'] = fieldValues[0].trim();
                }
                if (embedFieldData[currentIndex]['value'].indexOf('inline=') > 0) {
                    var fieldValues = embedFieldData[currentIndex]['value'].split('inline=', 2);
                    if (fieldValues.length >= 2) {
                        embedFieldData[currentIndex]['value'] = fieldValues[0].trim();
                        if (fieldValues[1].indexOf('false') >= 0 || fieldValues[1].indexOf('no') >= 0) {
                            embedFieldData[currentIndex]['checked'] = false;
                        }
                    } else if (fieldValues.length >= 1) {
                        embedFieldData[currentIndex]['value'] = fieldValues[0].trim();
                    }
                }
                if (embedFieldData[currentIndex]['title'].indexOf('name=') >= 0) {
                    embedFieldData[currentIndex]['title'] = embedFieldData[currentIndex]['title'].substr(5, embedFieldData[currentIndex]['title'].length);
                }
            }
        });

        if (color.length <= 0) {
            color = '#4f545c';
        }

        $('#inputDiscordEmbedContent').val(messageContent);
        $('#inputDiscordEmbedAuthorName').val(authorName);
        $('#inputDiscordEmbedAuthorPicture').val(authorPicture);
        $('#inputDiscordEmbedAuthorLink').val(authorLink);
        $('#inputDiscordEmbedTitleText').val(title);
        $('#inputDiscordEmbedDescription').val(description);
        $('#inputDiscordEmbedThumbnailLink').val(thumbnail);
        $('#inputDiscordEmbedImageLink').val(image);
        $('#inputDiscordEmbedFooterText').val(footerText);
        $('#inputDiscordEmbedFooterLink').val(footerLink);
        $('#inputDiscordEmbedColor').val(color);

        var numberOfEmbedFieldFormsInHTML = $('.inputDiscordEmbedFieldTitles').length;
        createDelayedEmbedFieldFormsWithCallback(embedFieldData.length - numberOfEmbedFieldFormsInHTML, function () {
            $.each(embedFieldData, function (index) {
                $('.inputDiscordEmbedFieldTitles').eq(index).val(this.title);
                $('.inputDiscordEmbedFieldValues').eq(index).val(this.value);
                if (this.checked) {
                    $('.inputDiscordEmbedFieldInlines').eq(index).attr('checked', true);
                } else {
                    $('.inputDiscordEmbedFieldInlines').eq(index).attr('checked', false);
                }
            });

            updateDiscordEmbedCreator(false);
        });
    }

    function createDelayedEmbedFieldFormsWithCallback(numberOfForms, callback) {
        if (numberOfForms <= 0) {
            callback();
        } else {
            setTimeout(function () {
                addDiscordEmbedFieldForm(false);
                createDelayedEmbedFieldFormsWithCallback(numberOfForms - 1, callback)
            }, 10);
        }
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

    function parseDiscordMarkdown(inputText) {
        var mdParse = SimpleMarkdown.defaultBlockParse;
        var mdOutput = SimpleMarkdown.defaultOutput;
        var syntaxTree = mdParse(inputText);
        return convert(mdOutput(syntaxTree)[0]);
    }
});
