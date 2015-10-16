# Session
The session class.

Start the session.
```php
Session::start();
```

Delete one or more session variables.
```php
Session::delete('user');
```

Destroy the session.
```php
Session::destroy();
```

Check if a session variable exists.
```php
if (Session::exists('user')) {
    // Do something...
}
```

Get a variable that was stored in the session.
```php
echo Session::get('user');
```


Return the sessionID.
```php
echo Session::getSessionId();
```

Store a variable in the session.
```php
Session::set('user', 'Awilum');
```
