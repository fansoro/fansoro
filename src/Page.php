<?php
namespace Fansoro;

use Url;
use Response;
use Symfony\Component\Yaml\Yaml;

/**
 * This file is part of the Fansoro.
 *
 * (c) Romanenko Sergey / Awilum <awilum@msn.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Page
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

    /**
     * Get page
     */
    public function getPage($url = '', $raw = false)
    {
        $file = $this->finder($url);

        if ($raw) {
            $page = trim(file_get_contents($file));
        } else {
            $page = $this->parse($file);

            $page_frontmatter = $page['frontmatter'];
            $page_content = $page['content'];

            $page = $page_frontmatter;

            // Parse page for summary <!--more-->
            if (($pos = strpos($page_content, "<!--more-->")) === false) {
                $page_content = Filter::apply('content', $page_content);
            } else {
                $page_content = explode("<!--more-->", $page_content);
                $page['summary']  = Filter::apply('content', $page_content[0]);
                $page['content']  = Filter::apply('content', $page_content[0].$page_content[1]);
            }

            if (is_array($page_content)) {
                $page['summary'] = $page['summary'];
                $page['content'] = $page['content'];
            } else {
                $page['content'] = $page_content;
            }
        }

        return $page;
    }

    /**
     * Page finder
     */
    public function finder($url)
    {

        // If url is empty that its a homepage
        if ($url) {
            $file = STORAGE_PATH . '/pages/' . $url;
        } else {
            $file = STORAGE_PATH . '/pages/' . 'index';
        }

        // Select the file
        if (is_dir($file)) {
            $file = STORAGE_PATH . '/pages/' . $url .'/index.md';
        } else {
            $file .= '.md';
        }

        // Get 404 page if file not exists
        if (!$this->fansoro['filesystem']->exists($file)) {
            $file = STORAGE_PATH . '/pages/' . '404.md';
            Response::status(404);
        }

        return $file;
    }

    /**
     * Page parser
     */
    public function parse($file)
    {
        $page = trim(file_get_contents($file));

        $page = explode('---', $page, 3);

        $frontmatter = Yaml::parse($page[1]);
        $content = $page[2];

        $url = str_replace(STORAGE_PATH . '/pages', Url::getBase(), $file);
        $url = str_replace('index.md', '', $url);
        $url = str_replace('.md', '', $url);
        $url = str_replace('\\', '/', $url);
        $url = rtrim($url, '/');

        $frontmatter['url']  = $url;
        $frontmatter['slug'] = basename($file, '.md');

        return ['frontmatter' => $frontmatter, 'content' => $content];
    }
}
