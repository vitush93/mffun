var UserStorage = require('../helpers/UserStorage');

module.exports = (function () {
    $(document).on('click', '.js-logout-button', function (e) {
        e.preventDefault();

        UserStorage.drop();

        window.location.href = $(this).attr('href');
    });
})();