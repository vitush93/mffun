var $ = require('jquery');
var QuotesView = require('../views/QuotesView');

module.exports = function () {
    var $container = $('#content-load');

    var view = new QuotesView($container);
    $.getJSON('/api/quote/?limit=10&offset=0', function (data) {
        console.log(data);

        view.render(data);
    });
};