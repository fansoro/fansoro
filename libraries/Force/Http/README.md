# Http
Response and Request class


## Response

### Set header status
```
Response::status(404);
```

## Request

### Redirects the browser to a page specified by the $url argument.
```
Request::redirect('test');
```

### Set one or multiple headers.
```
Request::setHeaders('Location: http://site.com/');
```

### Get
```
$action = Request::get('action');
```

### Post
```
$login = Request::post('login');
```

### Returns whether this is an ajax request or not
```
if (Request::isAjax()) {
  // do something...
}
```

### Terminate request
```
Request::shutdown();
```

## License
See [LICENSE](https://github.com/force-components/Http/blob/master/LICENSE)
