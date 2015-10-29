{extends 'base.tpl'}
{block 'content'}
	<div class="container">
		{set $posts = Pages::getPages('blog', 'date', 'DESC', ['404','index'])}
		{foreach $posts as $post}
			<h3><a href="{$.config.site.url}/blog/{$post.slug}">{$post.title}</a></h3>
			<p>Posted on {$post.date}</p>
			<div>{$post.summary}</div>
		{/foreach}
	</div>
{/block}
