<?php

/**
 *  Parsedown plugin
 *
 *  @package Morfy
 *  @subpackage Plugins
 *  @author Romanenko Sergey / Awilum
 *  @copyright 2014 - 2015 Romanenko Sergey / Awilum
 *  @version 1.0.0
 *
 */

include PLUGINS_PATH . '/parsedown/parsedown/Parsedown.php';
include PLUGINS_PATH . '/parsedown/parsedown/ParsedownExtra.php';

Morfy::factory()->addFilter('content', 'parsedown', 1);

function parsedown($content)
{
    $ParsedownExtra = new ParsedownExtra();
    return $ParsedownExtra->text($content);
}
