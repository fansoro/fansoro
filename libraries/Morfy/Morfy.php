<?php

/**
 * Morfy Engine
 *
 *  Morfy - Content Management System.
 *  Site: www.morfy.mostra.org
 *  Copyright (C) 2013 Romanenko Sergey / Awilum <awilum@msn.com>
 *
 * This source file is part of the Morfy Engine. More information,
 * documentation and tutorials can be found at http://morfy.monstra.org
 *
 * @package     Morfy
 *
 * @author      Romanenko Sergey / Awilum <awilum@msn.com>
 * @copyright   2013 Romanenko Sergey / Awilum <awilum@msn.com>
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
    const VERSION = '0.0.1';

    /**
     * The separator of Morfy
     *
     * @var string
     */
    const SEPARATOR = '----';    

    /**
     * Config array.
     *
     * @var array
     */
    public static $config;

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
     * Page headers
     *
     * @var array
     */
    private $page_headers = array(
                                    'title'         => 'Title',
                                    'description'   => 'Description',
                                    'keywords'      => 'Keywords',
                                    'author'        => 'Author',
                                    'date'          => 'Date',
                                    'robots'        => 'Robots',
                                    'tags'          => 'Tags',
                                    'template'      => 'Template',
                                );


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
     *      Morfy::factory()->run($path);
     *  </code>
     *
     * @param string $path Config path
     * @access  public
     */
    public function run($path)
    {

        // Load config file
        $this->loadConfig($path);

        // Set default timezone
        @ini_set('date.timezone', static::$config['site_timezone']);
        if (function_exists('date_default_timezone_set')) {
            date_default_timezone_set(static::$config['site_timezone']);
        } else {
            putenv('TZ='.static::$config['site_timezone']);
        }

        /**
         * Sanitize URL to prevent XSS - Cross-site scripting
         */
        $this->runSanitizeURL();

        /**
         * Send default header and set internal encoding
         */
        header('Content-Type: text/html; charset='.static::$config['site_charset']);
        function_exists('mb_language') and mb_language('uni');
        function_exists('mb_regex_encoding') and mb_regex_encoding(static::$config['site_charset']);
        function_exists('mb_internal_encoding') and mb_internal_encoding(static::$config['site_charset']);

        /**
         * Gets the current configuration setting of magic_quotes_gpc
         * and kill magic quotes
         */
        if (get_magic_quotes_gpc()) {
            function stripslashesGPC(&$value) { $value = stripslashes($value); }
            array_walk_recursive($_GET, 'stripslashesGPC');
            array_walk_recursive($_POST, 'stripslashesGPC');
            array_walk_recursive($_COOKIE, 'stripslashesGPC');
            array_walk_recursive($_REQUEST, 'stripslashesGPC');
        }

        // Load Plugins
        $this->loadPlugins();
        $this->runAction('plugins_loaded');

        // Get page for current requested url
        $page = $this->getPage($this->getUrl());

        // Overload page title, keywords and description
        empty($page['title']) and $page['title'] = static::$config['site_title'];
        empty($page['keywords']) and $page['keywords'] = static::$config['site_keywords'];
        empty($page['description']) and $page['description'] = static::$config['site_description'];

        $page   = $page;
        $config = self::$config;

        // Load template
        $this->runAction('before_render');
        require THEMES_PATH .'/'. $config['site_theme'] . '/'. ($template = !empty($page['template']) ? $page['template'] : 'index') .'.html';
        $this->runAction('after_render');
    }

    /**
     * Get Url
     *
     *  <code>
     *      $url = Morfy::factory()->getUrl();
     *  </code>
     *
     * @access  public
     * @return string
     */
    public function getUrl()
    {
        // Get request url and script url
        $url = '';
        $request_url = (isset($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : '';
        $script_url  = (isset($_SERVER['PHP_SELF'])) ? $_SERVER['PHP_SELF'] : '';

        // Get our url path and trim the / of the left and the right
        if ($request_url != $script_url) $url = trim(preg_replace('/'. str_replace('/', '\/', str_replace('index.php', '', $script_url)) .'/', '', $request_url, 1), '/');
        $url = preg_replace('/\?.*/', '', $url); // Strip query string

        return $url;
    }

    /**
     * Get Uri Segments
     *
     *  <code>
     *      $uri_segments = Morfy::factory()->getUriSegments();
     *  </code>
     *
     * @access  public
     * @return array
     */
    public function getUriSegments() {
        return explode('/', $this->getUrl());
    }

    /**
     * Get Uri Segment
     *
     *  <code>
     *      $uri_segment = Morfy::factory()->getUriSegment(1);
     *  </code>
     *
     * @access  public
     * @return string
     */
    public function getUriSegment($segment) {
        $segments = $this->getUriSegments();
        return isset($segments[$segment]) ? $segments[$segment] : null;
    }

    /**
     * Create safe url.
     *
     *  <code>
     *      $url = Morfy::factory()->sanitizeURL($url);
     *  </code>
     *
     * @access  public
     * @param  string $url Url to sanitize
     * @return string
     */
    public function sanitizeURL($url)
    {
        $url = trim($url);
        $url = rawurldecode($url);
        $url = str_replace(array('--','&quot;','!','@','#','$','%','^','*','(',')','+','{','}','|',':','"','<','>',
                                  '[',']','\\',';',"'",',','*','+','~','`','laquo','raquo',']>','&#8216;','&#8217;','&#8220;','&#8221;','&#8211;','&#8212;'),
                            array('-','-','','','','','','','','','','','','','','','','','','','','','','','','','','',''),
                            $url);
        $url = str_replace('--', '-', $url);
        $url = rtrim($url, "-");
        $url = str_replace('..', '', $url);
        $url = str_replace('//', '', $url);
        $url = preg_replace('/^\//', '', $url);
        $url = preg_replace('/^\./', '', $url);

        return $url;
     }

    /**
     * Sanitize URL to prevent XSS - Cross-site scripting
     *
     *  <code>
     *      Morfy::factory()->runSanitizeURL();
     *  </code>
     *
     * @access  public
     * @return void
     */
    public function runSanitizeURL()
    {
        $_GET = array_map(array($this, 'sanitizeURL'), $_GET);
    }


   /**
     * Get pages
     *
     *  <code>
     *      $pages = Morfy::factory()->getPages(CONTENT_PATH . '/blog/');
     *  </code>
     *
     * @param  string  $url        Url
     * @param  string  $order_by   Order by
     * @param  string  $order_type Order type
     * @param  array   $ignore     Pages to ignore
     * @param  int     $limit      Limit of pages
     * @return array
     */
    public function getPages($url, $order_by = 'date', $order_type = 'DESC', $ignore = array('404'), $limit = null)
    {

        // Page headers
        $page_headers = $this->page_headers;

        $pages = $this->getFiles($url);

        foreach($pages as $key => $page) {
            
            if (!in_array(basename($page, '.md'), $ignore)) {            

                $content = file_get_contents($page);

                $_page_headers = explode(Morfy::SEPARATOR, $content);

                foreach ($page_headers as $field => $regex) {
                    if (preg_match('/^[ \t\/*#@]*' . preg_quote($regex, '/') . ':(.*)$/mi', $_page_headers[0], $match) && $match[1]) {
                        $_pages[$key][ $field ] = trim($match[1]);
                    } else {
                        $_pages[$key][ $field ] = '';
                    }
                }

                $url = str_replace(CONTENT_PATH, Morfy::$config['site_url'], $page);
                $url = str_replace('index.md', '', $url);
                $url = str_replace('.md', '', $url);
                $url = str_replace('\\', '/', $url);
                $url = rtrim($url, '/');
                $_pages[$key]['url'] = $url;

                $_content = $this->parseContent($content);        
                if(is_array($_content)) {
                    $_pages[$key]['content_short'] = $_content['content_short'];
                    $_pages[$key]['content'] = $_content['content_full'];
                } else {
                    $_pages[$key]['content_short'] = $_content;
                    $_pages[$key]['content'] = $_content;
                }

                $_pages[$key]['slug'] = basename($page, '.md');

            }
        }

        $_pages = $this->subvalSort($_pages, $order_by, $order_type);

        if($limit != null) $_pages = array_slice($_pages, null, $limit);

        return $_pages;
    }

    /**
     * Get page
     *
     *  <code>
     *      $page = Morfy::factory()->getPage('downloads');
     *  </code>
     *
     * @param  string $url Url
     * @return array
     */
    public function getPage($url)
    {
        // Page headers
        $page_headers = $this->page_headers;

        // Get the file path
        if($url) $file = CONTENT_PATH . '/' . $url; else $file = CONTENT_PATH . '/' .'index';

        // Load the file
        if(is_dir($file)) $file = CONTENT_PATH . '/' . $url .'/index.md'; else $file .= '.md';

        if (file_exists($file)) {
            $content = file_get_contents($file);
        } else {
            $content = file_get_contents(CONTENT_PATH . '/' . '404.md');
            header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
        }

        $_page_headers = explode(Morfy::SEPARATOR, $content);

        foreach ($page_headers as $field => $regex) {
            if (preg_match('/^[ \t\/*#@]*' . preg_quote($regex, '/') . ':(.*)$/mi', $_page_headers[0], $match) && $match[1]) {
                $page[ $field ] = trim($match[1]);
            } else {
                $page[ $field ] = '';
            }            
        }

        $url = str_replace(CONTENT_PATH, Morfy::$config['site_url'], $file);
        $url = str_replace('index.md', '', $url);
        $url = str_replace('.md', '', $url);
        $url = str_replace('\\', '/', $url);
        $url = rtrim($url, '/');
        $pages['url'] = $url;

        $_content = $this->parseContent($content);        
        if(is_array($_content)) {
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
     * Get list of files in directory recursive
     *
     *  <code>
     *      $files = Morfy::factory()->getFiles('folder');
     *      $files = Morfy::factory()->getFiles('folder', 'txt');
     *      $files = Morfy::factory()->getFiles('folder', array('txt', 'log'));
     *  </code>
     *
     * @param  string $folder Folder
     * @param  mixed  $type   Files types
     * @return array
     */
    public static function getFiles($folder, $type = null)
    {
        $data = array();
        if (is_dir($folder)) {
            $iterator = new RecursiveDirectoryIterator($folder);
            foreach (new RecursiveIteratorIterator($iterator) as $file) {
                if ($type !== null) {
                    if (is_array($type)) {
                        $file_ext = substr(strrchr($file->getFilename(), '.'), 1);
                        if (in_array($file_ext, $type)) {
                            if (strpos($file->getFilename(), $file_ext, 1)) {
                                $data[] = $file->getPathName();
                            }
                        }
                    } else {
                        if (strpos($file->getFilename(), $type, 1)) {
                            $data[] = $file->getPathName();
                        }
                    }
                } else {
                    if ($file->getFilename() !== '.' && $file->getFilename() !== '..') $data[] = $file->getPathName();
                }
            }

            return $data;
        } else {
            return false;
        }
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
        $content = str_replace('{site_url}', static::$config['site_url'], $_content);

        // Parse {morfy_separator}
        $content = str_replace('{morfy_separator}', Morfy::SEPARATOR, $content);

        // Parse {morfy_version}
        $content = str_replace('{morfy_version}', Morfy::VERSION, $content);

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
        foreach (static::$config['plugins'] as $plugin) {
            include_once PLUGINS_PATH .'/'. $plugin.'/'.$plugin.'.php';
        }
    }

    /**
     * Load Config
     */
    protected function loadConfig($path)
    {
        if (file_exists($path)) {
            static::$config = require $path;
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
            $actions = $this->subvalSort(static::$actions, 'priority');

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
     * @param  string $filter_name The name of the filter hook.
     * @param  mixed  $value       The value on which the filters hooked.
     * @return mixed
     */
    public static function applyFilter($filter_name, $value)
    {
        // Redefine arguments
        $filter_name = (string) $filter_name;

        $args = array_slice(func_get_args(), 2);

        if ( ! isset(static::$filters[$filter_name])) {
            return $value;
        }

        foreach (static::$filters[$filter_name] as $priority => $functions) {
            if ( ! is_null($functions)) {
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
        $function_to_add = (string) $function_to_add;
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
     * Subval sort
     *
     *  <code>
     *      $new_array = Morfy::factory()->subvalSort($old_array, 'sort');
     *  </code>
     *
     * @param  array  $a      Array
     * @param  string $subkey Key
     * @param  string $order  Order type DESC or ASC
     * @return array
     */
    public function subvalSort($a, $subkey, $order = null)
    {
        if (count($a) != 0 || (!empty($a))) {
            foreach ($a as $k => $v) $b[$k] = function_exists('mb_strtolower') ? mb_strtolower($v[$subkey]) : strtolower($v[$subkey]);
            if ($order == null || $order == 'ASC') asort($b); else if ($order == 'DESC') arsort($b);
            foreach ($b as $key => $val) $c[] = $a[$key];

            return $c;
        }
    }
}
