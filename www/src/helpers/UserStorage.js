var _ = require('underscore');
var Lockr = require('lockr');
var config = require('../config');

var UserStorage = {

    data: null,

    namespace: null,

    init: function (data) {
        if (data == null) {
            if (this.isLoggedIn()) {
                this.setNamespace(window.logged_user.id);

                this.data = Lockr.get(this.namespace);
            }
        } else {
            this.data = data;
            this.setNamespace(data.id);

            this.flush();
        }
    },

    ratedCommentUp: function (comment_id) {
        if (!this.isLoggedIn()) return false;

        var ratings = this.getCommentRatings();

        return _.some(ratings, function (item) {
            return item.c_id == comment_id && item.value == 1;
        });
    },

    ratedCommentDown: function (comment_id) {
        if (!this.isLoggedIn()) return false;

        var ratings = this.getCommentRatings();

        return _.some(ratings, function (item) {
            return item.c_id == comment_id && item.value == -1;
        });
    },

    ratedQuoteUp: function (quote_id) {
        if (!this.isLoggedIn()) return false;

        var ratings = this.getQuoteRatings();

        return _.some(ratings, function (item) {
            return item.q_id == quote_id && item.value == 1;
        })
    },

    ratedQuoteDown: function (quote_id) {
        if (!this.isLoggedIn()) return false;

        var ratings = this.getQuoteRatings();

        return _.some(ratings, function (item) {
            return item.q_id == quote_id && item.value == -1;
        })
    },

    setNamespace: function (user_id) {
        this.namespace = 'User-' + user_id;
    },

    isLoggedIn: function () {
        return _.has(window.logged_user, 'id');
    },

    flush: function () {
        Lockr.set(this.namespace, this.data);
    },

    add: function (key, value) {
        this.data[key].push(value);

        this.flush();
    },

    rateComment: function (comment_id, rate) {
        var value = (rate == 'up') ? 1 : -1;

        var rating = {
            c_id: comment_id,
            value: value,
            u_id: window.logged_user.id
        };

        this.add('rated_comments', rating);
    },

    rateQuote: function (quote_id, rate) {
        var value = (rate == 'up') ? 1 : -1;

        var rating = {
            q_id: quote_id,
            u_id: window.logged_user.id,
            value: value
        };

        this.add('rated_quotes', rating);
    },

    getCommentRatings: function () {
        return this.data['rated_comments'];
    },

    getQuoteRatings: function () {
        return this.data['rated_quotes'];
    },

    fetch: function (callback) {
        var _this = this;
        $.getJSON(config.api.user(), function (data) {
            _this = data;

            callback(data);
        });
    }
};

module.exports = UserStorage;
