# Fansoro
[![Join the chat at https://gitter.im/fansoro/fansoro](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/fansoro/fansoro?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

Fansoro is Modern Open Source Flat-File Content Management System.  
Content in Fansoro is just a simple files written with markdown syntax in pages folder.   
You simply create markdown files in the pages folder and that becomes a page.

## Requirements
PHP 5.5 or higher with PHP's [Multibyte String module](http://php.net/mbstring)   
Apache with [Mod Rewrite](http://httpd.apache.org/docs/current/mod/mod_rewrite.html)  

## Installation

#### Using (S)FTP

[Download the latest version.](http://fansoro.org/download)  

Unzip the contents to a new folder on your local computer, and upload to your webhost using the (S)FTP client of your choice. After you’ve done this, be sure to chmod the following directories (with containing files) to 777, so they are readable and writable by Fansoro:  
* `cache/`
* `config/`
* `storage/`
* `themes/`
* `plugins/`

#### Using Composer

You can easily install Fansoro with Composer.

```
composer create-project fansoro/fansoro
```

## Contributing
1. Help on the [Forum.](http://forum.fansoro.org)
2. Develop a new plugin.
3. Create a new theme.
4. Find and [report issues.](https://github.com/fansoro/fansoro/issues)
5. Link back to [Fansoro](http://fansoro.org).

## Links
- [Site](http://fansoro.org)
- [Forum](http://forum.fansoro.org)
- [Documentation](http://fansoro.org/documentation)
- [Github Repository](https://github.com/fansoro/fansoro)

## License
See [LICENSE](https://github.com/fansoro/fansoro/blob/master/LICENSE.md)
