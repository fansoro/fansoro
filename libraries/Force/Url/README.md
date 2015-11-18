# Url
The url class allows you to interact with the URLs.  

### Gets the base URL
```php
$url_base = Url::getBase();
```

### Gets current URL
```php
$url_current = Url::getCurrent();
```

### Get Uri String
```php
$uri_string = Url::getUriString();
```

### Get Uri Segments
```php
$uri_segments = Url::getUriSegments();
```

### Get Uri Segment
```php
$uri_segment = Url::getUriSegment(0);
```

### Create safe url
```php
$safe_url = Url::sanitizeURL($url);
```

### Sanitize URL to prevent XSS - Cross-site scripting
```php
Url::runSanitizeURL();
```

## License
See [LICENSE](https://github.com/force-components/Url/blob/master/LICENSE)
