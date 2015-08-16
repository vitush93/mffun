'use strict';

require('jquery');

module.exports = function () {

    $('.flash i').on('click', function () {
        $(this).parent('.flash').remove();
    });

    $('#share-icons').bind('click mouseover', function () {
        $('#bar-social').load('/public/front/social.html');
        $(this).unbind('click mouseover');
    });
};