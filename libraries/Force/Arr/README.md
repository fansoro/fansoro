# Arr
The array class contains methods that can be useful when working with arrays.

Subval sort
```php
$new_array = Arr::subvalSort($old_array, 'sort');
```

Sets an array value using "dot notation".
```php
Arr::set($array, 'foo.bar', 'value');
```

Return value from array using "dot notation".  
If the key does not exist in the array, the default value will be returned instead.
```php
$login = Arr::get($_POST, 'login');  

$array = array('foo' => 'bar');  
$foo = Arr::get($array, 'foo');  

$array = array('test' => array('foo' => 'bar'));  
$foo = Arr::get($array, 'test.foo');
```

Delete an array value using "dot notation".
```php
Arr::delete($array, 'foo.bar');
```

Checks if the given dot-notated key exists in the array.
```php  
if (Arr::keyExists($array, 'foo.bar')) {
    // Do something...
}
```

Returns a random value from an array.
```php
$random = Arr::random(array('php', 'js', 'css', 'html'));
```

Returns TRUE if the array is associative and FALSE if not.
```php
if (Arr::isAssoc($array)) {
    // Do something...
}
```


## License
See [LICENSE](https://github.com/force-components/Arr/blob/master/LICENSE)
