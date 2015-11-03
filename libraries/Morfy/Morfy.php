<?php

 /**
  * Morfy
  *
  * @package Morfy
  * @author Romanenko Sergey / Awilum <awilum@msn.com>
  * @link http://morfy.org
  *
  * For the full copyright and license information, please view the LICENSE
  * file that was distributed with this source code.
  */

class Morfy
{
    /**
     * An instance of the Morfy class
     *
     * @var object
     */
    protected static $instance = null;

    /**
     * The version of Morfy
     *
     * @var string
     */
    const VERSION = '2.X.X';

    /**
     * Protected clone method to enforce singleton behavior.
     *
     * @access protected
     */
    protected function __clone()
    {
        // Nothing here.
    }

    /**
     * Constructor.
     *
     * @access protected
     */
    protected function __construct()
    {
        // Set configs
        Config::setFile(CONFIG_PATH . '/site.yml');
        Config::setFile(CONFIG_PATH . '/system.yml');

        // Send default header and set internal encoding
        header('Content-Type: text/html; charset='.Config::get('system.charset'));
        function_exists('mb_language') and mb_language('uni');
        function_exists('mb_regex_encoding') and mb_regex_encoding(Config::get('system.charset'));
        function_exists('mb_internal_encoding') and mb_internal_encoding(Config::get('system.charset'));

        // Set default timezone
        @ini_set('date.timezone', Config::get('system.timezone'));
        if (function_exists('date_default_timezone_set')) {
            date_default_timezone_set(Config::get('system.timezone'));
        } else {
            putenv('TZ='.Config::get('system.timezone'));
        }

        // Start the session
        Session::start();

        // Init Cache
        Cache::init();

        // Sanitize URL to prevent XSS - Cross-site scripting
        Url::runSanitizeURL();

        // Init Plugins
        Plugins::init();

        // Init Pages
        Pages::init();
    }

    /**
     * Initialize Morfy Application
     *
     *  <code>
     *      Morfy::init();
     *  </code>
     *
     * @access public
     * @return object
     */
    public static function init()
    {
        if (! isset(self::$instance)) {
            self::$instance = new Morfy();
        }
        return self::$instance;
    }
}
