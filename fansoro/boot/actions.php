<?php

/**
 * This file is part of the Fansoro.
 *
 * (c) Romanenko Sergey / Awilum <awilum@msn.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// Set Fansoro Meta Generator
Action::add('theme_meta', function () {
    echo('<meta name="generator" content="Powered by Fansoro" />');
});
