<?php

// Send default header and set internal encoding
header('Content-Type: text/html; charset=UTF-8');
function_exists('mb_language') and mb_language('uni');
function_exists('mb_regex_encoding') and mb_regex_encoding('UTF-8');
function_exists('mb_internal_encoding') and mb_internal_encoding('UTF-8');

// Set timezone to default, falls back to system if php.ini not set
date_default_timezone_set(@date_default_timezone_get());

// Gets the current configuration setting of magic_quotes_gpc and kill magic quotes
if (get_magic_quotes_gpc()) {
    function stripslashesGPC(&$value)
    {
        $value = stripslashes($value);
    }
    array_walk_recursive($_GET, 'stripslashesGPC');
    array_walk_recursive($_POST, 'stripslashesGPC');
    array_walk_recursive($_COOKIE, 'stripslashesGPC');
    array_walk_recursive($_REQUEST, 'stripslashesGPC');
}
