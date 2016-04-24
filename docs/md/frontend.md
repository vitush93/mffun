# Frontend development

JavaScript and CSS stuff.

## Styling

Application uses the LESS as CSS preprocessor, governed by Grunt.js. To see list of relevant Grunt commands, please see the Installation & Configuration.

Dependent CSS libraries such as Bootstrap and Font-awesome are referenced from a **main.less** file, which is then passed to LESS compiler - everything is then concatenated into one CSS file.

All LESS source files are located in `www/assets/less` folder. Compiled **style.css** is then located in `www/public/front/css/` folder.

## Application

Front-end application is divided into modules using CommonJS module format. Modules are assembled with Grunt. Sources are located in `www/src` folder.

### Routing and controllers

Routing is handled by Backbone.js's `Backbone.Router`. Controllers are simply functions passed to the router as a callback. See `www/src/router` and `www/src/controllers` folders for source.

There is a middleware attached to the router the *UserMiddleware*, which fires on every request before router resolves URI. UserMiddleware handles user login status detection and cached data invalidation.

### Views and model

Backbone's views and models are not used in this project. Instead, views handle most of the logic and API calls. See `www/src/views` for sources.

Every views extends the *BaseView* object which defines the *render* method responsible for rendering the templates assigned to the view.

### Templates

MFFun uses Handlebars templating engine for rendering quotes and comments boxes (it is a bit nicer way to do the endless-scroll).

Handlebars does not allow much logic in the template itself, so most of data preprocessing is done in *render* method of parent view.

### Local data

The application stores user info (such as quotes and comments user had rated and User entity itself) in browser local storage. Data is loaded on login and then removed on logout (manual or automatic).

On page load, all JSON data needed to render the page is printed to HTML by server avoid unnecessary request. Those data can be accessed in front-end application globally. Preloaded quotes can be accessed via `window.quotes` variable. User data storage is handled by *UserStorage* class which uses the *Lockr* library - abstraction over the local storage API.