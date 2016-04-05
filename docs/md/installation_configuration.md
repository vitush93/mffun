# Installation & Configuration

Here can find out how to setup the project on a new server.

## Requirements

Technical requirements to run this project are as follows:

- **PHP 5.5+**
- **MySQL 5.6+**
- Modern browser supporting **HTML5** APIs to run the application

Note: In order for LDAP authentication to function properly, it is required to have a **php-ldap** module installed on your system.

## Installation

Fresh installation is much easier using these tools:

- Composer package manager to download necessary PHP libraries
- npm package manager to download necessary Node modules

You will also need `bower` and `grunt` installed on your system.

In project's root folder run this command to download PHP packages:

```
composer install
```

in `www` folder run:

```
npm install
```

Note: sometimes Doctrine will incorrectly tell you that no changes had been detected. Just delete contents of the `temp/` folder to refresh it.

After-install utils are located in `bin/` folder. It is recommended to use these to for example add a new user to the database.

### Configuration

Application configuration files (NEON syntax) are located in `app/config` folder:

- **config.local.neon** contains Doctrine ORM database configuration and Nette extension configuration.
- **config.neon** contains main Nette framework configuration.
- **mailer.neon** contains configuration for SMTP mailer used for notification system.
- **services.neon** contains list of services for DI container configuration.

**Routing** is configured within `app/router/RouterFactory.php` file.

### Database

You can generate clean database schema by running this command in `www/` folder:

```
php index.php orm:schema-tool:update --force
```

To see full list of application's CLI commands run `php index.php`.

### Generating assets

To generate CSS and JavaScript assets run following command in `www/` folder:

```
grunt release
```

This will compile, minify and optimize your assets - this operation takes quite a long time; `style.css` and `bundle.js` files will be generated in `www/front/css` and `www/front/js` folders.
