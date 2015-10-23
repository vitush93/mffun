var Templates = require('../templates');
var config = require('../config');
var QuoteView = require('../views/QuoteView');

module.exports = function (id) {
    var view = new QuoteView(Templates.quote_detail, $('#content-load'), id);
    var data = window.quote;
    data.logged_user = window.logged_user;

    view.render(data);
};