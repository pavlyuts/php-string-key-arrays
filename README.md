# Array-like class with only strings used as keys
## Purpose
PHP arrays may be veery frustrating with it's mess in key string/integer and associated probems.

It was need to ensure the keys will work predictable as string, but the class still be used as a regular array with `$a['key']` syntax, `foreach` use and all other staff. So, this what it do!

## Installation
In the Composer storage. Just add proper require section:

    "require": {
        "pavlyuts/php-string-key-arrays": "*"
    }
    
I do not think there be a version compatibility problem as it is too simple thing and expected to be stable

## Use
Create the instance and use it as usual array with some issues:
- It will throw `SKArrayException` if you try to use null or empty string as a key. This mean `$stringKeyArray[] = 'value';` is illegal
- By default it also will throw `SKArrayException` if you try to read unknown key. Use `$a = new SKArray(false)` to change behaviour from exception to PHP notice and return null. Or, even better, use `$var = $stringKeyArray[] ?? null;` - this works fine.

Some examples
```
<?php

use SKArray\SKArray;
use SKArray\SKArrayException;

$arr =  new SKArray();

$arr['StringKey'] = 'Value1';

// In standart array this key becomes int(123), but here it be kept string 
$arr['123'] = 'ValueString123';

// This will REWRITE last one becuse int key 123 becomes string key '123'!
$arr[123] = 'ValueInt123';

//this will work fine
foreach ($arr as $key => $val) {
    echo "$key: $val\n";
}

//This will work fine
$var = $arr['Unknownn'] ?? null;

//This will throw exception
try {
    var = $arr['Unknownn'];
} catch (SKArrayException $ex) {
    echo "Check your key!\n";
}

//This will not throw, only PHP notice and null return
$arr2 = new SKArray(false);
$var = $arr2['Unknow'];
```

## Testing
Was tested with PHPUnit under PHP 7.2. The cod is very simple so expected to work 7.x and up.

If you have doubts about your eenvironment, install it with `--dev` composer optiion and then run `composer test` from library source root.
