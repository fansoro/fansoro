<?php

/**
 * Morfy Engine
 *
 *  Morfy - Content Management System.
 *  Site: www.morfy.org
 *  Copyright (C) 2014 - 2015 Romanenko Sergey / Awilum <awilum@msn.com>
 *
 * This source file is part of the Morfy Engine. More information,
 * documentation and tutorials can be found at http://morfy.org
 *
 * @package     Morfy
 *
 * @author      Romanenko Sergey / Awilum <awilum@msn.com>
 * @copyright   2014 - 2015 Romanenko Sergey / Awilum <awilum@msn.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

 class Morfy
 {
     /**
     * The version of Morfy
     *
     * @var string
     */
    const VERSION = '1.0.6';

    /**
     * The separator of Morfy
     *
     * @var string
     */
    const SEPARATOR = '----';

    /**
     * Site Config array.
     *
     * @var array
     */
    public static $site;

    /**
     * Fenom Config array.
     *
     * @var array
     */
    public static $fenom;

    /**
     * Plugins
     *
     * @var array
     */
    private static $plugins = array();

    /**
     * Actions
     *
     * @var array
     */
    private static $actions = array();

    /**
     * Filters
     *
     * @var array
     */
    private static $filters = array();

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
     * @access  public
     */
    protected function __construct()
    {
        // Nothing here
    }

    /**
     * Factory method making method chaining possible right off the bat.
     *
     *  <code>
     *      $morfy = Morfy::factory();
     *  </code>
     *
     * @access  public
     */
    public static function factory()
    {
        return new static();
    }

    /**
     * Run Morfy Application
     *
     *  <code>
     *      Morfy::factory()->run();
     *  </code>
     *
     * @access  public
     */
    public function run()
    {
        // Load Yaml Parser/Dumper
        include LIBRARIES_PATH . '/Spyc/Spyc.php';

        // Load config file
        $this->loadConfig();

        // Use the Force...
        include LIBRARIES_PATH . '/Force/ClassLoader/ClassLoader.php';
        ClassLoader::mapClasses(array(
            'Arr'      => LIBRARIES_PATH . '/Force/Arr/Arr.php',
            'Session'  => LIBRARIES_PATH . '/Force/Session/Session.php',
            'Token'    => LIBRARIES_PATH . '/Force/Token/Token.php',
            'Request'  => LIBRARIES_PATH . '/Force/Http/Request.php',
            'Response' => LIBRARIES_PATH . '/Force/Http/Response.php',
            'Url'      => LIBRARIES_PATH . '/Force/Url/Url.php',
            'File'     => LIBRARIES_PATH . '/Force/FileSystem/File.php',
            'Dir'      => LIBRARIES_PATH . '/Force/FileSystem/Dir.php',
        ));
        ClassLoader::register();

        // Set default timezone
        @ini_set('date.timezone', static::$site['site_timezone']);
        if (function_exists('date_default_timezone_set')) {
            date_default_timezone_set(static::$site['site_timezone']);
        } else {
            putenv('TZ='.static::$site['site_timezone']);
        }

        /**
         * Sanitize URL to prevent XSS - Cross-site scripting
         */
        Url::runSanitizeURL();

        /**
         * Send default header and set internal encoding
         */
        header('Content-Type: text/html; charset='.static::$site['charset']);
        function_exists('mb_language') and mb_language('uni');
        function_exists('mb_regex_encoding') and mb_regex_encoding(static::$site['charset']);
        function_exists('mb_internal_encoding') and mb_internal_encoding(static::$site['charset']);

        /**
         * Gets the current configuration setting of magic_quotes_gpc
         * and kill magic quotes
         */
        if (get_magic_quotes_gpc()) {
            function stripslashesGPC(&$value)
            {
                $value = stripslashes($value);
            }
            array_walk_recursive($_GET, 'stripslashesGPC');
            array_walk_recursive($_POST, 'stripslashesGPC');
            array_walk_recursive($_COOKIE, 'stripslashesGPC');
            array_walk_recursive($_REQUEST, 'stripslashesGPC');
        }

        // Start the session
        Session::start();

        // Include parsedown
        include LIBRARIES_PATH . '/Parsedown/Parsedown.php';
        include LIBRARIES_PATH . '/Parsedown/ParsedownExtra.php';

        // Load Plugins
        $this->loadPlugins();
        $this->runAction('plugins_loaded');

        // Load Fenom Template Engine
        include LIBRARIES_PATH . '/Fenom/Fenom.php';
        Fenom::registerAutoload();

        // Get page for current requested url
        $page = $this->getPage(Url::getUriString());

        // Overload page title, keywords and description
        empty($page['title']) and $page['title'] = static::$site['title'];
        empty($page['keywords']) and $page['keywords'] = static::$site['keywords'];
        empty($page['description']) and $page['description'] = static::$site['description'];

        $page   = $page;
        $site   = self::$site;

        // Load template
        $this->runAction('before_render');
        $this->loadTemplate($page, $site);
        $this->runAction('after_render');
    }

    /**
     * Load template
     *
     *  <code>
     *      Morfy::factory()->loadTemplate($page, $site);
     *  </code>
     *
     * @access public
     * @return string
     */
    public function loadTemplate($page, $site)
    {
        $fenom = Fenom::factory(
            THEMES_PATH . '/' . $site['theme'] . '/',
            ROOT_DIR . '/cache/fenom/',
            self::$fenom
        );

        // Do global tag {$.site} for the template
        $fenom->addAccessorSmart('site', 'site_config', Fenom::ACCESSOR_PROPERTY);
        $fenom->site_config = $site;

        // Display page
        try {
            $fenom->display(((!empty($page['template'])) ? $page['template'] : 'index') . '.tpl', $page);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

   /**
     * Get pages
     *
     *  <code>
     *      $pages = Morfy::factory()->getPages(CONTENT_PATH . '/blog/');
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
    public function getPages($url = '', $order_by = 'date', $order_type = 'DESC', $ignore = array('404'), $limit = null)
    {
        $pages = File::scan(CONTENT_PATH . $url, 'md');

        foreach ($pages as $key => $page) {
            if (!in_array(basename($page, '.md'), $ignore)) {
                $content = file_get_contents($page);

                $_page_headers = explode(Morfy::SEPARATOR, $content);

                $_pages[$key] = Spyc::YAMLLoad($_page_headers[0]);

                $url = str_replace(CONTENT_PATH, Morfy::$site['url'], $page);
                $url = str_replace('index.md', '', $url);
                $url = str_replace('.md', '', $url);
                $url = str_replace('\\', '/', $url);
                $url = rtrim($url, '/');
                $_pages[$key]['url'] = $url;

                $_content = $this->parseContent($content);
                if (is_array($_content)) {
                    $_pages[$key]['content_short'] = $_content['content_short'];
                    $_pages[$key]['content'] = $_content['content_full'];
                } else {
                    $_pages[$key]['content_short'] = $_content;
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
     *      $page = Morfy::factory()->getPage('downloads');
     *  </code>
     *
     * @access  public
     * @param  string $url Url
     * @return array
     */
    public function getPage($url)
    {

        // Get the file path
        if ($url) {
            $file = CONTENT_PATH . '/' . $url;
        } else {
            $file = CONTENT_PATH . '/' .'index';
        }

        // Load the file
        if (is_dir($file)) {
            $file = CONTENT_PATH . '/' . $url .'/index.md';
        } else {
            $file .= '.md';
        }

        if (file_exists($file)) {
            $content = file_get_contents($file);
        } else {
            $content = file_get_contents(CONTENT_PATH . '/' . '404.md');
            header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
        }

        $_page_headers = explode(Morfy::SEPARATOR, $content);

        $page = Spyc::YAMLLoad($_page_headers[0]);

        $url = str_replace(CONTENT_PATH, Morfy::$site['url'], $file);
        $url = str_replace('index.md', '', $url);
        $url = str_replace('.md', '', $url);
        $url = str_replace('\\', '/', $url);
        $url = rtrim($url, '/');
        $page['url'] = $url;

        $_content = $this->parseContent($content);
        if (is_array($_content)) {
            $page['content_short'] = $_content['content_short'];
            $page['content'] = $_content['content_full'];
        } else {
            $page['content_short'] = $_content;
            $page['content'] = $_content;
        }

        $page['slug'] = basename($file, '.md');

        return $page;
    }

    /**
     * Content Parser
     *
     * @param  string $content Content to parse
     * @return string $content Formatted content
     */
    protected function parseContent($content)
    {
        // Parse Content after Headers
        $_content = '';
        $i = 0;
        foreach (explode(Morfy::SEPARATOR, $content) as $c) {
            ($i++!=0) and $_content .= $c;
        }

        $content = $_content;

        // Parse {site_url}
        $content = str_replace('{site_url}', static::$site['url'], $_content);

        // Parse {morfy_separator}
        $content = str_replace('{morfy_separator}', Morfy::SEPARATOR, $content);

        // Parse {morfy_version}
        $content = str_replace('{morfy_version}', Morfy::VERSION, $content);

        $ParsedownExtra = new ParsedownExtra();
        $content = $ParsedownExtra->text($content);

        // Parse {cut}
        $pos = strpos($content, "{cut}");
        if ($pos === false) {
            $content = $this->applyFilter('content', $content);
        } else {
            $content = explode("{cut}", $content);
            $content['content_short'] = $this->applyFilter('content', $content[0]);
            $content['content_full']  = $this->applyFilter('content', $content[0].$content[1]);
        }

        // Return content
        return $content;
    }

    /**
     * Load Plugins
     */
    protected function loadPlugins()
    {
        if (count(static::$site['plugins']) > 0) {
            foreach (static::$site['plugins'] as $plugin) {
                include_once PLUGINS_PATH .'/'. $plugin.'/'.$plugin.'.php';
            }
        }
    }

    /**
     * Load Config
     */
    protected function loadConfig()
    {
        $site_config_path  = ROOT_DIR . '/config/site.yml';
        $fenom_config_path = ROOT_DIR . '/config/fenom.yml';

        if (file_exists($site_config_path) && file_exists($fenom_config_path)) {
            static::$site = Spyc::YAMLLoad(file_get_contents($site_config_path));
            static::$fenom = Spyc::YAMLLoad(file_get_contents($fenom_config_path));
        } else {
            die("Oops.. Where is config file ?!");
        }
    }

    /**
     *  Hooks a function on to a specific action.
     *
     *  <code>
     *      // Hooks a function "newLink" on to a "footer" action.
     *      Morfy::factory()->addAction('footer', 'newLink', 10);
     *
     *      function newLink() {
     *          echo '<a href="#">My link</a>';
     *      }
     *  </code>
     *
     * @access  public
     * @param string  $action_name    Action name
     * @param mixed   $added_function Added function
     * @param integer $priority       Priority. Default is 10
     * @param array   $args           Arguments
     */
    public function addAction($action_name, $added_function, $priority = 10, array $args = null)
    {
        // Hooks a function on to a specific action.
        static::$actions[] = array(
                        'action_name' => (string) $action_name,
                        'function'    => $added_function,
                        'priority'    => (int) $priority,
                        'args'        => $args
        );
    }

    /**
     * Run functions hooked on a specific action hook.
     *
     *  <code>
     *      // Run functions hooked on a "footer" action hook.
     *      Morfy::factory()->runAction('footer');
     *  </code>
     *
     * @access  public
     * @param  string  $action_name Action name
     * @param  array   $args        Arguments
     * @param  boolean $return      Return data or not. Default is false
     * @return mixed
     */
    public function runAction($action_name, $args = array(), $return = false)
    {
        // Redefine arguments
        $action_name = (string) $action_name;
        $return      = (bool) $return;

        // Run action
        if (count(static::$actions) > 0) {

            // Sort actions by priority
            $actions = Arr::subvalSort(static::$actions, 'priority');

            // Loop through $actions array
            foreach ($actions as $action) {

                // Execute specific action
                if ($action['action_name'] == $action_name) {

                    // isset arguments ?
                    if (isset($args)) {

                        // Return or Render specific action results ?
                        if ($return) {
                            return call_user_func_array($action['function'], $args);
                        } else {
                            call_user_func_array($action['function'], $args);
                        }
                    } else {
                        if ($return) {
                            return call_user_func_array($action['function'], $action['args']);
                        } else {
                            call_user_func_array($action['function'], $action['args']);
                        }
                    }
                }
            }
        }
    }

    /**
     * Apply filters
     *
     *  <code>
     *      Morfy::factory()->applyFilter('content', $content);
     *  </code>
     *
     * @access  public
     * @param  string $filter_name The name of the filter hook.
     * @param  mixed  $value       The value on which the filters hooked.
     * @return mixed
     */
    public function applyFilter($filter_name, $value)
    {
        // Redefine arguments
        $filter_name = (string) $filter_name;

        $args = array_slice(func_get_args(), 2);

        if (! isset(static::$filters[$filter_name])) {
            return $value;
        }

        foreach (static::$filters[$filter_name] as $priority => $functions) {
            if (! is_null($functions)) {
                foreach ($functions as $function) {
                    $all_args = array_merge(array($value), $args);
                    $function_name = $function['function'];
                    $accepted_args = $function['accepted_args'];
                    if ($accepted_args == 1) {
                        $the_args = array($value);
                    } elseif ($accepted_args > 1) {
                        $the_args = array_slice($all_args, 0, $accepted_args);
                    } elseif ($accepted_args == 0) {
                        $the_args = null;
                    } else {
                        $the_args = $all_args;
                    }
                    $value = call_user_func_array($function_name, $the_args);
                }
            }
        }

        return $value;
    }

    /**
     * Add filter
     *
     *  <code>
     *      Morfy::factory()->addFilter('content', 'replacer');
     *
     *      function replacer($content) {
     *          return preg_replace(array('/\[b\](.*?)\[\/b\]/ms'), array('<strong>\1</strong>'), $content);
     *      }
     *  </code>
     *
     * @access  public
     * @param  string  $filter_name     The name of the filter to hook the $function_to_add to.
     * @param  string  $function_to_add The name of the function to be called when the filter is applied.
     * @param  integer $priority        Function to add priority - default is 10.
     * @param  integer $accepted_args   The number of arguments the function accept default is 1.
     * @return boolean
     */
    public function addFilter($filter_name, $function_to_add, $priority = 10, $accepted_args = 1)
    {
        // Redefine arguments
        $filter_name     = (string) $filter_name;
        $function_to_add = $function_to_add;
        $priority        = (int) $priority;
        $accepted_args   = (int) $accepted_args;

        // Check that we don't already have the same filter at the same priority. Thanks to WP :)
        if (isset(static::$filters[$filter_name]["$priority"])) {
            foreach (static::$filters[$filter_name]["$priority"] as $filter) {
                if ($filter['function'] == $function_to_add) {
                    return true;
                }
            }
        }

        static::$filters[$filter_name]["$priority"][] = array('function' => $function_to_add, 'accepted_args' => $accepted_args);

        // Sort
        ksort(static::$filters[$filter_name]["$priority"]);

        return true;
    }
 }
