'use strict';

window.$ = window.jQuery = require('jquery');

module.exports = function () {
    $('body').on('click', '#toggle-button', function (e) {
        e.preventDefault();
        var topMenu = $('#top-menu');
        if ($(this).hasClass('active')) {
            $('#top-nav').attr('style', 'top:0px');
            topMenu.hide();
            $(this).removeClass('active');
        } else {
            $('#top-nav').attr('style', 'top: 0px !important');
            topMenu.show();
            $(this).addClass('active');
        }
    });

    $('#user-controls-toggle').on('click', function (e) {
        e.preventDefault();
        userCpControl();
    });

    $('html').on('click', function (e) {
        var target = $(e.target);
        if ($('#page-search-button').hasClass('active')) {
            if (target.parents('#top-nav').size() == 0 && target.parents('.ui-autocomplete').size() == 0) {
                searchBoxControl();
            }
        }
        if ($('#user-controls').hasClass('active')) {
            if (target.parents('#user-cp').size() == 0) {
                userCpControl();
            }
        }
    });

    $('#page-search-button').click(function (e) {
        e.preventDefault();
        searchBoxControl();
    });
};

function userCpControl() {
    var c = $('#user-controls');
    if (c.hasClass('active')) {
        $('#top-nav').attr('style', 'top:0px');
        c.stop().animate({top: 50, opacity: 0}, 150, function () {
            $(this).hide();
        });
        c.removeClass('active');
        $('#user-controls-toggle').removeClass('active');
    } else {
        $('#top-nav').attr('style', 'top: 0px !important');
        c.stop().show().animate({top: 60, opacity: 1}, 150);
        c.addClass('active');
        $('#user-controls-toggle').addClass('active');
    }
}

function searchBoxControl() {
    var button = $('#page-search-button');
    var box = $('#page-search-box');

    if (button.hasClass('active')) {
        $('#top-nav').attr('style', 'top:0px');
        button.removeClass('active');
        box.animate({width: 0, paddingLeft: 0, paddingRight: 0}, 100, function () {
            box.val('');
            box.focus();
        });
    } else {
        $('#top-nav').attr('style', 'top: 0px !important');
        box.animate({
            width: box.offset().left - $('#nav-container').offset().left - $('#logo').outerWidth() - 15,
            paddingLeft: 15,
            paddingRight: 15
        }, 100, function () {
            box.val('');
            box.focus();
        });
        button.addClass('active');
    }
}
