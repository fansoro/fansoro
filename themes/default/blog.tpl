{extends 'layout.tpl'}
{block 'content'}
	<div class="container">
		{set $posts = Morfy::getPages('blog', 'date', 'DESC', ['404','index'])}
		{foreach $posts as $post}
			<h3><a href="{$.site.url}/blog/{$post.slug}">{$post.title}</a></h3>
			<p>Posted on {$post.date}</p>
			<div>{$post.summary}</div>
		{/foreach}
	</div>
{/block}
