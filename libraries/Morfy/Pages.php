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

        // Get the file
        if (file_exists($file)) {
            $content = file_get_contents($file);
        } else {
            $content = file_get_contents(PAGES_PATH . '/' . '404.md');
            Response::status(404);
        }

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

        return $page;
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
        try {
            Template::fenom()->display(((!empty($page['template'])) ? $page['template'] : 'index') . '.tpl', $page);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
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
