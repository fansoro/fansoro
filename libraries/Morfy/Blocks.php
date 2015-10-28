<?php

/**
 * This file is part of the Morfy.
 *
 * (c) Romanenko Sergey / Awilum <awilum@msn.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Blocks
{
    /**
     * Get Page Block
     *
     *  <code>
     *      $content = Blocks::get('my-block');
     *  </code>
     *
     * @access  public
     * @param  string $name Block name
     * @return string Formatted Block content
     */
    public static function get($name)
    {
        if (File::exists($block_path = BLOCKS_PATH . '/' . $name . '.md')) {
            return Pages::parseContent(file_get_contents($block_path));
        } else {
            return 'Block '.$name.' is not found!';
        }
    }
}
