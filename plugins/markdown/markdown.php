<?php

/**
 * Markdown plugin
 *
 *  @package Morfy
 *  @subpackage Plugins
 *  @author Romanenko Sergey / Awilum
 *  @copyright 2014 - 2015 Romanenko Sergey / Awilum
 *  @version 1.0.0
 *
 */

use \Michelf\MarkdownExtra;
include PLUGINS_PATH . '/markdown/php-markdown/Michelf/Markdown.php';
include PLUGINS_PATH . '/markdown/php-markdown/Michelf/MarkdownExtra.php';

Morfy::factory()->addFilter('content', 'markdown', 1);

function markdown($content)
{
    return MarkdownExtra::defaultTransform($content);
}
