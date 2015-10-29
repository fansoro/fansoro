<?php

/**
 * This file is part of the Morfy.
 *
 * (c) Romanenko Sergey / Awilum <awilum@msn.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Cache
{
    /**
     * @var DoctrineCache
     */
    protected $driver;

    /**
     * An instance of the Cache class
     *
     * @var object
     */
    protected static $instance = null;

    /**
     * Protected clone method to enforce singleton behavior.
     *
     * @access  protected
     */
    protected function __clone()
    {
        // Nothing here.
    }

    /**
     * Constructor.
     *
     * @access  protected
     */
    protected function __construct()
    {
    }

    /**
     * Initialize Morfy Plugins
     *
     *  <code>
     *      Cache::init();
     *  </code>
     *
     * @access  public
     */
    public static function init()
    {
        if (! isset(self::$instance)) {
            self::$instance = new Cache();
        }
        return self::$instance;
    }
}
