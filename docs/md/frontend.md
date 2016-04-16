# Frontend development

JavaScript and CSS stuff.

## Styling

Application uses the LESS as CSS preprocessor, governed by Grunt.js. To see list of relevant Grunt commands, please see the Installation & Configuration.

Dependent CSS libraries such as Bootstrap and Font-awesome are referenced from a **main.less** file, which is then passed to LESS compiler - everything is then concatenated into one CSS file.

All LESS source files are located in `www/assets/less` folder. Compiled **style.css** is then located in `www/public/front/css/` folder.

## Application

Front-end application is divided into modules using CommonJS module format. Modules are assembled with Grunt. Sources are located in `www/src` folder.

### Routing and controllers

Routing is handled by Backbone.js's `Backbone.Router`. Controllers are simply functions passed to a router as a callback. See `www/src/router` and `www/src/controllers` folders for source.

### Views and model

### Templates