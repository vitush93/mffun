var QuoteView = require('../views/QuoteView');

module.exports = function () {
    var $container = $('#content-load');
    var view = new QuoteView($container);

    view.render();
};