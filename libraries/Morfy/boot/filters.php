<?php

/**
 * This file is part of the Morfy.
 *
 * (c) Romanenko Sergey / Awilum <awilum@msn.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// Add Shortcode parser filter
Filters::add('content', 'Shortcode::parse', 1);

// Add Parsedown parser filter
Filters::add('content', 'Markdown::parse', 2);
