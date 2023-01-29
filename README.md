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
### Basics
Basic use about the same as array but **with some limitations described below**.

Some [useful functions](#useful-functions) also available in the section below

Examples
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

//this will work
foreach ($arr as $key => $val) {
    echo "$key: $val\n";
}
```
### Limitations
- It will throw `SKArrayException` if you try to use null or empty string as a key. This mean `$stringKeyArray[] = 'value';` is illegal
- By default it also will throw `SKArrayException` if you try to read an unknown key. Use `$a = new SKArray(false)` to change behaviour from exception to PHP notice and return null. Or, even better, use `$var = $stringKeyArray[] ?? null;` - this works fine.
- Indirect midification like `$stringKeyArray['Level1Key']['Level2key'] = 'value';` will not work:
  - Will throw Exception/Notice for unknown key if the first key is not exist
  - If the key exist - no error **but also no effect and PHP notice**. Data will not change.

```
<?php

use SKArray\SKArray;
use SKArray\SKArrayException;

$arr =  new SKArray();

//This will work fine
$var = $arr['UnknownnKey'] ?? null;

//This will throw exception
try {
    var = $arr['Unknownn'];
} catch (SKArrayException $ex) {
    echo "Check your key!\n";
}

//This will not throw, only PHP notice and null return
$arr2 = new SKArray(false);
$var = $arr2['Unknow'];


//This will throw `SKArrayException` as the key is not known
$a = new SKArray();
$a['Level1']['Level2'] = 'data';

//This will NOT throw exception, but also it will not change contained array element
$a['Level1'] = [];
$a['Level1']['Level2'] = 'data';
// Result: $a['Level1'] == [], element not modified, PHP notice generated
```
## Useful functions
### `keys()` aka `array_keys()`
Call of `$stringKeyArray->keys()` or it's synonym `array_keys()` returns keys as array of strings, same as PHP analogue.
### `values()` aka `array_values()`
Call of `$stringKeyArray->values()` or it's synonym `array_values()` returns values as array of mixed, same as PHP analuue.

## Testing
Was tested with PHPUnit under PHP 7.2. The cod is very simple so expected to work 7.x and up.

If you have doubts about your eenvironment, install it with `--dev` composer optiion and then run `composer test` from library source root.
