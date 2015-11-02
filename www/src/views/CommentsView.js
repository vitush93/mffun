var _ = require('underscore');
var BaseView = require('./BaseView');
var CommentView = require('./CommentView');
var Templates = require('../templates');
var config = require('../config');
var UserStorage = require('../helpers/UserStorage');

var CommentsView = function ($el) {
    this.$el = $el;

    this.init();
};

CommentsView.prototype = Object.create(BaseView.prototype);
CommentsView.prototype.constructor = CommentsView;

CommentsView.prototype.init = function () {
    this.postComment();
    this.postReply();
    this.rate();
};

CommentsView.prototype.rate = function () {
    $(document).on('click', '.rate', function (e) {
        e.preventDefault();

        if (UserStorage.user == undefined) {
            window.location.href = "/sign/in";

            return;
        }

        var $this = $(this);

        var rate = $this.data('rate');
        var comment_id = $this.data('comment');

        $.getJSON(config.api.rateComment(rate, comment_id), function (data) {
            if (data.success == false) return;

            var $parent = $this.parents('.comment-controls');

            $parent.find('.up span').html(data.up);
            $parent.find('.down span').html(data.down);

            if ($this.hasClass('active')) {
                $this.removeClass('active');
            } else {
                $this.parents('.comment-controls').find('.rate').removeClass('active');
                $this.toggleClass('active');
            }

            if (rate == 'up') {
                UserStorage.rateComment(comment_id, 1);
            } else if (rate == 'down') {
                UserStorage.rateComment(comment_id, -1);
            }
        });
    });
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
                if (data.success == false) return;

                var newComment = new CommentView(Templates.comment, _this.$el, data.comment.id, data.comment.parent);
                var html = newComment.render(data.comment, true);

                _this.$el.prepend(html);
            }
        });
    });
};

CommentsView.prototype.postReply = function () {
    var _this = this;
    $('body').on('keyup', '.js-post-reply', function (e) {
        if (e.keyCode != 13) return;

        var qid = $(this).data('quote');
        var parent = $(this).data('parent');
        var text = $(this).val();

        var $this = $(this);
        var url = config.api.newComment(qid, parent);
        $.ajax({
            method: 'POST',
            url: url,
            data: {
                text: text
            },
            success: function (data) {
                if (data.success == false) return;

                var newComment = new CommentView(Templates.comment, _this.$el, data.comment.id, data.comment.parent);

                data.comment.child = true;
                data.comment.isAdmin = (data.comment.user.role == 'admin');
                data.comment.isMod = (data.comment.user.role == 'moderator');
                var html = newComment.render(data.comment, true);

                var $children = $('.js-child-' + parent);
                if ($children.size() == 0) {
                    $this.parents('.comment').after(html);
                } else {
                    $children.last().after(html);
                }

                $this.remove();
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