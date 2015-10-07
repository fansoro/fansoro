<?php

/**
 * Define the path to the root directory (without trailing slash).
 */
define('ROOT_DIR', __DIR__);

/**
 * Define the path to the content directory (without trailing slash).
 */
define('CONTENT_PATH', ROOT_DIR .'/content');

/**
 * Define the path to the libraries directory (without trailing slash).
 */
define('LIBRARIES_PATH', ROOT_DIR .'/libraries');

/**
 * Define the path to the themes directory (without trailing slash).
 */
define('THEMES_PATH', ROOT_DIR .'/themes');

/**
 * Define the path to the plugins directory (without trailing slash).
 */
define('PLUGINS_PATH', ROOT_DIR  .'/plugins');

/**
 * Load Morfy
 */
require LIBRARIES_PATH . '/Morfy/Morfy.php';



/**
 * First check for installer then go
 */
if (file_exists('install.php')) {
    if (isset($_GET['install']) && $_GET['install'] == 'done') {

        // Try to delete install file if not DELETE MANUALLY !!!
        @unlink('install.php');

        // Redirect to main page
        header('location: index.php');
    } else {
        include 'install.php';
    }
} else {
    /**
     * Run Morfy Application with Morfy config
     */
    Morfy::factory()->run();
}
