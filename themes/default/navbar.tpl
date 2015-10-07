<div class="navbar navbar-default navbar-fixed-top" role="navigation">
	<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="{$.site.url}">{$.site.title}</a>
		</div>
		<div class="collapse navbar-collapse">
			<ul class="nav navbar-nav">
				{set $slug = Url::getUriSegment(0)}
				<li {if $slug == ''} class="active" {/if}><a href="{$.site.url}">Home</a></li>
				<li {if $slug == 'blog'} class="active" {/if}><a href="{$.site.url}/blog">Blog</a></li>
				<li {if $slug == 'about'} class="active" {/if}><a href="{$.site.url}/about">About</a></li>
			</ul>
		</div>
	</div>
</div>
