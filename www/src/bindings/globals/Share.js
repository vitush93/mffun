var Templates = require('../../templates');

module.exports = function () {
    $(document).on('click', function (e) {
        $('.share-dropdown').hide();
    });

    $(document).on('click', '.js-share', function (e) {
        e.preventDefault();
        e.stopPropagation();

        var $this = $(this);
        var $share = $this.next();

        var pos = $this.position();
        var height = $this.outerHeight();

        $share.css({
            top: pos.top + height + 'px',
            left: pos.left + 10 + 'px'
        });

        $share.toggle();
    })
};