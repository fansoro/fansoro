<?php
namespace Fansoro;

use Arr;
use Symfony\Component\Yaml\Yaml;

/**
 * This file is part of the Fansoro.
 *
 * (c) Romanenko Sergey / Awilum <awilum@msn.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Config
{
    /**
     * @var Fansoro
     */
    protected $fansoro;

    /**
     * Config
     *
     * @var array
     * @access  protected
     */
    protected static $config = [];

    /**
     * Constructor.
     *
     * @access  protected
     */
    public function __construct(Fansoro $c)
    {
        $this->fansoro = $c;

        $site_config   = CONFIG_PATH . '/' . 'site.yml';
        $system_config = CONFIG_PATH . '/' . 'system.yml';

        if ($this->fansoro['filesystem']->exists($site_config) && $this->fansoro['filesystem']->exists($system_config)) {
            self::$config['site']   = Yaml::parse(file_get_contents($site_config));
            self::$config['system'] = Yaml::parse(file_get_contents($system_config));
        } else {
            throw new RuntimeException("Fansoro config files does not exist.");
        }
    }

    /**
     * Set new or update existing config variable
     *
     * @access public
     * @param string $key   Key
     * @param mixed  $value Value
     */
    public function set($key, $value)
    {
        Arr::set(self::$config, $key, $value);
    }

    /**
     * Get config variable
     *
     * @access  public
     * @param  string $key Key
     * @param  mixed  $default Default value
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return Arr::get(self::$config, $key, $default);
    }

    /**
     * Get config array
     *
     * @access  public
     * @return array
     */
    public function getConfig()
    {
        return self::$config;
    }
}
