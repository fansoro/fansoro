<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="{$description}>">
		<meta name="keywords" content="{$keywords}">

		{Morfy::factory()->runAction('theme_meta')}

		<link rel="shortcut icon" href="{$.site.url}/favicon.ico">

		<title>{$.site.title} | {$title}</title>

		{* Bootstrap core CSS *}
		<link href="{$.site.url}/themes/{$.site.theme}/assets/css/bootstrap.min.css" rel="stylesheet">
		<link href="{$.site.url}/themes/{$.site.theme}/assets/css/default.css" rel="stylesheet">
		{Morfy::factory()->runAction('theme_header')}

	</head>
	<body>
		<div id="wrap">
			{include 'navbar.tpl'}
			{Morfy::factory()->runAction('theme_content_before')}
			{block 'content'}{/block}
			{Morfy::factory()->runAction('theme_content_after')}
		</div>
		<div id="footer">
			<div class="container">
			    <p class="text-muted pull-right">Powered by <a href="http://morfy.org" title="Simple and fast file-based CMS">Morfy</a></p>
			</div>
		</div>
		{* Bootstrap core JavaScript *}
		{* Placed at the end of the document so the pages load faster *}
		<script src="{$.site.url}/themes/{$.site.theme}/assets/js/jquery.min.js"></script>
		<script src="{$.site.url}/themes/{$.site.theme}/assets/js/bootstrap.min.js"></script>
		{Morfy::factory()->runAction('theme_footer')}
	</body>
</html>
