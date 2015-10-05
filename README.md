# MFFun

This repository contains source code of MFFun website, which has been created as part of my Bachelor\'s thesis and Individual Software Project.

## Overview

This project is powered by:

- Nette framework
- Doctrine ORM
- Backbone.js
- Bootstrap

## Installation and configuration

1. Copy `app/config.local.sample.neon` to `app/config.local.neon` and modify the configuration file to match your environment.
2. Create database using Doctrine CLI by running `php www/index.php orm:schema-tool:update --force`.