'use strict';

require('jquery');

module.exports = function () {

    $(document).on('click', '.flash i', function () {
        $(this).parent('.flash').remove();
    });

    $('#share-icons').bind('click', function () {
        $('#bar-social').load('/public/front/social.html');
        $(this).unbind('click');
    });

    footerScroll.init();
};

var footerScroll = {
    init: function () {
        $('#scroll-top').click(function (e) {
            e.preventDefault();
            $('html, body').stop().animate({scrollTop: 0}, 200);
            return false;
        });

        var lastScroll = 0;
        var scrollVisible = true;
        var scrolled = false;

        $(window).scroll(function (event) {
            scrolled = true;
        });

        setInterval(function () {
            if (!scrolled) return;

            scrolled = false;
            var st = $(this).scrollTop();
            if (st > lastScroll) {
                if (scrollVisible) {
                    $('#scroll-top').stop().animate({bottom: 10, opacity: 0}, 100, function () {
                        $(this).hide();
                    });

                    $('#footer').stop().animate({opacity: 0}, 100, function () {
                        $(this).hide();
                    });
                    scrollVisible = false;
                }
            } else {
                if (!scrollVisible) {
                    $('#scroll-top').stop().show().animate({bottom: 20, opacity: 1}, 100);
                    $('#footer').stop().show().animate({opacity: 1}, 100);
                    scrollVisible = true;
                }
            }

            lastScroll = st;
        }, 150);
    }
};