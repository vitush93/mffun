
module.exports = {

    endlessScroll: {
        itemsPerLoad: 10,

        maxLoadsPerPage: 10
    },

    api: {
        quote: function (qid) {

        },

        quotes: function (action, limit, offset) {
            return '/api/quote/' + action + '/?limit=' + limit + '&offset=' + offset;
        }
    }
};
