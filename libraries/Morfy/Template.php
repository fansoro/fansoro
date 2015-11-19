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

    /**
     * Template factory
     *
     *  <code>
     *      $template = Template::factory('templates_path');
     *  </code>
     *
     * @param string|Fenom\ProviderInterface $source path to templates or custom provider
     * @param string $compile_dir path to compiled files
     * @param int|array $options
     * @throws InvalidArgumentException
     * @return Fenom
     */
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

        $fenom->assign('config', Config::getConfig());

        return $fenom;
    }


    /**
     * Execute template and write result into stdout
     *
     *  <code>
     *      $template->display('template.tpl');
     *  </code>
     *
     * @param string|array $template name of template.
     * If it is array of names of templates they will be extended from left to right.
     * @param array $vars array of data for template
     * @return Fenom\Render
     */
    public function display($template, array $vars = [])
    {
        try {
            return $this->_vars = $this->getTemplate($template)->display($vars ? $vars + $this->_vars : $this->_vars);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}
