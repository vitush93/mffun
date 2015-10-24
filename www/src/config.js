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

        },

        comments: function (qid) {
            return '/api/?presenter=Comment&action=quote&id=' + qid;
        },

        comment: function (qid, limit, offset) {
            return '/api/?presenter=Comment&action=thread&id=' + qid + '&limit=' + limit + '&offset=' + offset;
        },

        newComment: function (qid, comm) {
            if (comm) {
                return '/api/?presenter=Comment&action=post&id=' + comm + '&quote=' + qid;
            } else {
                return '/api/?presenter=Comment&action=post&quote=' + qid;
            }

        }
    }
};
