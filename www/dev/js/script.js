$.nette.init();

$('#filter-button').on('click', function (e) {
    e.preventDefault();
    var height = $('#filter-container').height() + 95 + 41;
    var $filter = $('#control-bar');
    if ($filter.hasClass('active')) {
        $('#filter-buttons').hide();
        $filter.stop().animate({height: 95}, 200).removeClass('active');
    } else {
        $('#filter-buttons').show();
        $filter.stop().animate({height: height}, 200).addClass('active');
    }
});

$('.rate').click(function () {
    $(this).addClass('active');
    $(this).removeAttr('href');
});


// QUOTE
$('.new-reply-form').on('click', '.reply', function (e) {
    e.preventDefault();
    $(this).parent('div').append('<input type="hidden" value="' + $(this).data('reply') + '" name="reply-id"><textarea name="reply-content"></textarea>');
});

$(".new-comment-form textarea").keypress(function (event) {
    if (event.which == 13) {
        event.preventDefault();
        $(".new-comment-form").submit();
        $(this).val('');
    }
});

$('.new-reply-form').on('keypress', 'textarea', function (event) {
    if (event.which == 13) {
        event.preventDefault();
        $(".new-reply-form").submit();
        $('.new-reply-form textarea').remove();
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

$('#user-controls-toggle').on('click', function (e) {
    e.preventDefault();
    userCpControl();
});


$('html').on('click', function (e) {
    var target = $(e.target);
    if ($('#page-search-button').hasClass('active')) {
        if (target.parents('#top-nav').size() == 0) {
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

function userCpControl() {
    var c = $('#user-controls');
    if (c.hasClass('active')) {
        c.stop().animate({top: 50, opacity: 0}, 150, function () {
            $(this).hide();
        });
        c.removeClass('active');
        $('#user-controls-toggle').removeClass('active');
    } else {
        c.stop().show().animate({top: 60, opacity: 1}, 150);
        c.addClass('active');
        $('#user-controls-toggle').addClass('active');
    }
}

function searchBoxControl() {
    var button = $('#page-search-button');
    var box = $('#page-search-box');

    if (button.hasClass('active')) {
        button.removeClass('active');
        box.animate({width: 0, paddingLeft: 0, paddingRight: 0}, 100, function () {
            box.val('');
            box.focus();
        });
    } else {
        box.animate({
            width: box.offset().left - $('#nav-container').offset().left - 15 - $('#logo').outerWidth() - 30 + 2,
            paddingLeft: 15,
            paddingRight: 15
        }, 100, function () {
            box.val('');
            box.focus();
        });
        button.addClass('active');
    }
}

$('#top-nav').autoHidingNavbar({
    hideOffset: 100
});