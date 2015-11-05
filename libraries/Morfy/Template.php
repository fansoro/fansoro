<?php

/**
 * This file is part of the Morfy.
 *
 * (c) Romanenko Sergey / Awilum <awilum@msn.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Template
{
    /**
     * An instance of the Template class
     *
     * @var object
     * @access  protected
     */
    protected static $instance = null;

    /**
     * Fenom object
     *
     * @var object
     * @access  protected
     */
    protected static $fenom = null;

    /**
     * Constructor.
     *
     * @access  protected
     */
    protected function __construct()
    {
        // Create fenom cache directory if its not exists
        !Dir::exists(CACHE_PATH . '/fenom/') and Dir::create(CACHE_PATH . '/fenom/');

        $fenom = FenomExtended::factory(THEMES_PATH . '/' . Config::get('system.theme') . '/',
                                CACHE_PATH . '/fenom/',
                                Config::get('system.fenom'));

        $fenom->addAccessorSmart('config', 'config', Fenom::ACCESSOR_PROPERTY);
        $fenom->config = Config::$config;

        static::$fenom = $fenom;
    }

    /**
     * Get Fenom Object
     */
    public static function fenom()
    {
        return static::$fenom;
    }

    /**
     * Initialize Morfy Template
     *
     *  <code>
     *      Template::init();
     *  </code>
     *
     * @access  public
     */
    public static function init()
    {
        if (! isset(self::$instance)) {
            self::$instance = new Template();
        }
        return self::$instance;
    }
}

class FenomExtended extends \Fenom
{
    use \Fenom\StorageTrait;
}
