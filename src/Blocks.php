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
     * @var Fansoro
     */
    protected $fansoro;

    /**
     * __construct
     */
    public function __construct(Fansoro $c)
    {
        $this->fansoro = $c;
    }
}
