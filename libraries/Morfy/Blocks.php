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
     * @access public
     * @param  string $name Block name
     * @return string Formatted Block content
     */
    public static function get($name)
    {
        if (File::exists($block_path = BLOCKS_PATH . '/' . $name . '.md')) {

            // Create Unique Cache ID for Block
            $block_cache_id = md5('block' . ROOT_DIR . filemtime(BLOCKS_PATH . '/' . $name . '.md'));

            if (Cache::driver()->contains($block_cache_id)) {
                return Cache::driver()->fetch($block_cache_id);
            } else {
                Cache::driver()->save($block_cache_id, $block = Markdown::parse(file_get_contents($block_path)));
                return $block;
            }
        } else {
            return 'Block '.$name.' is not found!';
        }
    }
}
