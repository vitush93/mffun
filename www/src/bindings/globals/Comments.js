require('autogrow');

module.exports = function () {
    $('.comment-textarea').autogrow();

    $(document).on('click', '.reply', function (e) {
        e.preventDefault();

        var $textarea = $('<textarea maxlength="1000" class="js-post-reply" name="reply-content"></textarea>');
        $textarea.data('parent', $(this).data('parent'));
        $textarea.data('quote', $(this).data('quote'));

        $(this).parent('div').append($textarea);

        $textarea.focus();
        $textarea.autogrow();
    });
};