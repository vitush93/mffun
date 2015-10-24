var _ = require('underscore');
var BaseView = require('./BaseView');
var CommentView = require('./CommentView');
var Templates = require('../templates');
var config = require('../config');

var CommentsView = function ($el) {
    this.$el = $el;

    this.init();
};

CommentsView.prototype = Object.create(BaseView.prototype);
CommentsView.prototype.constructor = CommentsView;

CommentsView.prototype.init = function () {
    this.postComment();
};

CommentsView.prototype.postComment = function () {
    var _this = this;
    $('body').on('keyup', '.comment-textarea', function (e) {
        if (e.keyCode != 13) return;

        var $textbox = $(this);

        var text = $textbox.val();
        var qid = $textbox.data('qid');
        var url = config.api.newComment(qid);

        $textbox.val('');

        $.ajax({
            type: 'POST',
            url: url,
            data: {
                text: text
            },
            success: function (data) {
                if (data.success) {
                    var newComment = new CommentView(Templates.comment, _this.$el);
                    var html = newComment.render(data.comment, true);

                    _this.$el.prepend(html);
                }
            }
        });
    });
};

CommentsView.prototype.render = function (data) {
    var _this = this;
    _.each(data[0], function (comment) {
        comment.isAdmin = (comment.user.role == 'admin');
        comment.isMod = (comment.user.role == 'moderator');

        var commentView = new CommentView(Templates.comment, _this.$el);
        commentView.render(comment);

        _.each(data[comment.id], function (child) {
            child.child = true;
            child.isAdmin = (child.user.role == 'admin');
            child.isMod = (child.user.role == 'moderator');

            var childView = new CommentView(Templates.comment, _this.$el);
            childView.render(child);
        });
    });
};

module.exports = CommentsView;