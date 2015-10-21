var _ = require('underscore');
var BaseView = require('./BaseView');
var QuoteView = require('./QuoteView');
var Templates = require('../templates');
var EndlessScroll = require('../helpers/EndlessScroll');
var config = require('../config');

var QuotesView = function ($el) {
    this.$el = $el;

    this.init();
};

QuotesView.prototype = Object.create(BaseView.prototype);
QuotesView.prototype.constructor = QuotesView;

QuotesView.prototype.init = function () {
    this.$loaderContainer = $('#js-loader-container');

    this.bindScrollLoad();
};

QuotesView.prototype.quotes = [];

QuotesView.prototype.$loaderContainer = null;

QuotesView.prototype.addQuoteViews = function(data) {
    var _this = this;
    _.each(data, function(quote) {
        var view = new QuoteView(_this.$el);
        view.render(quote);

        _this.quotes.push(view);
    });
};

QuotesView.prototype.render = function (data) {
    this.addQuoteViews(data);
};

QuotesView.prototype.currentLoad = 1;

QuotesView.prototype.bindScrollLoad = function () {
    var _this = this;

    var scroll = new EndlessScroll();
    scroll.callback = function () {
        if (_this.currentLoad == config.endlessScroll.maxLoadsPerPage) {
            _this.$loaderContainer.html(Templates.more({
                action: window.action,
                page: (1 + Number(_this.getPage()))
            }));

            return;
        }
        _this.$loaderContainer.html(Templates.loader());

        $.getJSON(config.api.quotes(_this.getAction(), config.endlessScroll.itemsPerLoad, _this.getOffset()), function (data) {
            _this.addQuoteViews(data);

            _this.currentLoad++;

            scroll.lock = false;
            _this.$loaderContainer.html('');
        }).error(function () {
            _this.$loaderContainer.html(Templates.reload());
        });
    }
};

QuotesView.prototype.getAction = function () {
    return window.action;
};

QuotesView.prototype.getPage = function () {
    if (window.page.length == 0) {
        return 1;
    }

    return window.page;
};

QuotesView.prototype.getOffset = function () {
    var itemsPerPage = config.endlessScroll.itemsPerLoad * config.endlessScroll.maxLoadsPerPage;
    var pageOffset = (this.getPage() - 1) * itemsPerPage;
    var loadOffset = (this.currentLoad * config.endlessScroll.itemsPerLoad);

    return pageOffset + loadOffset;
};

module.exports = QuotesView;