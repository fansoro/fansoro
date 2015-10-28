<?php

/**
 * This file is part of the Morfy.
 *
 * (c) Romanenko Sergey / Awilum <awilum@msn.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Config
{
    /**
     * Config
     *
     * @var array
     */
    public static $config = array();

    /**
     * Set config file
     *
     *  <code>
     *      Config::setFile('path_to_config_file');
     *  </code>
     *
     * @access  public
     * @param  string $path Path to config file
     */
    public static function setFile($path)
    {
        if (File::exists($path)) {
            static::$config[File::name($path)] = Spyc::YAMLLoad(file_get_contents($path));
        }
    }

    /**
     * Set new config variable
     *
     *  <code>
     *      Config::set('key', 'value');
     *  </code>
     *
     * @access  public
     * @param  string $key   Key
     * @param  mixed  $value Value
     */
    public static function set($key, $value)
    {
        return Arr::set(static::$config, $key, $value);
    }

    /**
     * Get config variable
     *
     *  <code>
     *      Config::set('key');
     *  </code>
     *
     * @access  public
     * @param  string $key   Key
     */
    public static function get($key)
    {
        return Arr::get(static::$config, $key);
    }
}
