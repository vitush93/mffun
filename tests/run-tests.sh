#!/usr/bin/env bash

NETTE_TESTER="../vendor/bin/tester"
PHP="/c/xampp/php/php.exe"
PHP_INI="/c/xampp/php/php.ini"

eval ${NETTE_TESTER} -p ${PHP} -c ${PHP_INI} "\."