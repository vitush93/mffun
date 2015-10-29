var Lockr = require('lockr');
var config = require('../config');

var UserStorage = {

    data: null,

    namespace: null,

    init: function (data) {
        this.data = data;
        this.namespace = 'User-' + data.id;

        this.flush();
    },

    flush: function () {
        Lockr.set(this.namespace, this.data);
    },

    add: function (key, value) {
        this.data[key].push(value);

        this.flush();
    },

    rateComment: function (rating) {
        this.add('rated_comments', rating);
    },

    rateQuote: function (rating) {
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
