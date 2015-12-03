# Morfy
[![Join the chat at https://gitter.im/morfy-cms/morfy](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/morfy-cms/morfy?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

Morfy is Modern Open Source Flat-File Content Management System.  
Content in Morfy is just a simple files written with markdown syntax in pages folder.   
You simply create markdown files in the pages folder and that becomes a page.

## Requirements
PHP 5.5 or higher with PHP's [Multibyte String module](http://php.net/mbstring)   
Apache with [Mod Rewrite](http://httpd.apache.org/docs/current/mod/mod_rewrite.html)  

## Installation

#### Using (S)FTP

[Download the latest version.](http://morfy.org/download)  

Unzip the contents to a new folder on your local computer, and upload to your webhost using the (S)FTP client of your choice. After you’ve done this, be sure to chmod the following directories (with containing files) to 777, so they are readable and writable by Morfy:  
* `cache/`
* `config/`
* `storage/`
* `themes/`
* `plugins/`

#### Using Composer

You can easily install Morfy with Composer.

```
composer create-project morfy-cms/morfy
```

## Contributing
1. Help on the [Forum.](http://forum.morfy.org)
2. Develop a new plugin.
3. Create a new theme.
4. Find and [report issues.](https://github.com/morfy-cms/morfy/issues)
5. Link back to [Morfy](http://morfy.org).

## Links
- [Site](http://morfy.org)
- [Forum](http://forum.morfy.org)
- [Documentation](http://morfy.org/documentation)
- [Github Repository](https://github.com/morfy-cms/morfy)

## License
See [LICENSE](https://github.com/morfy-cms/morfy/blob/master/LICENSE.md)
