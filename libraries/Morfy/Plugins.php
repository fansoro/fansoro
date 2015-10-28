<?php

/**
 * This file is part of the Morfy.
 *
 * (c) Romanenko Sergey / Awilum <awilum@msn.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Plugins
{
    /**
     * An instance of the Plugins class
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
        if (is_array(Config::get('system.plugins')) && count(Config::get('system.plugins')) > 0) {
            foreach (Config::get('system.plugins') as $plugin) {
                Config::set('plugins.'.$plugin, Spyc::YAMLLoad(file_get_contents(PLUGINS_PATH .'/'. $plugin . '/' . $plugin.'.yml')));
                if (Config::get('plugins.'.$plugin.'.enabled')) {
                    include_once PLUGINS_PATH .'/'. $plugin.'/'.$plugin.'.php';
                }
            }
        }
        Actions::run('plugins_loaded');
    }

    /**
     * Initialize Morfy Plugins
     *
     *  <code>
     *      Plugins::init();
     *  </code>
     *
     * @access  public
     */
    public static function init()
    {
        if (! isset(self::$instance)) {
            self::$instance = new Plugins();
        }
        return self::$instance;
    }
}
