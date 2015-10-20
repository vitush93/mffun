var $ = require('jquery');
var QuotesView = require('../views/QuotesView');

module.exports = function () {
    var $container = $('#content-load');

    var view = new QuotesView($container);

    view.render(window.quotes);
};