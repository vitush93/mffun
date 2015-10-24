var BaseView = require('./BaseView');

var CommentView = function (template, $el, id) {
    this.template = template;
    this.$container = $el;
    this.id = id;

    this.init();
};

CommentView.prototype = Object.create(BaseView.prototype);
CommentView.prototype.constructor = CommentView;

CommentView.prototype.$container = null;

CommentView.prototype.id = 0;

CommentView.prototype.init = function () {
    this.$el = $('<div></div>');
    this.$el.addClass('js-comment-view');

    this.showResponseBox();
    this.postReply();
};

CommentView.prototype.postReply = function () {
    $('body').on('keyup', '.js-post-reply', function (e) {
        if (e.keyCode != 13) return;

        $(this).after('<p>test</p>');
        $(this).remove();
        // TODO post a reply
    });
};

CommentView.prototype.showResponseBox = function () {
    var _this = this;
    $('body').on('click', '.reply', function (e) {
        e.preventDefault();

        var replyButton = _this.$el.find('.reply')[0];
        if (e.target != replyButton) return;

        $(this).parent('div').append('<textarea maxlength="1000" class="js-post-reply" name="reply-content"></textarea>');

        var textarea = $('textarea');
        textarea.focus();
        textarea.autogrow();
    });
};

CommentView.prototype.render = function (data, onlyHTML) {
    data.logged_user = window.logged_user;
    data.show_reply = (window.logged_user && data.parent == 0);

    var html = this.template(data);

    if (onlyHTML) {
        return html;
    } else {
        this.$el.html(html);
        this.$container.append(this.$el);
    }
};

module.exports = CommentView;