# Shortcode

The Shortcode Class is a simple regex based parser that allows you to replace simple bbcode-like tags within a HTMLText or HTMLVarchar field when rendered into a content.   

Examples of shortcode tags:  

```php
{{shortcode}}
{{shortcode parameter="value"}}
```

Example of escaping shortcodes:  
```php
{{{shortcode}}}
```

### Add new shortcode

Your shorcode function:  
```php
function returnSiteUrl() {
   return 'http://example.org';
}
```

Add shortcode  
```php
Shortcode::add('site_url', 'returnSiteUrl');
```

### Add new shortcode with Variables
Your shorcode function:  
```php
function foo($attributes) {
    // Extract attributes
    extract($attributes);

    // text
    if (isset($text)) $text = $text; else $text = '';

    // return
    return $text;
}
```

Add shortcode {foo text="Hello World"}   
```php
Shortcode::add('foo', 'foo');
```
Usage:  
```php
{foo text="Hello World"}
```
Result:  
```
Hello World
```

### Add new shortcode with Variables and Content

Your shorcode function:  
```php
function foo($attributes, $content) {
    // Extract attributes
    extract($attributes);

    // text
    if (isset($color)) $color = $color; else $color = 'black';

    // return
    return '<span style="color:'.$color.'">'.$content.'</span>';
}
```

Add shortcode {foo color="red"}  
```php
Shortcode::add('foo', 'foo');
```

Usage:  
```php
{foo color="red"}Hello World{/foo}
```

Result:  
```html
<span style="color: red">Hello World</span>  
```

### Check if a shortcode has been registered.
```php
if (Shortcode::exists('foo')) {
    // do something...
}
```

### Remove a specific registered shortcode.
```php
Shortcode::delete('foo');
```

### Remove all registered shortcodes.
```php
Shortcode::clear();
```

## Braces
The shortcode parser does not accept braces within attributes. Thus the following will fail:   
```php
{foo attribute="{Some value}"}Hello World{/foo}
```

## License
See [LICENSE](https://github.com/force-components/Shortcode/blob/master/LICENSE)
