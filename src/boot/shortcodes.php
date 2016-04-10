<?php

/**
 * This file is part of the Fansoro.
 *
 * (c) Romanenko Sergey / Awilum <awilum@msn.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// Add {block name=block-name raw=true} shortcode
Shortcode::add('block', function ($attributes) {
    if (isset($attributes['name'])) {
        return Blocks::get($attributes['name'], (($attributes['raw'] && $attributes['raw'] == 'true') ? true : false));
    }
});

// Add {site_url} shortcode
Shortcode::add('site_url', function () {
    return Url::getBase();
});
