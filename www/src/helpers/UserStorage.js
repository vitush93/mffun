var _ = require('underscore');
var Lockr = require('lockr');
var config = require('../config');

var UserStorage = (function () {

    /**
     * Gets correct localStorage index.
     *
     * @param type
     * @param value
     * @returns {string}
     */
    function resolveIndex(type, value) {
        if (type == 'comment') {
            return value == 1 ? config.cache.commentRatingsPositive : config.cache.commentRatingsNegative;
        } else if (type == 'quote') {
            return value == 1 ? config.cache.quoteRatingsPositive : config.cache.quoteRatingsNegative;
        }
    }

    return {

        drop: function () {
            Lockr.flush();
        },

        init: function (data) {

            // save all user data
            Lockr.set(config.cache.user, data);

            // save user's comment ratings
            _.each(data.rated_comments, function (r) {
                var index = resolveIndex('comment', r.value);

                Lockr.sadd(index, r.c_id);
            });

            // save user's quote ratings
            _.each(data.rated_quotes, function (r) {
                var index = resolveIndex('quote', r.value);

                Lockr.sadd(index, r.q_id);
            });
        },

        ratedComment: function (comment_id, value) {
            var index = resolveIndex('comment', value);

            return Lockr.sismember(index, comment_id);
        },

        ratedQuote: function (quote_id, value) {
            var index = resolveIndex('quote', value);

            return Lockr.sismember(index, quote_id);
        },

        rateComment: function (comment_id, value) {
            var index = resolveIndex('comment', value);
            var remove_index = resolveIndex('comment', -1 * value);

            Lockr.srem(remove_index, comment_id);
            if (Lockr.sismember(index, comment_id)) {
                Lockr.srem(index, comment_id);
            } else {
                Lockr.sadd(index, comment_id);
            }
        },

        rateQuote: function (quote_id, value) {
            var index = resolveIndex('quote', value);
            var remove_index = resolveIndex('quote', -1 * value);

            Lockr.srem(remove_index, quote_id);
            if (Lockr.sismember(index, quote_id)) {
                Lockr.srem(index, quote_id);
            } else {
                Lockr.sadd(index, quote_id);
            }
        }
    }
})();

module.exports = UserStorage;
