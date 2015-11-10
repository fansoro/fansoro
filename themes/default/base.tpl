<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="{$description}>">

		{Action::run('theme_meta')}

		<link rel="shortcut icon" href="{$.config.site.url}/favicon.ico">

		<title>{$.config.site.title} | {$title}</title>

		{* Bootstrap core CSS *}
		<link href="{Url::getBase()}/themes/{$.config.system.theme}/assets/css/bootstrap.min.css" rel="stylesheet">
		<link href="{Url::getBase()}/themes/{$.config.system.theme}/assets/css/theme.css" rel="stylesheet">
		{Action::run('theme_header')}

	</head>
	<body>
		<div id="wrap">
			{include 'navbar.tpl'}
			{Action::run('theme_content_before')}
			{block 'content'}{/block}
			{Action::run('theme_content_after')}
		</div>
		<div id="footer">
			<div class="container">
			    <p class="text-muted pull-right">Powered by <a href="http://morfy.org" title="Simple and fast file-based CMS">Morfy</a></p>
			</div>
		</div>
		{* Bootstrap core JavaScript *}
		{* Placed at the end of the document so the pages load faster *}
		<script src="{Url::getBase()}/themes/{$.config.site.theme}/assets/js/jquery.min.js"></script>
		<script src="{Url::getBase()}/themes/{$.config.site.theme}/assets/js/bootstrap.min.js"></script>
		{Action::run('theme_footer')}
	</body>
</html>
