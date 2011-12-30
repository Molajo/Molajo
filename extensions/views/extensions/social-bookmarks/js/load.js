// Molajo Project
// Load Social Bookmarks Buttons
// Copyright (C) 2012 Amy Stephen. All rights reserved.
// License GNU General Public License version 2 or later http://www.gnu.org/licenses/gpl.html

// Twitter
$.get('index.php?option=views&view=component&view=popup&view=dummy', function(html) {
    var $response = $(html);

    // if there is an empty response do nothing
    if ($response.length < 1) return;

    // create the twitter buttons
    $response.find('a.twitter-share-button').each(function() {
        var tweet_button = new twttr.TweetButton($(this).get(0));
        tweet_button.render();
    });
});


// Facebook
$.get('index.php?option=views&view=component&view=popup&view=dummy', function(html) {
    var $response = $(html);

    // if there is an empty response do nothing
    if ($response.length < 1) return;

    // create the facebook buttons
    $response.find('.btn_fb').each(function() {
        FB.XFBML.parse(this);
    });
});


