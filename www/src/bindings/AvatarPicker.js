'use strict';

require('jquery');

module.exports = function () {
    $('body').on('click', '.avatar-pick', function () {
        $('.avatar-pick').removeClass('active');
        $(this).addClass('active');
        $('.acc-avatar-input').val($(this).data('avatar'));

    });
};
