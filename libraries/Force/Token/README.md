# Token
Token class

Generate and store a unique token which can be used to help prevent  
[CSRF](http://wikipedia.org/wiki/Cross_Site_Request_Forgery) attacks.  
```php
$token = Token::generate();
```

```html
<input type="hidden" name="csrf" value="<?php echo Token::generate(); ?>">
```

Check that the given token matches the currently stored security token.  
```php
if (Token::check($token)) {
    // Pass
}
```
