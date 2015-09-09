Title: Welcome  
Description: Morfy is a simple and light-weighted Content Management System  
Template: index  

----

## Welcome to Morfy

Welcome to your new Morfy powered website. 


### Creating Content

Morfy doesnt provide any administration backend and database to deal with.  
You just create `.md` files in the `content` folder and that becomes a page.

That how it looks:

<table class="table">
    <thead>
        <tr><th>Physical Location</th><th>URL</th></tr>
    </thead>
    <tbody>
        <tr><td>content/index.md</td><td>/</td></tr>
        <tr><td>content/sub.md</td><td>/sub</td></tr>
        <tr><td>content/sub/index.md</td><td>/sub (same as above)</td></tr>
        <tr><td>content/sub/page.md</td><td>/sub/page</td></tr>
        <tr><td>content/a/very/long/url.md</td><td>/a/very/long/url</td></tr>
    </tbody>
</table>


#### Text File Markup

Text files are marked up using Markdown Plugin. They can also contain regular HTML.
At the top of text files you can place a block comment and specify certain attributes of the page.

Example:
	
	Title: Welcome  
	Description: Some description here   
    Keywords: key, words
	Author: Awilum  
	Date: 2015-09-01 16:08 
	Tags: tag1, tag2
    Robots: noindex,nofollow  
	Template: index (allows you to use different templates in your theme)  
	{morfy_separator}

<br>

#### Text File Vars

Write text file vars inside `{}` e.g. `{var}`

<table class="table">
    <thead>
        <tr><th>Name</th><th>Description</th></tr>
    </thead>
    <tbody>
        <tr><td>site_url</td><td>Outputs site url</td></tr>
        <tr><td>morfy_separator</td><td>Outputs morfy separator</td></tr>
        <tr><td>morfy_version</td><td>Outputs morfy version</td></tr>
        <tr><td>cut</td><td>Page Cut</td></tr>
    </tbody>
</table>

### Themes

You can create themes for your Morfy installation and in the "themes" folder.
To setup your theme just update `theme` setting in config.php

All themes must include an index.html file to define the HTML structure of the theme. 
You can seperate index.html to header.html and footer.html on your wish and easy include theme:
`<?php include 'header.html' ?>` and `<?php include 'footer.html' ?>`

<br>

#### Theme variables

<table class="table">
    <thead>
        <tr><th>Name</th><th>Description</th></tr>
    </thead>
    <tbody>
        <tr><td>Config</td><td></td></tr>
        <tr><td>$config['site_url']</td><td>Site url</td></tr>
        <tr><td>$config['site_charset']</td><td>Site charset</td></tr>
        <tr><td>$config['site_timezone']</td><td>Site timezone</td></tr>
        <tr><td>$config['site_theme']</td><td>Site theme</td></tr>
        <tr><td>$config['site_title']</td><td>Site title</td></tr>
        <tr><td>$config['site_description']</td><td>Site description</td></tr>
        <tr><td>$config['site_keywords']</td><td>Site keywords</td></tr>
        <tr><td>$config['email']</td><td>Email</td></tr>
        <tr><td>Page</td><td></td></tr>
        <tr><td>$page['title']</td><td>Page title</td></tr>
        <tr><td>$page['description']</td><td>Page description</td></tr>
        <tr><td>$page['keywords']</td><td>Page keywords</td></tr>
        <tr><td>$page['tags']</td><td>Page tags</td></tr>
        <tr><td>$page['url']</td><td>Page url</td></tr>
        <tr><td>$page['author']</td><td>Page author</td></tr>
        <tr><td>$page['date']</td><td>Page date</td></tr>
        <tr><td>$page['robots']</td><td>Page robots</td></tr>
        <tr><td>$page['template']</td><td>Page template</td></tr>
    </tbody>
</table>

Example how to output variable: `<?php echo $page['title']; ?>`


### Config
You can set your own site title, keywords, description and etc.. by editing config.php in the root Morfy directory. 

### Documentation
For more help have a look at the Mory documentation at [http://morfy.org/documentation](http://morfy.org/documentation)
