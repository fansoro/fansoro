<?php

/**
 * This file is part of the Force Components.
 *
 * (c) Romanenko Sergey / Awilum <awilum@msn.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Url
{
    /**
     * Protected constructor since this is a static class.
     *
     * @access  protected
     */
    protected function __construct()
    {
        // Nothing here
    }

    /**
      * Gets the base URL
      *
     *  <code>
     *      echo Url::getBase();
     *  </code>
     *
      * @return string
     */
    public static function getBase()
    {
        $https = (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') ? 'https://' : 'http://';

        return $https . rtrim(rtrim($_SERVER['HTTP_HOST'], '\\/') . dirname($_SERVER['PHP_SELF']), '\\/');
    }

    /**
     * Gets current URL
     *
     *  <code>
     *      echo Url::getCurrent();
     *  </code>
     *
     * @return string
     */
    public static function getCurrent()
    {
        return (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] : "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
    }

    /**
     * Get Uri String
     *
     *  <code>
     *      $uri_string = Url::getUriString();
     *  </code>
     *
     * @access  public
     * @return string
     */
    public static function getUriString()
    {
        // Get request url and script url
        $url = '';
        $request_url = (isset($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : '';
        $script_url  = (isset($_SERVER['PHP_SELF'])) ? $_SERVER['PHP_SELF'] : '';

        // Get our url path and trim the / of the left and the right
        if ($request_url != $script_url) {
            $url = trim(preg_replace('/'. str_replace('/', '\/', str_replace('index.php', '', $script_url)) .'/', '', $request_url, 1), '/');
        }
        $url = preg_replace('/\?.*/', '', $url); // Strip query string

        return $url;
    }

    /**
     * Get Uri Segments
     *
     *  <code>
     *      $uri_segments = Url::getUriSegments();
     *  </code>
     *
     * @access  public
     * @return array
     */
    public static function getUriSegments()
    {
        return explode('/', self::getUriString());
    }

    /**
     * Get Uri Segment
     *
     *  <code>
     *      $uri_segment = Url::getUriSegment(1);
     *  </code>
     *
     * @access  public
     * @return string
     */
    public static function getUriSegment($segment)
    {
        $segments = self::getUriSegments();
        return isset($segments[$segment]) ? $segments[$segment] : null;
    }

    /**
     * Create safe url.
     *
     *  <code>
     *      $url = Url::sanitizeURL($url);
     *  </code>
     *
     * @access  public
     * @param  string $url Url to sanitize
     * @return string
     */
    public static function sanitizeURL($url)
    {
        $url = trim($url);
        $url = rawurldecode($url);
        $url = str_replace(array('--', '&quot;', '!', '@', '#', '$', '%', '^', '*', '(', ')', '+', '{', '}', '|', ':', '"', '<', '>',
            '[', ']', '\\', ';', "'", ',', '*', '+', '~', '`', 'laquo', 'raquo', ']>', '&#8216;', '&#8217;', '&#8220;', '&#8221;', '&#8211;', '&#8212;'),
            array('-', '-', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
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
     *      Url::runSanitizeURL();
     *  </code>
     *
     * @access  public
     * @return void
     */
    public function runSanitizeURL()
    {
        $_GET = array_map('Url::sanitizeURL', $_GET);
    }
}
