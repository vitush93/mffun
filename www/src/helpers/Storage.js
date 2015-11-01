var _ = require('underscore');
var Lockr = require('lockr');

var Storage = (function (Lockr, user) {

    var instance;

    function init() {

        var comment_ups = [];
        var comment_downs = [];
        var quote_ups = [];
        var quote_downs = [];

        function createQuoteRating(qid, value) {
            return {q_id: qid, u_id: user.id, value: value};
        }

        function createCommentRating(cid, value) {
            return {c_id: cid, u_id: user.id, value: value};
        }

        function ratedQuote(qid, value) {
            var rating = createQuoteRating(qid, value);

            // TODO return quote_ratings.contains(rating);
        }

        function ratedComment(cid, value) {
            var rating = createCommentRating(cid, value);

            // TODO return comment_ratings.contains(rating);
        }

        function flush() {
            // TODO
        }

        return {

            isLoggedIn: function () {
                return _.has(user, 'id');
            },

            ratedQuoteUp: function (quote_id) {
                return ratedQuote(quote_id, 1);
            },

            ratedQuoteDown: function (quote_id) {
                return ratedQuote(quote_id, -1);
            },

            ratedCommentUp: function (comment_id) {
                return ratedComment(comment_id, 1);
            },

            ratedCommentDown: function (comment_id) {
                return ratedComment(comment_id, -1);
            },

            rateComment: function (comment_id, value) {
                var rating = createCommentRating(comment_id, value);

                // TODO
            },

            rateQuote: function (quote_id, value) {
                var rating = createQuoteRating(quote_id, value);

                // TODO
            }

        };
    }

    return {
        getInstance: function () {
            if (!instance) {
                instance = init();
            }

            return instance;
        }
    };

})(Lockr, logged_user);

module.exports = Storage;