<?php

/**
 * This file is part of the Fansoro.
 *
 * (c) Romanenko Sergey / Awilum <awilum@msn.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Blocks
{
    /**
     * An instance of the Blocks class
     *
     * @var object
     * @access  protected
     */
    protected static $instance = null;

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
        $blocks_cache_id = '';

        $blocks = File::scan(STORAGE_PATH . '/blocks', 'md');

        if ($blocks) {
            foreach ($blocks as $block) {
                $blocks_cache_id .= filemtime($block);
            }

            // Create Unique Cache ID for Block
            $blocks_cache_id = md5('blocks' . ROOT_DIR . $blocks_cache_id);
        }

        if (Cache::driver()->contains($blocks_cache_id)) {
            Cache::driver()->fetch($blocks_cache_id);
        } else {
            Config::set('system.pages.flush_cache', true);
            Cache::driver()->save($blocks_cache_id, $blocks_cache_id);
        }
    }

    /**
     * Get Page Block
     *
     *  <code>
     *      $block = Blocks::get('my-block');
     *      $block_raw = Blocks::get('my-block', true);
     *  </code>
     *
     * @access public
     * @param  string  $name Block name
     * @param  boolean $raw  Return raw block content. Default is false
     * @return string Formatted Block content
     */
    public static function get($name, $raw = false)
    {
        if (File::exists($block_path = STORAGE_PATH .'/blocks/' . $name . '.md')) {

            // Create Unique Cache ID for Block
            $block_cache_id = md5('block' . ROOT_DIR . $block_path . (($raw) ? 'true' : 'false') . filemtime($block_path));

            if (Cache::driver()->contains($block_cache_id)) {
                return Cache::driver()->fetch($block_cache_id);
            } else {
                $block = (($raw) ? trim(file_get_contents($block_path)) : trim(Filter::apply('content', file_get_contents($block_path))));
                Cache::driver()->save($block_cache_id, $block);
                return $block;
            }
        } else {
            return 'Block '.$name.' is not found!';
        }
    }

    /**
     * Initialize Fansoro Blocks
     *
     *  <code>
     *      Blocks::init();
     *  </code>
     *
     * @access  public
     */
    public static function init()
    {
        return !isset(self::$instance) and self::$instance = new Blocks();
    }
}
