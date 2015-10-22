module.exports = {

    endlessScroll: {
        itemsPerLoad: 10,

        maxLoadsPerPage: 10
    },

    api: {
        quote: function (qid) {
            return '/api/?presenter=Quote&action=single&id=' + qid;
        },

        rateUp: function (qid) {
            return '/api/?presenter=Quote&action=rateUp&id=' + qid;
        },

        rateDown: function (qid) {
            return '/api/?presenter=Quote&action=rateDown&id=' + qid;
        },

        quotes: function (action, limit, offset, url_id) {
            if (url_id) {
                return '/api/?presenter=Quote&action=' + action + '&limit=' + limit + '&offset=' + offset + '&id=' + url_id;
            } else {
                return '/api/?presenter=Quote&action=' + action + '&limit=' + limit + '&offset=' + offset;
            }

        }
    }
};
