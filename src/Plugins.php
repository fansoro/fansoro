<?php
namespace Fansoro;

use Symfony\Component\Yaml\Yaml;

/**
 * This file is part of the Fansoro.
 *
 * (c) Romanenko Sergey / Awilum <awilum@msn.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Plugins
{
    /**
     * @var Fansoro
     */
    protected $fansoro;

    /**
     * __construct
     */
    public function __construct(Fansoro $c)
    {
        $this->fansoro = $c;

        $plugin_manifest = [];
        $plugin_settings = [];

        // Get Plugins List
        $plugins_list = $this->fansoro['config']->get('system.plugins');

        // @TODO THIS with cache then
        // If Plugins List isnt empty
        if (is_array($plugins_list) && count($plugins_list) > 0) {

            // Go through...
            foreach ($plugins_list as $plugin) {
                if (file_exists($_plugin_manifest = PLUGINS_PATH . '/' . $plugin . '/' . $plugin . '.yml')) {
                    $plugin_manifest = Yaml::parse(file_get_contents($_plugin_manifest));
                }

                if (file_exists($_plugin_settings = PLUGINS_PATH . '/' . $plugin . '/settings.yml')) {
                    $plugin_settings = Yaml::parse(file_get_contents($_plugin_settings));
                }

                $_plugins_config[basename($_plugin_manifest)] = array_merge($plugin_manifest, $plugin_settings);
            }
        }


        if (is_array($this->fansoro['config']->get('system.plugins')) && count($this->fansoro['config']->get('system.plugins')) > 0) {
            foreach ($this->fansoro['config']->get('system.plugins') as $plugin_id => $plugin_name) {
                //echo '@@@'.$plugins;
                //if ($this->fansoro['config']->get('plugins.'.$plugin_name.'.enabled')) {
                    include_once PLUGINS_PATH .'/'. $plugin_name .'/'. $plugin_name . '.php';
                //}
            }
        }
    }

    public function init()
    {
        // init
    }
}
