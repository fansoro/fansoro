{extends 'layout.tpl'}
{block 'content'}
	<div class="container">
		<h1>{$title}</h1>
		<p>Posted on {$date}</p>
		<div>{$content}</div>
	</div>
{/block}
