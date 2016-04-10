<?php
namespace Fansoro;

// Register the auto-loader.
$loader = require __DIR__ . '/vendor/autoload.php';

// Check PHP Version
version_compare(PHP_VERSION, PHP_MIN_VERSION, "<") and exit('Fansoro requires PHP '.PHP_MIN_VERSION.' or greater.');

// Get Fansoro Instance
$app = Fansoro::instance();

// Run Fansoro Application
$app->run();
