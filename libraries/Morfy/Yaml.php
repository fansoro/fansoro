<?php

/**
 * This file is part of the Morfy.
 *
 * (c) Romanenko Sergey / Awilum <awilum@msn.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Yaml
{
    /**
     * Parses YAML to array.
     *
     * @param string $file Path to YAML file.
     * @return array
     */
    public static function parseFile($file)
    {
        return Spyc::YAMLLoad($file);
    }

    /**
     * Parses YAML to array.
     *
     * @param string $string YAML string.
     * @return array
     */
    public static function parse($string)
    {
        return Spyc::YAMLLoadString($string);
    }

    /**
     * Dumps array to YAML.
     *
     * @param array $data Array.
     * @return string
     */
    public static function dump($data)
    {
        return Spyc::YAMLDump($data, false, false, true);
    }
}
