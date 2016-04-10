<?php
namespace Fansoro;

use Pimple\Container as Container;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

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

class Fansoro extends Container
{
    /**
     * An instance of the Fansoro class
     *
     * @var object
     * @access protected
     */
    protected static $instance;

    /**
     * The version of Fansoro
     *
     * @var string
     */
    const VERSION = 'X.X.X alfa';

    /**
     * Init Fansoro Application
     */
    protected static function init()
    {
        $container = new static();

        $container['filesystem'] = function ($c) {
            return new Filesystem();
        };

        $container['finder'] = function ($c) {
            return new Finder();
        };

        $container['config'] = function ($c) {
            return new Config($c);
        };

        $container['twig'] = function ($c) {
            return new Twig($c);
        };

        $container['plugins'] = function ($c) {
            return new Plugins($c);
        };

        $container['plugins']->init();

        $container['pages'] = function ($c) {
            return new Pages($c);
        };

        return $container;
    }

    /**
     * Run Fansoro Application
     */
    public function run()
    {
        // Turn on output buffering
        ob_start();

        // Display Errors
        $this['config']->get('system.errors.display') and error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_USER_DEPRECATED);

        // Set internal encoding
        function_exists('mb_language') and mb_language('uni');
        function_exists('mb_regex_encoding') and mb_regex_encoding($this['config']->get('system.charset'));
        function_exists('mb_internal_encoding') and mb_internal_encoding($this['config']->get('system.charset'));

        // Set default timezone
        date_default_timezone_set($this['config']->get('system.timezone'));

        // Render The Page
        $this['twig']->renderPage($this['pages']->getPage(\Url::getUriString()));

        // Flush (send) the output buffer and turn off output buffering
        ob_end_flush();
    }

    /**
     * Get Fansoro Application Instance
     *
     * @access public
     * @return object
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = static::init();
            FansoroTrait::setFansoro(self::$instance);
        }
        return self::$instance;
    }
}
