<?php

 /**
  * Morfy
  *
  * @package Morfy
  * @author Romanenko Sergey / Awilum <awilum@msn.com>
  * @link http://morfy.org
  *
  * For the full copyright and license information, please view the LICENSE
  * file that was distributed with this source code.
  */

class Morfy
{
    /**
     * An instance of the Morfy class
     *
     * @var object
     */
    protected static $instance = null;

    /**
     * The version of Morfy
     *
     * @var string
     */
    const VERSION = '1.1.1';

    /**
     * Site Config array (/config/site.yml).
     *
     * @var array
     */
    public static $site;

    /**
     * Fenom Config array (/config/fenom.yml).
     *
     * @var array
     */
    public static $fenom;

    /**
     * Current Site Theme config (/themes/%theme%/%theme%.yml).
     *
     * @var array
     */
    public static $theme;

    /**
     * Current page.
     *
     * @var array
     */
    public static $page;

    /**
     * Plugins
     *
     * @var array
     */
    public static $plugins = array();

    /**
     * Actions
     *
     * @var array
     */
    public static $actions = array();

    /**
     * Filters
     *
     * @var array
     */
    public static $filters = array();

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
        // Use the Force...
        include LIBRARIES_PATH . '/Force/ClassLoader/ClassLoader.php';

        // Map Classes
        ClassLoader::mapClasses(array(
            // Yaml Parser/Dumper
            'Spyc'     => LIBRARIES_PATH . '/Spyc/Spyc.php',

            // Force Components
            'Arr'        => LIBRARIES_PATH . '/Force/Arr/Arr.php',
            'Session'    => LIBRARIES_PATH . '/Force/Session/Session.php',
            'Token'      => LIBRARIES_PATH . '/Force/Token/Token.php',
            'Request'    => LIBRARIES_PATH . '/Force/Http/Request.php',
            'Response'   => LIBRARIES_PATH . '/Force/Http/Response.php',
            'Url'        => LIBRARIES_PATH . '/Force/Url/Url.php',
            'File'       => LIBRARIES_PATH . '/Force/FileSystem/File.php',
            'Dir'        => LIBRARIES_PATH . '/Force/FileSystem/Dir.php',
            'Shortcode'  => LIBRARIES_PATH . '/Force/Shortcode/Shortcode.php',

            // Parsedown
            'Parsedown'      => LIBRARIES_PATH . '/Parsedown/Parsedown.php',
            'ParsedownExtra' => LIBRARIES_PATH . '/Parsedown/ParsedownExtra.php'
        ));

        // Map Fenom Template Engine folder
        ClassLoader::directory(LIBRARIES_PATH . '/Fenom/');

        // Register the ClassLoader to the SPL autoload stack.
        ClassLoader::register();

        // Load config file
        static::loadConfig();

        // Set default timezone
        @ini_set('date.timezone', static::$site['timezone']);
        if (function_exists('date_default_timezone_set')) {
            date_default_timezone_set(static::$site['timezone']);
        } else {
            putenv('TZ='.static::$site['timezone']);
        }

        // Sanitize URL to prevent XSS - Cross-site scripting
        Url::runSanitizeURL();

        // Send default header and set internal encoding
        header('Content-Type: text/html; charset='.static::$site['charset']);
        function_exists('mb_language') and mb_language('uni');
        function_exists('mb_regex_encoding') and mb_regex_encoding(static::$site['charset']);
        function_exists('mb_internal_encoding') and mb_internal_encoding(static::$site['charset']);

        // Gets the current configuration setting of magic_quotes_gpc and kill magic quotes
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

        // Load Plugins
        static::loadPlugins();
        static::runAction('plugins_loaded');

        // Get page for current requested url
        Morfy::$page = static::getPage(Url::getUriString());

        // Load template
        static::runAction('before_render');
        static::loadPageTemplate(Morfy::$page);
        static::runAction('after_render');
    }

    /**
     * Load Page template
     *
     *  <code>
     *      Morfy::loadPageTemplate($page);
     *  </code>
     *
     * @access public
     * @param  array $page Page array
     * @return string
     */
    public static function loadPageTemplate($page)
    {
        $fenom = Fenom::factory(
            THEMES_PATH . '/' . static::$site['theme'] . '/',
            CACHE_PATH . '/fenom/',
            static::$fenom
        );

        if (file_exists($theme_config_path = THEMES_PATH . '/' . static::$site['theme'] . '/'. static::$site['theme'] .'.yml')) {
            static::$theme = Spyc::YAMLLoad(file_get_contents($theme_config_path));

            // Do global tag {$.theme} for the template
            $fenom->addAccessorSmart('theme', 'theme_config', Fenom::ACCESSOR_PROPERTY);
            $fenom->theme_config = static::$theme;
        }

        // Do global tag {$.site} for the template
        $fenom->addAccessorSmart('site', 'site_config', Fenom::ACCESSOR_PROPERTY);
        $fenom->site_config = static::$site;

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
     *      $pages = Morfy::getPages('blog');
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

                $url = str_replace(PAGES_PATH, static::$site['url'], $page);
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
     *      $page = Morfy::getPage('downloads');
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

        $url = str_replace(PAGES_PATH, static::$site['url'], $file);
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
        empty($page['title']) and $page['title'] = static::$site['title'];
        empty($page['keywords']) and $page['keywords'] = static::$site['keywords'];
        empty($page['description']) and $page['description'] = static::$site['description'];

        return $page;
    }

    /**
     * Parsedown
     *
     *  <code>
     *      $content = Morfy::parsedown($content);
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
     * Get Page Block
     *
     *  <code>
     *      $content = Morfy::getBlock($content);
     *  </code>
     *
     * @access  public
     * @param  string $name Block name
     * @return string Formatted Block content
     */
    public static function getBlock($name)
    {
        return static::parseContent(file_get_contents(BLOCKS_PATH . '/' . $name . '.md'));
    }

    /**
     * Content Parser
     *
     * @param  string $content Content to parse
     * @return string $content Formatted content
     */
    protected static function parseContent($content)
    {
        // Add {site_url} shortcode
        Shortcode::add('site_url', function () {
            return static::$site['url'];
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
            $content = static::applyFilter('content', $content);
        } else {
            $content = explode("<!--more-->", $content);
            $content['summary']  = static::applyFilter('content', $content[0]);
            $content['content']  = static::applyFilter('content', $content[0].$content[1]);
        }

        // Return content
        return $content;
    }

    /**
     * Load Config
     */
    protected static function loadConfig()
    {
        if (file_exists($site_config_path  = CONFIG_PATH . '/site.yml') &&
            file_exists($fenom_config_path = CONFIG_PATH . '/fenom.yml')) {
            static::$site  = Spyc::YAMLLoad(file_get_contents($site_config_path));
            static::$fenom = Spyc::YAMLLoad(file_get_contents($fenom_config_path));
        } else {
            die("Oops.. Where is config files ?!");
        }
    }

    /**
     * Load Plugins
     */
    protected static function loadPlugins()
    {
        if (is_array(static::$site['plugins']) && count(static::$site['plugins']) > 0) {
            foreach (static::$site['plugins'] as $plugin) {
                static::$plugins[$plugin] = Spyc::YAMLLoad(file_get_contents(PLUGINS_PATH .'/'. $plugin.'/'.$plugin.'.yml'));
                if (static::$plugins[$plugin]['enabled']) {
                    include_once PLUGINS_PATH .'/'. $plugin.'/'.$plugin.'.php';
                }
            }
        }
    }

    /**
     *  Hooks a function on to a specific action.
     *
     *  <code>
     *      // Hooks a function "newLink" on to a "footer" action.
     *      Morfy::addAction('footer', 'newLink', 10);
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
    public static function addAction($action_name, $added_function, $priority = 10, array $args = null)
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
     *      Morfy::runAction('footer');
     *  </code>
     *
     * @access  public
     * @param  string  $action_name Action name
     * @param  array   $args        Arguments
     * @param  boolean $return      Return data or not. Default is false
     * @return mixed
     */
    public static function runAction($action_name, $args = array(), $return = false)
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
     *      Morfy::applyFilter('content', $content);
     *  </code>
     *
     * @access  public
     * @param  string $filter_name The name of the filter hook.
     * @param  mixed  $value       The value on which the filters hooked.
     * @return mixed
     */
    public static function applyFilter($filter_name, $value)
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
     *      Morfy::addFilter('content', 'replacer');
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
    public static function addFilter($filter_name, $function_to_add, $priority = 10, $accepted_args = 1)
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

    /**
     * Initialize Morfy Application
     *
     *  <code>
     *      Morfy::init();
     *  </code>
     *
     * @access  public
     */
    public static function init()
    {
        if (! isset(self::$instance)) {
            self::$instance = new Morfy();
        }
        return self::$instance;
    }
}
