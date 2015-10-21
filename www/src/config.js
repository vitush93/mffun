module.exports = {

    endlessScroll: {
        itemsPerLoad: 10,

        maxLoadsPerPage: 10
    },

    api: {
        quote: function (qid) {

        },

        rateUp: function (qid) {
            return '/api/quote/rate-up/' + qid;
        },

        rateDown: function (qid) {
            return '/api/quote/rate-down/' + qid;
        },

        quotes: function (action, limit, offset, url_id) {
            if (url_id) {
                return '/api/quote/' + action + '/' + url_id + '/?limit=' + limit + '&offset=' + offset;
            } else {
                return '/api/quote/' + action + '/?limit=' + limit + '&offset=' + offset;
            }

        }
    }
};
