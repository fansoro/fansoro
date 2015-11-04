<?php

/**
 * This file is part of the Morfy.
 *
 * (c) Romanenko Sergey / Awilum <awilum@msn.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// Set Morfy Meta Generator
Actions::add('theme_meta', function () {
    echo('<meta name="generator" content="Powered by Morfy" />');
});
