module.exports = {

    endlessScroll: {
        itemsPerLoad: 10,
        maxLoadsPerPage: 10
    },

    cache: {
        user: 'cachedUser',
        quoteRatingsPositive: 'cachedPositiveQuoteRatings',
        quoteRatingsNegative: 'cachedNegativeQuoteRatings',
        commentRatingsPositive: 'cachedPositiveCommentRatings',
        commentRatingsNegative: 'cachedNegativeCommentRatings'
    },

    api: {
        quote: function (qid) {
            return '/api/quote/single?id=' + qid;
        },

        rateUp: function (qid) {
            return '/api/quote/rate-up?id=' + qid;
        },

        rateDown: function (qid) {
            return '/api/quote/rate-down?id=' + qid;
        },

        rateComment: function (rate, id) {
            return '/api/comment/rate?id=' + id + '&rate=' + rate;
        },

        quotes: function (action, limit, offset, url_id) {
            if (url_id) {
                return '/api/quote/' + action + '?limit=' + limit + '&offset=' + offset + '&id=' + url_id;
            } else {
                return '/api/quote/' + action + '?limit=' + limit + '&offset=' + offset;
            }
        },

        comments: function (qid) {
            return '/api/comment/quote?id=' + qid;
        },

        comment: function (qid, limit, offset) {
            return '/api/comment/thread?id=' + qid + '&limit=' + limit + '&offset=' + offset;
        },

        newComment: function (qid, comm) {
            if (comm) {
                return '/api/comment/post?id=' + comm + '&quote=' + qid;
            } else {
                return '/api/comment/post?quote=' + qid;
            }
        },

        user: function () {
            return '/api/homepage/user';
        }
    }
};
