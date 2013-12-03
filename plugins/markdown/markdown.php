<?php

    use \Michelf\MarkdownExtra;
    include PLUGINS_PATH . '/markdown/php-markdown/Michelf/Markdown.php';
    include PLUGINS_PATH . '/markdown/php-markdown/Michelf/MarkdownExtra.php';

    Morfy::factory()->addFilter('content', 'markdown', 1);

    function markdown($content)
    {
        return MarkdownExtra::defaultTransform($content);
    }
