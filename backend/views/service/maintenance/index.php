<?php
use artsoft\helpers\Html;
?>
<!DOCTYPE html>
<html>
<head>
    <title>stravinskiy.ru</title>
    <!-- for-mobile-apps -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <!-- //for-mobile-apps -->
    <!-- js -->
    <script type="text/javascript" src="js/jquery-2.1.4.min.js"></script>
    <!-- //js -->
    <link href="css/style.css" rel="stylesheet" type="text/css" media="all" />
    <link href='//fonts.googleapis.com/css?family=Quicksand:400,300,700' rel='stylesheet' type='text/css'>
</head>
<body>
<!-- banner -->
<div class="banner">
    <div class="banner-info">
        <h1><?= Html::encode($message)?></h1>
        <h2>Мы будем рады Вас видеть на нашем ресурсе через <?= $retry_after ?>!</h2>
        <div class="main-example">
            <div class="countdown-container" id="main-example"></div>
        </div>
        <script type="text/javascript" src="js/jquery.countdown.js"></script>
        <script type="text/javascript" src="js/lodash.min.js"></script>
        <script type="text/template" id="main-example-template">
            <div class="time <%= label %>">
                <span class="count curr top"><%= curr %></span>
                <span class="count next top"><%= next %></span>
                <span class="count next bottom"><%= next %></span>
                <span class="count curr bottom"><%= curr %></span>
                <span class="label"><%= label.length < 6 ? label : label.substr(0, 3)  %></span>
            </div>
        </script>
        <script type="text/javascript">
            $(window).on('load', function() {
                var labels = ['weeks', 'days', 'hours', 'minutes', 'seconds'],
                    nextYear = (new Date().getFullYear() + 1) + '/01/01',
                    template = _.template($('#main-example-template').html()),
                    currDate = '00:00:00:00:00',
                    nextDate = '00:00:00:00:00',
                    parser = /([0-9]{2})/gi,
                    $example = $('#main-example');
                // Parse countdown string to an object
                function strfobj(str) {
                    var parsed = str.match(parser),
                        obj = {};
                    labels.forEach(function(label, i) {
                        obj[label] = parsed[i]
                    });
                    return obj;
                }
                // Return the time components that diffs
                function diff(obj1, obj2) {
                    var diff = [];
                    labels.forEach(function(key) {
                        if (obj1[key] !== obj2[key]) {
                            diff.push(key);
                        }
                    });
                    return diff;
                }
                // Build the layout
                var initData = strfobj(currDate);
                labels.forEach(function(label, i) {
                    $example.append(template({
                        curr: initData[label],
                        next: initData[label],
                        label: label
                    }));
                });
                // Starts the countdown
                $example.countdown(nextYear, function(event) {
                    var newDate = event.strftime('%w:%d:%H:%M:%S'),
                        data;
                    if (newDate !== nextDate) {
                        currDate = nextDate;
                        nextDate = newDate;
                        // Setup the data
                        data = {
                            'curr': strfobj(currDate),
                            'next': strfobj(nextDate)
                        };
                        // Apply the new values to each node that changed
                        diff(data.curr, data.next).forEach(function(label) {
                            var selector = '.%s'.replace(/%s/, label),
                                $node = $example.find(selector);
                            // Update the node
                            $node.removeClass('flip');
                            $node.find('.curr').text(data.curr[label]);
                            $node.find('.next').text(data.next[label]);
                            // Wait for a repaint to then flip
                            _.delay(function($node) {
                                $node.addClass('flip');
                            }, 50, $node);
                        });
                    }
                });
            });
        </script>
        <div class="progressbars" progress="60%"></div>
        <script src="js/jprogress.js" type="text/javascript"></script>
        <script>
            // activate jprogress
            $(".progressbars").jprogress({
                background: "#80C340"
            });
        </script>
        <div class="copyright">
            <p>© 2014 <a href="https://stravinskiy.ru">stravinskiy.ru</a></p>
        </div>
    </div>
</div>
<!-- //banner -->
</body>
</html>
