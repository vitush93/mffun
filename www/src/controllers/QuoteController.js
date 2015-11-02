var Templates = require('../templates');
var config = require('../config');
var QuoteView = require('../views/QuoteView');
var CommentsView = require('../views/CommentsView');
var UserStorage = require('../helpers/UserStorage');

module.exports = function (id) {
    var quoteView = new QuoteView(Templates.quote_detail, $('#content-load'), id);
    var data = window.quote;
    data.logged_user = UserStorage.user;

    quoteView.render(data);

    $.getJSON(config.api.comments(id), function (data) {
        var comments = new CommentsView($('#js-comments-load'));

        comments.render(data);
    });
};