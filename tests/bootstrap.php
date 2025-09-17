<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

// Ne charger .env que si APP_ENV n’est pas déjà défini (cas Heroku = prod)
if (!isset($_SERVER['APP_ENV']) && !isset($_ENV['APP_ENV'])) {
    if (method_exists(Dotenv::class, 'bootEnv')) {
        (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
    } else {
        (new Dotenv())->loadEnv(dirname(__DIR__).'/.env');
    }
}

if ($_SERVER['APP_DEBUG'] ?? false) {
    umask(0000);
}