<?php

/**
 * This file is part of the Morfy.
 *
 * (c) Romanenko Sergey / Awilum <awilum@msn.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Pages
{
    /**
     * Current page.
     *
     * @var array
     */
    public static $currentPage;

    /**
     * Initialize Pages
     *
     *  <code>
     *      Pages::init();
     *  </code>
     *
     * @access  public
     */
    public static function init()
    {
        // Get page for current requested url
        static::$currentPage = static::getPage(Url::getUriString());

        // Load template
        Actions::run('before_page_rendered');
        static::loadPageTemplate(static::$currentPage);
        Actions::run('after_page_rendered');
    }

    /**
     * Get pages
     *
     *  <code>
     *      $pages = Pages::getPages('blog');
     *  </code>
     *
     * @access  public
     * @param  string  $url        Url
     * @param  string  $order_by   Order by
     * @param  string  $order_type Order type
     * @param  array   $ignore     Pages to ignore
     * @param  int     $limit      Limit of pages
     * @return array
     */
    public static function getPages($url = '', $order_by = 'date', $order_type = 'DESC', $ignore = array('404'), $limit = null)
    {
        $pages = File::scan(PAGES_PATH . '/' . $url, 'md');

        foreach ($pages as $key => $page) {
            if (!in_array(basename($page, '.md'), $ignore)) {
                $content = file_get_contents($page);

                $_page = explode('---', $content, 3);

                $_pages[$key] = Spyc::YAMLLoad($_page[1]);

                $url = str_replace(PAGES_PATH, Config::get('site.url'), $page);
                $url = str_replace('index.md', '', $url);
                $url = str_replace('.md', '', $url);
                $url = str_replace('\\', '/', $url);
                $url = rtrim($url, '/');
                $_pages[$key]['url'] = $url;

                $_content = static::parseContent($_page[2]);

                if (is_array($_content)) {
                    $_pages[$key]['summary'] = $_content['summary'];
                    $_pages[$key]['content'] = $_content['content'];
                } else {
                    $_pages[$key]['summary'] = $_content;
                    $_pages[$key]['content'] = $_content;
                }

                $_pages[$key]['slug'] = basename($page, '.md');
            }
        }

        $_pages = Arr::subvalSort($_pages, $order_by, $order_type);

        if ($limit != null) {
            $_pages = array_slice($_pages, null, $limit);
        }

        return $_pages;
    }

    /**
     * Get page
     *
     *  <code>
     *      $page = Pages::getPage('downloads');
     *  </code>
     *
     * @access  public
     * @param  string $url Url
     * @return array
     */
    public static function getPage($url)
    {

        // Get the file path
        if ($url) {
            $file = PAGES_PATH . '/' . $url;
        } else {
            $file = PAGES_PATH . '/' .'index';
        }

        // Load the file
        if (is_dir($file)) {
            $file = PAGES_PATH . '/' . $url .'/index.md';
        } else {
            $file .= '.md';
        }

        if (file_exists($file)) {
            $content = file_get_contents($file);
        } else {
            $content = file_get_contents(PAGES_PATH . '/' . '404.md');
            Response::status(404);
        }

        $_page = explode('---', $content, 3);

        $page = Spyc::YAMLLoad($_page[1]);

        $url = str_replace(PAGES_PATH, Config::get('site.url'), $file);
        $url = str_replace('index.md', '', $url);
        $url = str_replace('.md', '', $url);
        $url = str_replace('\\', '/', $url);
        $url = rtrim($url, '/');
        $page['url'] = $url;

        $_content = static::parseContent($_page[2]);

        if (is_array($_content)) {
            $page['summary'] = $_content['summary'];
            $page['content'] = $_content['content'];
        } else {
            $page['content'] = $_content;
        }

        $page['slug'] = basename($file, '.md');

        // Overload page title, keywords and description if needed
        empty($page['title']) and $page['title'] = Config::get('site.title');
        empty($page['keywords']) and $page['keywords'] = Config::get('site.keywords');
        empty($page['description']) and $page['description'] = Config::get('site.description');

        return $page;
    }

    /**
     * Parsedown
     *
     *  <code>
     *      $content = Pages::parsedown($content);
     *  </code>
     *
     * @access  public
     * @param  string $content Content to parse
     * @return string Formatted content
     */
    public static function parsedown($content)
    {
        $parsedown_extra = new ParsedownExtra();
        return $parsedown_extra->text($content);
    }

    /**
     * Content Parser
     *
     * @param  string $content Content to parse
     * @return string $content Formatted content
     */
    public static function parseContent($content)
    {
        // Add {site_url} shortcode
        Shortcode::add('site_url', function () {
            return Config::get('site.url');
        });

        // Add {block name=block-name} shortcode
        Shortcode::add('block', function ($attributes) {
            if (isset($attributes['name'])) {
                if (File::exists($block_file = BLOCKS_PATH . '/' . $attributes['name'] . '.md')) {
                    return file_get_contents($block_file);
                } else {
                    return 'Block ' . $attributes['name'] . ' is not found!';
                }
            }
        });

        // Parse Shortcodes
        $content = Shortcode::parse($content);

        // Parsedown
        $content = static::parsedown($content);

        // Parse page for summary <!--more-->
        if (($pos = strpos($content, "<!--more-->")) === false) {
            $content = Filters::apply('content', $content);
        } else {
            $content = explode("<!--more-->", $content);
            $content['summary']  = Filters::apply('content', $content[0]);
            $content['content']  = Filters::apply('content', $content[0].$content[1]);
        }

        // Return content
        return $content;
    }

    /**
     * Load Page template
     *
     *  <code>
     *      Pages::loadPageTemplate($page);
     *  </code>
     *
     * @access public
     * @param  array $page Page array
     * @return string
     */
    public static function loadPageTemplate($page)
    {
        // Create fenom cache directory if its not exists
        if (!Dir::exists(CACHE_PATH . '/fenom/')) {
            Dir::create(CACHE_PATH . '/fenom/');
        }

        $fenom = Fenom::factory(
            THEMES_PATH . '/' . Config::get('system.theme') . '/',
            CACHE_PATH . '/fenom/',
            Config::get('system.fenom')
        );


        if (file_exists($theme_config_path = THEMES_PATH . '/' . Config::get('system.theme') . '/'. Config::get('system.theme') .'.yml')) {
            $conf = Spyc::YAMLLoad(file_get_contents($theme_config_path));
            Config::set('theme', $conf);

            // Do global tag {$.theme} for the template
            $fenom->addAccessorSmart('theme', 'theme_config', Fenom::ACCESSOR_PROPERTY);
            $fenom->theme_config = Config::get('theme');
        }

        // Do global tag {$.site} for the template
        $fenom->addAccessorSmart('site', 'site_config', Fenom::ACCESSOR_PROPERTY);
        $fenom->site_config = Config::get('site');

        // Display page
        try {
            $fenom->display(((!empty($page['template'])) ? $page['template'] : 'index') . '.tpl', $page);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}
