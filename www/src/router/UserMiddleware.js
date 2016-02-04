var config = require('../config.js');
var UserStorage = require('../helpers/UserStorage');

/**
 * Remove cached user data after autologout.
 */
module.exports = function () {
    $.get(config.api.user(), function (data) {
        var userLoggedIn = !!data.success;
        var cachedDataExists = !!UserStorage.user;

        if (!userLoggedIn && cachedDataExists) {
            UserStorage.drop();
        }
    });
};