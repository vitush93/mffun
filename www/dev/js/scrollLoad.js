var ScrollLoad = function (container, loader, bottomOffset, interval) {
    this.lock = false;
    this.bottomOffset = bottomOffset;
    this.currentPage = 1;
    this.interval = interval;
    this.container = container;
    this.loader = loader;

    this.init();
};

ScrollLoad.prototype = {

    load: function (page) {
        console.log('loading page ' + page + '..');
        var $loader = $(this.loader);
        var $container = $(this.container);
        var context = this;
        $.ajax({
            type: 'GET',
            url: window.location.toString(),
            data: 'do=load&page=' + page
        }).done(function (data) {
            $container.append(data);

            context.lock = false;
            context.currentPage++;
        });
    },

    init: function () {
        var context = this;
        setInterval(function () {
            if (context.lock) return;
            var diff = document.body.offsetHeight - (window.innerHeight + window.scrollY);
            if (diff < context.bottomOffset) {
                context.lock = true;
                context.load(context.currentPage);
            }
        }, this.interval);
    }
};