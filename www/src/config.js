module.exports = {

    endlessScroll: {
        itemsPerLoad: 10,

        maxLoadsPerPage: 10
    },

    api: {
        quote: function (qid) {

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
