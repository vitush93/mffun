var Templates = require('../templates');
var config = require('../config');
var QuoteView = require('../views/QuoteView');

module.exports = function (id) {
    console.log(window.quote);

    var view = new QuoteView(Templates.quote_detail, $('#content-load'), id);
    view.render(window.quote);
};