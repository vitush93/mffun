'use strict';

require('jquery');

module.exports = function () {
    $('body').on('click', '#optional-toggle', function () {
        if ($(this).hasClass('active')) {
            $('#optional-content').hide();
        } else {
            $('#optional-content').show();
        }
        $(this).toggleClass('active');
    });
};
