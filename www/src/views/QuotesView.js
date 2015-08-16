'use strict';

var _ = require('underscore');
var Backbone = require('backbone');
var QuotesCollection = require('../collections/Quotes');
var QuoteView = require('./QuoteView');

module.exports = Backbone.View.extend({

    tagName: 'div',

    initialize: function () {
        var _this = this;
        this.collection = new QuotesCollection();
        this.collection.fetch({
            success: function () {
                console.log(_this.collection);
            }
        });
    },

    render: function () {
        this.collection.each(function (quote) {
            var view  = new QuoteView({model: quote});

            console.log(view);

            this.$el.append(view.render().el);
        }, this);
    }

});
