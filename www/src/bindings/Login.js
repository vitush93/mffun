var UserStorage = require('../helpers/UserStorage');
var FlashMessage = require('../helpers/FlashMessage');

module.exports = function () {

    $('#frm-loginForm').submit(function (e) {
        e.preventDefault(); // avoid to execute the actual submit of the form.

        var url = '/';

        $.ajax({
            type: 'POST',

            url: url,

            data: $(this).serialize(), // serializes the form's elements.

            success: function (data) {
                if (data.success) {
                    UserStorage.init(data);

                    window.location.href = '/';
                } else {
                    FlashMessage.show({
                        type: 'error',
                        message: data.message
                    });
                }
            }
        });
    });
};