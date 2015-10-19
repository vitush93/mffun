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
require('./bindings/Rating')();