# FileSystem
File and Dir class contains methods that assist in working with files and directories.

## File

### Returns true if the File exists.
```php
if (File::exists('filename.txt')) {
  // Do something...
}
```

### Delete file
```php
File::delete('filename.txt');
```

### Rename file
```php
File::rename('filename1.txt', 'filename2.txt');
```

### Copy file
```php
File::copy('folder1/filename.txt', 'folder2/filename.txt');
```

### Get the File extension.
```php
echo File::ext('filename.txt');
```

### Get the File name
```php
echo File::name('filename.txt');
```

### Get list of files in directory recursive
```php
$files = File::scan('folder');
$files = File::scan('folder', 'txt');
$files = File::scan('folder', array('txt', 'log'));
$files = File::scan('folder', array('txt', 'log'), false);
```

### Fetch the content from a file or URL.
```php
echo File::getContent('filename.txt');
```

### Writes a string to a file.
```php
File::setContent('filename.txt', 'Content ...');
```

### Get time(in Unix timestamp) the file was last changed
```php
echo File::lastChange('filename.txt');
```

### Get last access time
```php
echo File::lastAccess('filename.txt');
```

### Returns the mime type of a file.
```php
echo File::mime('filename.txt');
```

### Forces a file to be downloaded.
```php
File::download('filename.txt');
```

### Display a file in the browser.
```php
File::display('filename.txt');
```

### Tests whether a file is writable for anyone.
```php
if (File::writable('filename.txt')) {
  // do something...
}
```

## Dir

### Creates a directory
```php
Dir::create('folder1');
```

### Checks if this directory exists.
```php
if (Dir::exists('folder1')) {
  // Do something...
}
```  

### Check dir permission
```php
$dir_perm = Dir::checkPerm('folder1');
```

### Delete directory
```php
Dir::delete('folder1');
```

### Get list of directories
```php
$dirs = Dir::scan('folders');
```

### Check if a directory is writable.
```php
if (Dir::writable('folder1')) {
  // Do something...
}
```

### Get directory size.
```php
echo Dir::size('folder1');
```

## License
See [LICENSE](https://github.com/force-components/FileSystem/blob/master/LICENSE)
