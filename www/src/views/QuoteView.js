'use strict';

var Backbone = require('backbone');
var Templates = require('../templates');

module.exports = Backbone.View.extend({

    tagName: 'div',

    className: 'row article',

    template: Templates.quote,

    events: {
        'click .q-rate-up': 'rateUp',
        'click .q-rate-down': 'rateDown'
    },

    rateUp: function (e) {
        if (e) e.preventDefault();
        // TODO implement rateUp
    },

    rateDown: function (e) {
        if (e) e.preventDefault();
        // TODO implement rateDown
    },

    render: function () {
        this.$el.html(this.template(this.model));

        return this;
    }

});
