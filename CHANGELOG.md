# v2.0.3, 2016-01-20
* Project Rebranded!
* Added new favicon
* License updated
* Fixed Blocks bug with empty $blocks

# v2.0.2, 2015-12-08
* Added BOWER for Default Theme
* Added ability to access Current Page Template with public static method getCurrentTemplate()
* Updated Twitter Bootstrap to 3.3.6 for Default Theme
* Updated Doctrine Cache to 1.5.2
* Updated .gitignore
* Fixed Default Themes styles

# v2.0.1, 2015-12-03
* Fixed robots.txt
* Removed LIBRARIES_PATH constant
* Updated .gitignore

# v2.0.0, 2015-11-20
* Improved Fansoro Architecture.
* Improved Fansoro Security.
* Improved Default Fansoro Theme.
* Improved Fansoro Configurations System.
* Added Fansoro Smart Cache based on Doctrine Cache.
* Added Minimum PHP version support is 5.5
* Added Composer Support.
* Added Fansoro /boot/ directory with defines.php, shortcodes.php, actions.php filters.php
* Added New Classes: Action, Filter, Blocks, Cache, Config, Pages, Template, Yaml, Plugins.
* Added New Config file: system.yml
* Added New Pages::getCurrentPage() and Pages::updateCurrentPage() methods.
* Added new Pages::display() method.
* Added New Pages actions `before_page_rendered` and `after_page_rendered`
* Added Shortcode and Markdown parsers as a content filters.
* Added Fenom Storage to store data in Fenom.
* Added Output buffering.
* Added ability to configure of display errors. Default value is false - for production.
* Added and used Composer Autoloader instead of Force Autoloader.
* Removed BLOCKS_PATH and PAGES_PATH constants.
* Added .gitignore, composer.json and .gitkeep instead of .empty
* Removed constants: site, fenom, theme, page, plugins and actions from Fansoro Class.
* Removed force, fenom, parsedown and spyc from libraries directory.
* Removed Fenom Config file fenom.yml
* Removed Actions before_render and after_render
* Removed Fansoro Installer.

# v1.1.4, 2015-11-18
* Fixed Fansoro Installer
* Fixed Force Libraries

# v1.1.3, 2015-10-25
* Default Theme: change layout.tpl to base.tpl
* Fixed welcome page bug. Change content to storage
* Fixed Prevent visitors from viewing yml, yaml files directly.

# v1.1.2, 2015-10-19
* Fixed Bug in {site_url} shortcode

# v1.1.1, 2015-10-18
* Added Force Shortcode class
* Added STORAGE_PATH constant
* Added `storage` folder
* Updated Force Components 1.0.1
* Updated Fansoro Class with static methods.
* Removed Fansoro `factory()->` and chaining.
* Removed `content` folder
* Removed CONTENT_PATH constant

# v1.1.0, 2015-10-16
* Added Fansoro MIT LICENSE instead of GNU GPL v3
* Added Fenom Template Engine
* Added Force Components (Arr, ClassLoader, FileSystem, Http, Session, Token, Url)
* Added Parsedown Lib for parsing Markdown files.
* Added SPYC Lib for parsing YAML configurations.
* Added new folders /cache/, /content/pages/ and /content/blocks/
* Added ability to display PAGE BLOCKS. {block=name} and Fansoro::factory()->getBlock('name');
* Added new constants PAGES_PATH BLOCKS_PATH CACHE_PATH
* Added new Fansoro and Fenom configuration files site.yml and fenom.yml
* Added new configuration file for Fansoro default theme: default.yml
* Added new Fansoro public method loadPageTemplate() - load page template.
* Added new Fansoro public method parsedown() - to execute parsedown parser.
* Added new Fansoro public method getBlock() - to get page block.
* Added new Fansoro public static variables $site, $fenom and $theme
* Added ability to use custom variables for page header in valid YAML format.
* Added new page header format. Between triple-dashed lines is page header variables.
* Added add ability to load plugins configuration and disable or enable plugins.
* Added <!--more--> for creating page summary text.
* Added Fansoro Favicon: favicon.ico
* Added robots.txt
* Removed Fansoro configuration file Fansoro.php
* Removed PHP Tag {php}{/php} from content parser for security reasons.
* Removed {cut}(use <!--more--> instead) also {fansoro_separator} and {fansoro_version} content tags.
* Removed Fansoro private variable $page_headers. Because now you are free to set your own.
* Removed Fansoro protected variable $security_token_name (its part of Force Token Class)
* Removed Fansoro public static variable $config (use $site and $fenom instead)
* Removed Fansoro constant SEPARATOR
* Removed Fansoro methods obEval() evalPHP() cleanString()
* Removed Fansoro method subvalSort() (its part of Force Arr Class)
* Removed Fansoro methods checkToken() generateToken() (they are part of Force Token Class)
* Removed Fansoro method getFiles() (its part of Force FileSystem/File Class)
* Removed Fansoro methods runSanitizeURL() sanitizeURL() getUriSegment() getUriSegments() getUrl() (they are part of Force Http/Response and Http/Request Classes)
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
* Fansoro fixes
* Default Theme fixes

# v1.0.3, 2014-01-24
* New method generateToken() - Generate and store a unique token which can be used to help prevent CSRF attacks.
* New method checkToken() - Check that the given token matches the currently stored security token.
* New method cleanString() - Sanitize data to prevent XSS (Cross-site scripting)
* Default Theme - Improvements

# v1.0.2, 2014-01-21
* Fansoro Filters - Closure support added.
* Default Theme - Fixes

# v1.0.1, 2014-01-19
* Default Theme - Improvements

# v1.0.0, 2014-01-18
* Initial release
