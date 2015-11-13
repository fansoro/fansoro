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
     * An instance of the Pages class
     *
     * @var object
     * @access  protected
     */
    protected static $instance = null;

    /**
     * Current page.
     *
     * @var array
     * @access  protected
     */
    protected static $current_page;

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
        // Run actions before page rendered
        Action::run('before_page_rendered');

        // Get page for current requested url
        static::loadPageTemplate(static::$current_page = static::getPage(Url::getUriString()));

        // Run actions after page rendered
        Action::run('after_page_rendered');
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

        // Create Unique Cache ID for requested list pages
        $pages_cache_id = md5('pages' . ROOT_DIR . $url . filemtime(PAGES_PATH . '/' . $url));

        if (Cache::driver()->contains($pages_cache_id)) {
            return Cache::driver()->fetch($pages_cache_id);
        } else {
            $pages = File::scan(PAGES_PATH . '/' . $url, 'md');

            foreach ($pages as $key => $page) {
                if (!in_array(basename($page, '.md'), $ignore)) {
                    $content = file_get_contents($page);

                    $_page = explode('---', $content, 3);

                    $_pages[$key] = Yaml::parse($_page[1]);

                    $url = str_replace(PAGES_PATH, Url::getBase(), $page);
                    $url = str_replace('index.md', '', $url);
                    $url = str_replace('.md', '', $url);
                    $url = str_replace('\\', '/', $url);
                    $url = rtrim($url, '/');
                    $_pages[$key]['url'] = $url;

                    $_content = $_page[2];

                        // Parse page for summary <!--more-->
                        if (($pos = strpos($_content, "<!--more-->")) === false) {
                            $_content = Filter::apply('content', $_content);
                        } else {
                            $_content = explode("<!--more-->", $_content);
                            $_content['summary']  = Filter::apply('content', $_content[0]);
                            $_content['content']  = Filter::apply('content', $_content[0].$_content[1]);
                        }

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

            Cache::driver()->save($pages_cache_id, $_pages);
            return $_pages;
        }
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

        // If url is empty that its a homepage
        if ($url) {
            $file = PAGES_PATH . '/' . $url;
        } else {
            $file = PAGES_PATH . '/' .'index';
        }

        // Select the file
        if (is_dir($file)) {
            $file = PAGES_PATH . '/' . $url .'/index.md';
        } else {
            $file .= '.md';
        }

        // Get 404 page if file not exists
        if (!file_exists($file)) {
            $file = PAGES_PATH . '/' . '404.md';
            Response::status(404);
        }

        // Create Unique Cache ID for requested page
        $page_cache_id = md5('page' . ROOT_DIR . $file . filemtime($file));

        if (Cache::driver()->contains($page_cache_id) && Config::get('pages_flush_cache') == false) {
            return Cache::driver()->fetch($page_cache_id);
        } else {
            $content = file_get_contents($file);

            $_page = explode('---', $content, 3);

            $page = Yaml::parse($_page[1]);

            $url = str_replace(PAGES_PATH, Url::getBase(), $file);
            $url = str_replace('index.md', '', $url);
            $url = str_replace('.md', '', $url);
            $url = str_replace('\\', '/', $url);
            $url = rtrim($url, '/');
            $page['url'] = $url;

            $_content = $_page[2];

            // Parse page for summary <!--more-->
            if (($pos = strpos($_content, "<!--more-->")) === false) {
                $_content = Filter::apply('content', $_content);
            } else {
                $_content = explode("<!--more-->", $_content);
                $_content['summary']  = Filter::apply('content', $_content[0]);
                $_content['content']  = Filter::apply('content', $_content[0].$_content[1]);
            }

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

            Cache::driver()->save($page_cache_id, $page);
            return $page;
        }
    }

    /**
     * Get Current Page
     *
     *  <code>
     *      $page = Pages::getCurrentPage();
     *  </code>
     *
     * @return array
     */
    public static function getCurrentPage()
    {
        return static::$current_page;
    }

    /**
     * Load Page Template
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
        $template = Template::factory(THEMES_PATH . '/' . Config::get('system.theme'));
        $template->display(((!empty($page['template'])) ? $page['template'] : 'index') . '.tpl', $page);
    }

    /**
     * Initialize Morfy Pages
     *
     *  <code>
     *      Pages::init();
     *  </code>
     *
     * @access  public
     */
    public static function init()
    {
        return !isset(self::$instance) and self::$instance = new Pages();
    }
}
