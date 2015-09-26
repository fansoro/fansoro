<?php

/**
 * Statistics plugin
 *
 *  @package Morfy
 *  @subpackage Plugins
 *  @author Pavel Belousov / pafnuty
 *  @copyright 2014 - 2015 Romanenko Sergey / Awilum
 *  @version 1.0.0
 *
 */

//require_once PLUGINS_PATH . '/statistics/ShowStatistics.php';

/**
 * Gelato Library
 *
 * This source file is part of the Gelato Library. More information,
 * documentation and tutorials can be found at http://gelato.monstra.org
 *
 * @package     Gelato
 *
 * @author      Romanenko Sergey / Awilum <awilum@msn.com>
 * @copyright   2012-2014 Romanenko Sergey / Awilum <awilum@msn.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */



class Number
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
     * Convert bytes in 'KB','MB','GB','TiB','PiB'
     *
     *  <code>
     *      echo Number::byteFormat(10000);
     *  </code>
     *
     * @param  integer $size Data to convert
     * @return string
     */
    public static function byteFormat($size)
    {
        // Redefine vars
        $size = (int) $size;

        $unit = array('B', 'KB', 'MB', 'GB', 'TiB', 'PiB');

        return @round($size/pow(1024, ($i=floor(log($size, 1024)))), 2).' '.$unit[($i < 0 ? 0 : $i)];
    }

    /**
     * Convert 'KB','MB','GB' in bytes
     *
     *  <code>
     *      echo Number::convertToBytes('10MB');
     *  </code>
     *
     * @param  string $num Number to convert
     * @return int
     */
    public static function convertToBytes( $num ) {
        $num  = strtolower( $num );
        $bytes = (int) $num;
        if ( strpos( $num, 'k' ) !== false )
            $bytes = intval( $num ) * 1024;
        elseif ( strpos( $num, 'm' ) !== false )
            $bytes = intval($num) * 1024 * 1024;
        elseif ( strpos( $num, 'g' ) !== false )
            $bytes = intval( $num ) * 1024 * 1024 * 1024;
        return $bytes;
    }

    /**
     * Converts a number into a more readable human-type number.
     *
     *  <code>
     *      echo Number::quantity(7000); // 7K
     *      echo Number::quantity(7500); // 8K
     *      echo Number::quantity(7500, 1); // 7.5K
     *  </code>
     *
     * @param  integer $num      Num to convert
     * @param  integer $decimals Decimals
     * @return string
     */
    public static function quantity($num, $decimals = 0)
    {
        // Redefine vars
        $num      = (int) $num;
        $decimals = (int) $decimals;

        if ($num >= 1000 && $num < 1000000) {
            return sprintf('%01.'.$decimals.'f', (sprintf('%01.0f', $num) / 1000)).'K';
        } elseif ($num >= 1000000 && $num < 1000000000) {
            return sprintf('%01.'.$decimals.'f', (sprintf('%01.0f', $num) / 1000000)).'M';
        } elseif ($num >= 1000000000) {
            return sprintf('%01.'.$decimals.'f', (sprintf('%01.0f', $num) / 1000000000)).'B';
        }

        return $num;
    }

    /**
     * Checks if the value is between the minimum and maximum (min & max included).
     *
     *  <code>
     *      if (Number::between(2, 10, 5)) {
     *          // do something...
     *      }
     *  </code>
     *
     * @param  float   $minimum The minimum.
     * @param  float   $maximum The maximum.
     * @param  float   $value   The value to validate.
     * @return boolean
     */
    public static function between($minimum, $maximum, $value)
    {
        return ((float) $value >= (float) $minimum && (float) $value <= (float) $maximum);
    }

    /**
     * Checks the value for an even number.
     *
     *  <code>
     *      if (Number::even(2)) {
     *          // do something...
     *      }
     *  </code>
     *
     * @param  integer $value The value to validate.
     * @return boolean
     */
    public static function even($value)
    {
        return (((int) $value % 2) == 0);
    }

    /**
     * Checks if the value is greather than a given minimum.
     *
     *  <code>
     *      if (Number::greaterThan(2, 10)) {
     *          // do something...
     *      }
     *  </code>
     *
     * @param  float   $minimum The minimum as a float.
     * @param  float   $value   The value to validate.
     * @return boolean
     */
    public static function greaterThan($minimum, $value)
    {
        return ((float) $value > (float) $minimum);
    }

    /**
     * Checks if the value is smaller than a given maximum.
     *
     *  <code>
     *      if (Number::smallerThan(2, 10)) {
     *          // do something...
     *      }
     *  </code>
     *
     * @param  integer $maximum The maximum.
     * @param  integer $value   The value to validate.
     * @return boolean
     */
    public static function smallerThan($maximum, $value)
    {
        return ((int) $value < (int) $maximum);
    }

    /**
     * Checks if the value is not greater than or equal a given maximum.
     *
     *  <code>
     *      if (Number::maximum(2, 10)) {
     *          // do something...
     *      }
     *  </code>
     *
     * @param  integer $maximum The maximum.
     * @param  integer $value   The value to validate.
     * @return boolean
     */
    public static function maximum($maximum, $value)
    {
        return ((int) $value <= (int) $maximum);
    }

    /**
     * Checks if the value is greater than or equal to a given minimum.
     *
     *  <code>
     *      if (Number::minimum(2, 10)) {
     *          // do something...
     *      }
     *  </code>
     *
     * @param  integer $minimum The minimum.
     * @param  integer $value   The value to validate.
     * @return boolean
     */
    public static function minimum($minimum, $value)
    {
        return ((int) $value >= (int) $minimum);
    }

    /**
     * Checks the value for an odd number.
     *
     *  <code>
     *      if (Number::odd(2)) {
     *          // do something...
     *      }
     *  </code>
     *
     * @param  integer $value The value to validate.
     * @return boolean
     */
    public static function odd($value)
    {
        return ! Number::even((int) $value);
    }

}

class Debug
{
    /**
     * Time
     *
     * @var array
     */
    protected static $time = array();

    /**
     * Memory
     *
     * @var array
     */
    protected static $memory = array();

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
     * Save current time for current point
     *
     *  <code>
     *      Debug::elapsedTimeSetPoint('point_name');
     *  </code>
     *
     * @param string $point_name Point name
     */
    public static function elapsedTimeSetPoint($point_name)
    {
        Debug::$time[$point_name] = microtime(true);
    }

    /**
     * Get elapsed time for current point
     *
     *  <code>
     *      echo Debug::elapsedTime('point_name');
     *  </code>
     *
     * @param  string $point_name Point name
     * @return string
     */
    public static function elapsedTime($point_name)
    {
        if (isset(Debug::$time[$point_name])) return sprintf("%01.4f", microtime(true) - Debug::$time[$point_name]);
    }

    /**
     * Save current memory for current point
     *
     *  <code>
     *      Debug::memoryUsageSetPoint('point_name');
     *  </code>
     *
     * @param string $point_name Point name
     */
    public static function memoryUsageSetPoint($point_name)
    {
        Debug::$memory[$point_name] = memory_get_usage();
    }

    /**
     * Get memory usage for current point
     *
     *  <code>
     *      echo Debug::memoryUsage('point_name');
     *  </code>
     *
     * @param  string $point_name Point name
     * @return string
     */
    public static function memoryUsage($point_name)
    {
        if (isset(Debug::$memory[$point_name])) return Number::byteFormat(memory_get_usage() - Debug::$memory[$point_name]);
    }

    /**
     * Print the variable $data and exit if exit = true
     *
     *  <code>
     *      Debug::dump($data);
     *  </code>
     *
     * @param mixed   $data Data
     * @param boolean $exit Exit
     */
    public static function dump($data, $exit = false)
    {
        echo "<pre>dump \n---------------------- \n\n" . print_r($data, true) . "\n----------------------</pre>";
        if ($exit) exit;
    }

}

Morfy::factory()->addAction('before_render', function () {
    //global $statistics;
    //$statistics = new ShowStatistics();
    Debug::elapsedTimeSetPoint('point_name');
    echo ' / ';
    Debug::memoryUsageSetPoint('point_name');
});

Morfy::factory()->addAction('after_render', function () {
     echo Debug::elapsedTime('point_name');
     echo ' / ';
     echo Debug::memoryUsage('point_name');
});
