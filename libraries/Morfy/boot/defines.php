<?php

// Define the path to the root directory (without trailing slash).
define('ROOT_DIR', str_replace(DIRECTORY_SEPARATOR, '/', getcwd()));

// Define the path to the storage directory (without trailing slash).
define('STORAGE_PATH', ROOT_DIR .'/storage');

// Define the path to the blocks directory (without trailing slash).
define('BLOCKS_PATH', STORAGE_PATH .'/blocks');

// Define the path to the pages directory (without trailing slash).
define('PAGES_PATH', STORAGE_PATH .'/pages');

// Define the path to the libraries directory (without trailing slash).
define('LIBRARIES_PATH', ROOT_DIR .'/libraries');

// Define the path to the themes directory (without trailing slash).
define('THEMES_PATH', ROOT_DIR .'/themes');

// Define the path to the plugins directory (without trailing slash).
define('PLUGINS_PATH', ROOT_DIR  .'/plugins');

// Define the path to the cache directory (without trailing slash).
define('CACHE_PATH', ROOT_DIR  .'/cache');

// Define the path to the config directory (without trailing slash).
define('CONFIG_PATH', ROOT_DIR  .'/config');
