<?php

// Fansoro requires PHP 5.5.0 or greater
version_compare(PHP_VERSION, "5.5.0", "<") and exit("Fansoro requires PHP 5.5.0 or greater.");

// Register the auto-loader.
require_once __DIR__ . '/vendor/autoload.php';

// Initialize Fansoro Application
Fansoro::init();
