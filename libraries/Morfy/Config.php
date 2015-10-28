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

    public static function setFile($path)
    {
        if (File::exists($path)) {
            static::$config[File::name($path)] = Spyc::YAMLLoad(file_get_contents($path));
        }
    }

    public static function set($key, $value)
    {
        return Arr::set(static::$config, $key, $value);
    }

    public static function get($key)
    {
        return Arr::get(static::$config, $key);
    }
}
