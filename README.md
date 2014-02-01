# Morfy CMS
Simple and fast file-based CMS

## System Requirements
Operation system: Unix, Linux, Windows, Mac OS   
Middleware: PHP 5.3.0 or higher with PHP's [Multibyte String module](http://php.net/mbstring)   
Webserver: Apache with [Mod Rewrite](http://httpd.apache.org/docs/current/mod/mod_rewrite.html) or Ngnix with [Rewrite Module](http://wiki.nginx.org/HttpRewriteModule)   

## Steps to Install
1. [Download the latest version.](http://morfy.monstra.org/download)
2. Unzip the contents to a new folder on your local computer.
3. Upload that whole folder with an FTP client to your host.
4. You may also need to recursively CHMOD the folder /content/, /themes/ to 755(or 777) if your host doesn't set it implicitly.
5. Also you may also need to recursively CHMOD the /install.php, /.htaccess to 755(or 777) if your host doesn't set it implicitly.
6. Type http://example.org/install.php in the browser.

## Links
- [Site](http://morfy.monstra.org)
- [Github Repository](https://github.com/Awilum/morfy-cms)

Copyright (C) 2014 Romanenko Sergey / Awilum [awilum@msn.com]
