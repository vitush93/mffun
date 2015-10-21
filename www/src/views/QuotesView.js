var _ = require('underscore');
var BaseView = require('./BaseView');
var QuoteView = require('./QuoteView');
var Templates = require('../templates');
var EndlessScroll = require('../helpers/EndlessScroll');
var config = require('../config');

// TODO move API urls to config
var QuotesView = function ($el, model) {
    this.$el = $el;
    this.model = model;

    this.init();
};

QuotesView.prototype.init = function () {
    this.$loaderContainer = $('#content-load');

    this.initQuotes();
    this.bindScrollLoad();
};

QuotesView.prototype = BaseView.prototype;

QuotesView.prototype.quotes = [];

QuotesView.prototype.$loaderContainer = null;

QuotesView.prototype.initQuotes = function() {
    var _this = this;
    _.each(this.model, function(quote) {
        _this.quotes.push(new QuoteView(Templates.quote, _this.$el, quote));
    });
};

QuotesView.prototype.render = function () {
    var html = '';

    var _this = this;
    _.each(_this.quotes, function(quoteView) {
        _this.$el.append(quoteView.render());
    });

    this.$el.append(html);
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

        $.getJSON('/api/quote/' + _this.getAction() + '/?limit=' + config.endlessScroll.itemsPerLoad + '&offset=' + _this.getOffset(), function (data) {
            _this.render(data);

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