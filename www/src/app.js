'use strict';

var Backbone = require('backbone');
var Router = require('./routers/AppRouter');

new Router();

Backbone.history.start({
    pushState: true,
    root: '/'
});

window.$ = window.jQuery = require('jquery');
require('bootstrap');

require('./bindings/Nette')();
require('./bindings/Navbar')();
require('./bindings/GoogleAnalytics')();
require('./bindings/ScrollLoad')();
require('./bindings/KeyboardShortcuts')();
require('./bindings/Autocomplete')();
require('./bindings/Comments')();
require('./bindings/AddQuote')();
require('./bindings/AvatarPicker')();
require('./bindings/Global')();
require('./bindings/CommentRating')();
require('./bindings/QuoteRating')();