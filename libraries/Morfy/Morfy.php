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
     * Config array.
     *
     * @var array
     */
    protected static $config;

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
     * Constructor.
     *
     * @access  public
     */
    public function __construct()
    {
        // Nothing here
    }

    /**
     * Factory method making method chaining possible right off the bat.
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
        static::$config = require $path;

        // Set default timezone
        @ini_set('date.timezone', static::$config['site_timezone']);
        if (function_exists('date_default_timezone_set')) {
            date_default_timezone_set(static::$config['site_timezone']);
        } else {
            putenv('TZ='.static::$config['site_timezone']);
        }

        /**
         * Send default header and set internal encoding
         */
        header('Content-Type: text/html; charset='.static::$config['site_charset']);
        function_exists('mb_language') AND mb_language('uni');
        function_exists('mb_regex_encoding') AND mb_regex_encoding(static::$config['site_charset']);
        function_exists('mb_internal_encoding') AND mb_internal_encoding(static::$config['site_charset']);

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

        $this->loadPlugins();

        // Get page
        $_page = $this->getPage($this->getUrl());
        $page_data = $_page['page'];
        $page_data['slug'] = $_page['file'];

        // Select current template
        if (!empty($page_data['template'])) $template = $page_data['template']; else $template = 'index';

        // Overload page title, keywords and description
        if (empty($page_data['title'])) $page_data['title'] = static::$config['site_title'];
        if (empty($page_data['keywords'])) $page_data['keywords'] = static::$config['site_keywords'];
        if (empty($page_data['description'])) $page_data['description'] = static::$config['site_description'];

        // Vars for Template
        $page   = (object) $page_data;
        $config = (object) self::$config;

        // Load template
        $this->runAction('before_render');
        include THEMES_PATH .'/'. static::$config['site_theme'] . '/'. $template .'.html';
        $this->runAction('after_render');
    }

    /**
     * Get Url
     */
    protected function getUrl()
    {
        // Get request url and script url
        $url = '';
        $request_url = (isset($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : '';
        $script_url  = (isset($_SERVER['PHP_SELF'])) ? $_SERVER['PHP_SELF'] : '';

        // Get our url path and trim the / of the left and the right
        if($request_url != $script_url) $url = trim(preg_replace('/'. str_replace('/', '\/', str_replace('index.php', '', $script_url)) .'/', '', $request_url, 1), '/');
        $url = preg_replace('/\?.*/', '', $url); // Strip query string

        return $url;
    }

    /**
     * Get page
     * 
     * @param string $url Url
     */
    protected function getPage($url)
    {

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

        $page = $this->getPageHeaders($content);
        $page['content'] = $this->parseContent($content);

        return array('page' => $page, 'file' => basename($file, '.md'));
    }

    /**
     * Get Page Headers
     * 
     * @param  string $content Content to parse
     * @return array  $content Headers array
     */
    protected function getPageHeaders($content)
    {

        $headers = array(
            'title'         => 'Title',
            'description'   => 'Description',
            'author'        => 'Author',
            'date'          => 'Date',
            'robots'        => 'Robots',
            'template'      => 'Template',
        );

        foreach ($headers as $field => $regex) {
            if (preg_match('/^[ \t\/*#@]*' . preg_quote($regex, '/') . ':(.*)$/mi', $content, $match) && $match[1]) {
                $headers[ $field ] = trim(preg_replace("/\s*(?:\*\/|\?>).*/", '', $match[1]));
            } else {
                $headers[ $field ] = '';
            }
        }

        return $headers;
    }

    /**
     * Parses the content using Markdown
     *
     * @param  string $content Content to parse
     * @return string $content Formatted content
     */
    protected function parseContent($content)
    {
        $content = preg_replace('#/\*.+?\*/#s', '', $content);
        $content = str_replace('{site_url}', static::$config['site_url'], $content);
        $content = $this->applyFilter('content', $content);

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
     *  Hooks a function on to a specific action.
     *
     *  <code>
     *      // Hooks a function "newLink" on to a "footer" action.
     *      $this->addAction('footer', 'newLink', 10);
     *
     *      function newLink() {
     *          echo '<a href="#">My link</a>';
     *      }
     *  </code>
     *
     * @param string  $action_name    Action name
     * @param string  $added_function Added function
     * @param integer $priority       Priority. Default is 10
     * @param array   $args           Arguments
     */
    public function addAction($action_name, $added_function, $priority = 10, array $args = null)
    {
        // Hooks a function on to a specific action.
        static::$actions[] = array(
                        'action_name' => (string) $action_name,
                        'function'    => (string) $added_function,
                        'priority'    => (int) $priority,
                        'args'        => $args
        );
    }

    /**
     * Run functions hooked on a specific action hook.
     *
     *  <code>
     *      // Run functions hooked on a "footer" action hook.
     *      $this->runAction('footer');
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
     *      $this->applyFilter('content', $content);
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
     *      $this->addFilter('content', 'replacer');
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
     *      $new_array = $this->subvalSort($old_array, 'sort');
     *  </code>
     *
     * @param  array  $a      Array
     * @param  string $subkey Key
     * @param  string $order  Order type DESC or ASC
     * @return array
     */
    protected function subvalSort($a, $subkey, $order = null)
    {
        if (count($a) != 0 || (!empty($a))) {
            foreach ($a as $k => $v) $b[$k] = function_exists('mb_strtolower') ? mb_strtolower($v[$subkey]) : strtolower($v[$subkey]);
            if ($order == null || $order == 'ASC') asort($b); else if ($order == 'DESC') arsort($b);
            foreach ($b as $key => $val) $c[] = $a[$key];

            return $c;
        }
    }
}
