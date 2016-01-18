<?php

 /**
  * Fansoro
  *
  * @package Fansoro
  * @author Romanenko Sergey / Awilum <awilum@msn.com>
  * @link http://fansoro.org
  *
  * For the full copyright and license information, please view the LICENSE
  * file that was distributed with this source code.
  */

class Fansoro
{
    /**
     * An instance of the Fansoro class
     *
     * @var object
     * @access protected
     */
    protected static $instance = null;

    /**
     * The version of Fansoro
     *
     * @var string
     */
    const VERSION = '2.0.2';

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
        // Init Config
        Config::init();

        // Turn on output buffering
        ob_start();

        // Display Errors
        Config::get('system.errors.display') and error_reporting(-1);

        // Set internal encoding
        function_exists('mb_language') and mb_language('uni');
        function_exists('mb_regex_encoding') and mb_regex_encoding(Config::get('system.charset'));
        function_exists('mb_internal_encoding') and mb_internal_encoding(Config::get('system.charset'));

        // Set default timezone
        date_default_timezone_set(Config::get('system.timezone'));

        // Start the session
        Session::start();

        // Init Cache
        Cache::init();

        // Init Plugins
        Plugins::init();

        // Init Blocks
        Blocks::init();

        // Init Pages
        Pages::init();

        // Flush (send) the output buffer and turn off output buffering
        ob_end_flush();
    }

    /**
     * Initialize Fansoro Application
     *
     *  <code>
     *      Fansoro::init();
     *  </code>
     *
     * @access public
     * @return object
     */
    public static function init()
    {
        return !isset(self::$instance) and self::$instance = new Fansoro();
    }
}
