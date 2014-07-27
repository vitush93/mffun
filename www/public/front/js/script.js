$(function () {
    $('#user-controls-toggle').on('click', function (e) {
        e.preventDefault();
    });

    $('#filter-button').on('click', function (e) {
        e.preventDefault();
        var height = $('#filter-container').height() + 95 + 41;
        var $filter = $('#control-bar');
        if ($filter.hasClass('active')) {
            $('#filter-buttons').hide();
            $filter.stop().animate({height: 95}, 200).removeClass('active');
            //$filter.stop().velocity({height: 95}, {duration: 200}).removeClass('active');
        } else {
            $('#filter-buttons').show();
            $filter.stop().animate({height: height}, 200).addClass('active');
            //$filter.stop().velocity({height: height}, {duration: 200}).addClass('active');
        }
    });

    $('.flash i').on('click', function () {
        $(this).parent('.flash').remove();
    });

    $('#checkbox-reset').on('click', function (e) {
        e.preventDefault();
        $('.filter-checkbox').each(function () {
            $(this).attr('checked', false);
        });
    });

    $('#user-cp').on({
        mouseenter: function () {
            $('#user-controls').stop().show().animate({top: 60, opacity: 1}, 150);
            /*
             $('#user-controls').stop().show().velocity({
             top: 60,
             opacity: 1
             },
             {
             duration: 150,
             mobileHA: true
             });
             */
        },
        mouseleave: function () {


            $('#user-controls').stop().animate({top: 50, opacity: 0}, 150, function () {
                $(this).hide();
            });


            /*
             $('#user-controls').stop().velocity({
             top: 50,
             opacity: 0
             },
             {
             duration: 150,
             mobileHA: true,
             complete: function () {
             $(this).hide();
             }
             });
             */
        }
    });


    $('html').on('click', function (e) {
        if ($('#page-search-button').hasClass('active')) {
            var target = $(e.target);
            if (target.parents('#top-nav').size() == 0) {
                searchBoxControl();
            }
        }
    });

    $('#page-search-button').click(function (e) {
        e.preventDefault();
        searchBoxControl();
    });

    function searchBoxControl() {
        var button = $('#page-search-button');
        var box = $('#page-search-box');
        var props = {
            mobileHA: true,
            easing: 'swing',
            duration: 100,
            complete: function () {
                box.val('');
                box.focus();
            }
        };

        if (button.hasClass('active')) {
            button.removeClass('active');
            /*
             box.velocity({
             width: 0,
             paddingLeft: 0,
             paddingRight: 0
             }, props);
             */
            box.animate({width: 0, paddingLeft: 0, paddingRight: 0}, 100, function () {
                box.val('');
                box.focus();
            });
        } else {
            /*
             box.velocity({
             width: box.offset().left - $('#nav-container').offset().left - 15 - $('#logo').outerWidth() - 30 + 2,
             paddingLeft: 15,
             paddingRight: 15
             }, props);
             */
            box.animate({width: box.offset().left - $('#nav-container').offset().left - 15 - $('#logo').outerWidth() - 30 + 2,
                paddingLeft: 15,
                paddingRight: 15}, 100, function () {
                box.val('');
                box.focus();
            });
            button.addClass('active');
        }
    }

    $('#scroll-top').click(function (event) {
        event.preventDefault();
        $('html, body').stop().animate({scrollTop: 0}, 200);
        return false;
    });

    stickyMenu($('#mobile-menu'), 45, 200, true);
    stickyMenu($('#top-nav'), 55, 50, false);

    // FIXME needs refactoring
    function stickyMenu(mobileMenu, menuHeight, dur, mobile) {
        var lastScroll = 0;
        var scrollTop = $('#scroll-top');
        var navbarVisible = true;
        var scrollVisible = true;

        var scrolled = false;
        $(window).scroll(function (event) {
            scrolled = true;
        });

        setInterval(function () {
            if (scrolled) {
                scrolled = false;
                var st = $(this).scrollTop();

                if (st > lastScroll) {
                    // scroll down
                    if (navbarVisible) {
                        if (st > 200) {
                            if (mobile) {
                                //mobileMenu.hide();
                                mobileMenu.stop().animate({top: -menuHeight}, 200);
                            } else {
                                /*
                                 mobileMenu.stop().velocity(
                                 {
                                 top: -menuHeight
                                 },
                                 {
                                 mobileHA: true,
                                 duration: 100
                                 });
                                 */
                                mobileMenu.stop().animate({top: -menuHeight}, 200);
                            }
                            navbarVisible = false;
                        }
                    }
                    if (!mobile) {
                        if (scrollVisible) {
                            /*
                             scrollTop.stop().velocity({
                             bottom: 10,
                             opacity: 0
                             },
                             {
                             duration: 200,
                             mobileHA: true,
                             complete: function () {
                             $(this).hide();
                             }
                             });
                             */
                            scrollTop.stop().animate({bottom: 10, opacity: 0}, 200, function () {
                                $(this).hide();
                            });
                            /*
                             $('#footer').stop().velocity({
                             opacity: 0
                             },
                             {
                             duration: 100,
                             mobileHA: true,
                             complete: function () {
                             $(this).hide();
                             }
                             });
                             */
                            $('#footer').stop().animate({opacity: 0}, 200, function () {
                                $(this).hide();
                            });
                            scrollVisible = false;
                        }
                    }
                }
                else {
                    // scroll up
                    if (!navbarVisible) {
                        if (mobile) {
                            //mobileMenu.show();
                            mobileMenu.stop().animate({top: 0}, 200);
                        } else {
                            /*
                             mobileMenu.stop().velocity(
                             {
                             top: 0
                             },
                             {
                             mobileHA: true,
                             duration: 100
                             });
                             */
                            mobileMenu.stop().animate({top: 0}, 200);
                        }
                        navbarVisible = true;
                    }
                    if (!mobile) {
                        if (!scrollVisible) {
                            if (st + $(window).height() - $('.tag-cloud').outerHeight() - $("#control-bar").outerHeight() > 200) {
                                /*
                                 scrollTop.stop().show().velocity(
                                 {
                                 bottom: 20,
                                 opacity: 1
                                 },
                                 {
                                 mobileHA: true,
                                 duration: 200
                                 });
                                 */
                                scrollTop.stop().show().animate({bottom: 20, opacity: 1}, 200);
                                /*
                                 $('#footer').stop().show().velocity(
                                 {
                                 opacity: 1
                                 },
                                 {
                                 mobileHA: true,
                                 duration: 100
                                 });
                                 */
                                $('#footer').stop().show().animate({opacity: 1}, 200);
                                scrollVisible = true;
                            }
                        } else {
                            if (st + $(window).height() - $('.tag-cloud').outerHeight() - $("#control-bar").outerHeight() < 200) {
                                /*
                                 scrollTop.stop().velocity(
                                 {
                                 bottom: 10,
                                 opacity: 0
                                 },
                                 {
                                 duration: 200,
                                 mobileHA: true,
                                 complete: function () {
                                 $(this).hide();
                                 }
                                 });
                                 */
                                scrollTop.stop().animate({bottom: 10, opacity: 0}, 200, function () {
                                    $(this).hide();
                                });
                                /*
                                 $('#footer').stop().velocity({
                                 opacity: 0
                                 },
                                 {
                                 duration: 100,
                                 mobileHA: true,
                                 complete: function () {
                                 $(this).hide();
                                 }
                                 });
                                 */
                                $('#footer').stop().animate({opacity: 0}, 200, function () {
                                    $(this).hide();
                                });
                                scrollVisible = false;
                            }
                        }
                    }
                }

                lastScroll = st;
            }
        }, 500);
    }

});