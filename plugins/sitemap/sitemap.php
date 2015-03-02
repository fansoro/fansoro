<?php

/**
 *  Sitemap plugin
 *
 *  @package Morfy
 *  @subpackage Plugins
 *  @author Romanenko Sergey / Awilum
 *  @copyright 2014 - 2015 Romanenko Sergey / Awilum
 *  @version 1.0.0
 *
 */

 
if (Morfy::factory()->getUrl() == 'sitemap.xml') {	
    Morfy::factory()->addAction('before_render', function() {
    	header($_SERVER['SERVER_PROTOCOL'].' 200 OK');
header("Content-Type: text/xml; charset=utf-8");
echo '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
$pages = Morfy::factory()->getPages(CONTENT_PATH, 'date', 'DESC', array('404'));
foreach($pages as $page) {
echo ('<url>
		<loc>'.$page['url'].'</loc>
		<lastmod>'.$page['date'].'</lastmod>
		<changefreq>weekly</changefreq>
		<priority>1.0</priority>
	</url>');
}
echo '
</urlset>
';

        exit();
    });
}
