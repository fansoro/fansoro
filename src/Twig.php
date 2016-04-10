<?php
namespace Fansoro;

use Twig_Autoloader;
use Twig_Loader_Filesystem;
use Twig_Environment;
use Twig_Loader_Array;
use Twig_Loader_Chain;
use Twig_Extension_Escaper;

/**
 * This file is part of the Fansoro.
 *
 * (c) Romanenko Sergey / Awilum <awilum@msn.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Twig
{
    /**
     * @var Fansoro
     */
    protected $fansoro;

    protected $loader;

    protected $loaderArray;

    protected static $twig_paths;

    protected $twig;

    /**
     * Construct
     */
    public function __construct(Fansoro $c)
    {
        $this->fansoro = $c;

        Twig_Autoloader::register();

        static::$twig_paths[] = 'themes/'.$this->fansoro['config']->get('system.theme');

        $this->loader = new Twig_Loader_Filesystem(static::$twig_paths);
        $this->loaderArray = new Twig_Loader_Array(array());
    }

    public function renderPage($page)
    {
        $loader_chain = new Twig_Loader_Chain(array($this->loaderArray, $this->loader));

        $this->twig = new Twig_Environment($this->loader, array(
            //'cache' => CACHE_PATH,
            array('debug' => true)
        ));

        if (empty($page['template'])) {
            $template_name = 'index';
        } else {
            $template_name = $page['template'];
        }

        $template_ext = '.html.twig';

        echo $this->twig->render($template_name.$template_ext,
            ['site'    => $this->fansoro['config']->get('site'),
            'theme'    => $this->fansoro['config']->get('system.theme'),
            'plugins'  => $this->fansoro['config']->get('system.plugins'),
            'uri'      => \Url::getUriSegments(),
            'base_url' => \Url::getBase(),
            'page'     => $page]);
    }

    public function addPath($path)
    {
        static::$twig_paths[] = $path;
    }

    public function twig()
    {
        return $this->twig;
    }
}
