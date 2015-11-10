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
			{include 'partials/navigation.tpl'}
			{Action::run('theme_content_before')}
			{block 'content'}{/block}
			{Action::run('theme_content_after')}
		</div>
		{include 'partials/footer.tpl'}
		{include 'partials/tail.tpl'}
	</body>
</html>
