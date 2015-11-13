<?php

/**
 * This file is part of the Morfy.
 *
 * (c) Romanenko Sergey / Awilum <awilum@msn.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Template extends \Fenom
{
    use \Fenom\StorageTrait;

    public static function factory($source, $compile_dir = '/tmp', $options = 0)
    {
        // Create fenom cache directory if its not exists
        !Dir::exists(CACHE_PATH . '/fenom/') and Dir::create(CACHE_PATH . '/fenom/');

        // Create Unique Cache ID for Theme
        $theme_config_file = THEMES_PATH . '/' . Config::get('system.theme') . '/' . Config::get('system.theme') . '.yml';
        $theme_cache_id = md5('theme' . ROOT_DIR . $theme_config_file . filemtime($theme_config_file));

        // Set current them options
        if (Cache::driver()->contains($theme_cache_id)) {
            Config::set('theme', Cache::driver()->fetch($theme_cache_id));
        } else {
            $theme_config = Yaml::parseFile($theme_config_file);
            Config::set('theme', $theme_config);
            Cache::driver()->save($theme_cache_id, $theme_config);
        }

        $compile_dir = CACHE_PATH . '/fenom/';
        $options = Config::get('system.fenom');

        $fenom = parent::factory($source, $compile_dir, $options);

        // Add {$.config} for templates
        $fenom->addAccessorSmart('config', 'config', Fenom::ACCESSOR_PROPERTY);
        $fenom->config = Config::getConfig();

        return $fenom;
    }
}
