var Templates = require('../templates');
var QuoteView = require('../views/QuoteView');
var CommentsView = require('../views/CommentsView');

module.exports = function (id) {
    var view = new QuoteView(Templates.quote_detail, $('#content-load'), id);
    var data = window.quote;
    data.logged_user = window.logged_user;
    view.render(data);

    var commentsView = new CommentsView($('#js-comments-load'));
    commentsView.render(window.quote_comments);
};