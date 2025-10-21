<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

if (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}

if ($_SERVER['APP_DEBUG']) {
    umask(0000);
}

// Set up test database
if ('test' === $_SERVER['APP_ENV']) {
    // Drop existing test database if it exists
    passthru(sprintf(
        'php "%s/bin/console" doctrine:database:drop --env=test --if-exists --force --no-interaction 2>&1',
        dirname(__DIR__)
    ));

    // Create test database
    passthru(sprintf(
        'php "%s/bin/console" doctrine:database:create --env=test --no-interaction 2>&1',
        dirname(__DIR__)
    ));

    // Create database schema
    passthru(sprintf(
        'php "%s/bin/console" doctrine:schema:create --env=test --no-interaction 2>&1',
        dirname(__DIR__)
    ));
}
