'use strict';

window.$ = window.jQuery = require('jquery');
require('autogrow');

module.exports = function () {
    $('.comment-textarea').autogrow();

    $('.new-reply-form').on('click', '.reply', function (e) {
        e.preventDefault();
        $(this).parent('div').append('<input type="hidden" value="' + $(this).data('reply') + '" name="reply-id"><textarea maxlength="250" name="reply-content"></textarea>');
        $('textarea').focus();
        $('textarea').autogrow();
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
};