'use strict';

var Backbone = require('backbone');
var Router = require('./routers/AppRouter');

new Router();

Backbone.history.start({
    pushState: true,
    root: '/'
});

require('./bindings/Nette')();
require('./bindings/Navbar')();
require('./bindings/Bootstrap')();
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