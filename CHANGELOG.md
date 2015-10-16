# v1.1.0, 2015-10-16
* Added Morfy MIT LICENSE instead of GNU GPL v3
* Added Fenom Template Engine
* Added Force Components (Arr, ClassLoader, FileSystem, Http, Session, Token, Url)
* Added Parsedown Lib for parsing Markdown files.
* Added SPYC Lib for parsing YAML configurations.
* Added new folders /cache/, /content/pages/ and /content/blocks/
* Added ability to display PAGE BLOCKS. {block=name} and Morfy::factory()->getBlock('name');
* Added new constants PAGES_PATH BLOCKS_PATH CACHE_PATH
* Added new Morfy and Fenom configuration files site.yml and fenom.yml
* Added new configuration file for Morfy default theme: default.yml
* Added new Morfy public method loadPageTemplate() - load page template.
* Added new Morfy public method parsedown() - to execute parsedown parser.
* Added new Morfy public method getBlock() - to get page block.
* Added new Morfy public static variables $site, $fenom and $theme
* Added ability to use custom variables for page header in valid YAML format.
* Added new page header format. Between triple-dashed lines is page header variables.
* Added add ability to load plugins configuration and disable or enable plugins.
* Added <!--more--> for creating page summary text.
* Added Morfy Favicon: favicon.ico
* Added robots.txt
* Removed Morfy configuration file Morfy.php
* Removed PHP Tag {php}{/php} from content parser for security reasons.
* Removed {cut}(use <!--more--> instead) also {morfy_separator} and {morfy_version} content tags.
* Removed Morfy private variable $page_headers. Because now you are free to set your own.
* Removed Morfy protected variable $security_token_name (its part of Force Token Class)
* Removed Morfy public static variable $config (use $site and $fenom instead)
* Removed Morfy constant SEPARATOR
* Removed Morfy methods obEval() evalPHP() cleanString()
* Removed Morfy method subvalSort() (its part of Force Arr Class)
* Removed Morfy methods checkToken() generateToken() (they are part of Force Token Class)
* Removed Morfy method getFiles() (its part of Force FileSystem/File Class)
* Removed Morfy methods runSanitizeURL() sanitizeURL() getUriSegment() getUriSegments() getUrl() (they are part of Force Http/Response and Http/Request Classes)
* Removed Markdown plugin
* Removed Sitemap plugin

# v1.0.6, 2015-09-10
* Prevent Visitors from Viewing our MD and TXT Files
* Sitemap Plugin fixes
* Default Theme - update jQuery to v2.1.3
* Default Theme - update Twitter Bootstrap to v3.3.5
* Default Theme - remove IE9 Support
* Date format for blog posts and pages - Fixed

# v1.0.5, 2014-02-01
* Added {php}{/php} tags for inline php code
* Default Theme Fav Icon fixes
* Sitemap Plugin fixes

# v1.0.4, 2014-01-26
* Morfy fixes
* Default Theme fixes

# v1.0.3, 2014-01-24
* New method generateToken() - Generate and store a unique token which can be used to help prevent CSRF attacks.
* New method checkToken() - Check that the given token matches the currently stored security token.
* New method cleanString() - Sanitize data to prevent XSS (Cross-site scripting)
* Default Theme - Improvements

# v1.0.2, 2014-01-21
* Morfy Filters - Closure support added.
* Default Theme - Fixes

# v1.0.1, 2014-01-19
* Default Theme - Improvements

# v1.0.0, 2014-01-18
* Initial release
