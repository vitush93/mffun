var Templates = require('../templates');

var FlashMessage = {
    $el: null,

    show: function (msg) {
        if (this.$el == null) this.$el = $('#js-flashes');

        this.render(msg);
    },

    render: function (msg) {
        this.$el.append(Templates.flash(msg));
    }
};

module.exports = FlashMessage;