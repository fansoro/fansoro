<?php

// Morfy requires PHP 5.3.0 or greater
version_compare(PHP_VERSION, "5.3.0", "<") and exit("Morfy requires PHP 5.3.0 or greater.");

// Register the auto-loader.
require_once __DIR__ . '/vendor/autoload.php';

// Initialize Morfy Application
Morfy::init();
