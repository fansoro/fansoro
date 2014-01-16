<?php

if (Morfy::factory()->getUrl() == 'sitemap.xml') {
    Morfy::factory()->addAction('before_render', function() {
        echo 'Sitemap';
        exit();
    });
}
