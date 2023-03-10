# Array-like class with only strings used as keys
## Purpose
PHP arrays may be veery frustrating with it's mess in key string/integer and associated probems.

It was need to ensure the keys will work predictable as string, but the class still be used as a regular array with `$a['key']` syntax, `foreach` use and all other staff. So, this what it do!

## Installation
In the Composer storage. Just add proper require section:

    "require": {
        "pavlyuts/php-string-key-arrays": "*"
    }
    
I do not think there be a version compatibility problem as it is too simple thing and expected to be stable. However, I recommend to inspect [CHANGELOG.md](https://github.com/pavlyuts/php-string-key-arrays/blob/main/CHANGELOG.md) for changes.

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
### `merge()`
Merges another iterable into SKArray about the same way as array_merge(), but all kays handled as string type
### `column()`
 About the same like array_column but... a bit different!
```
public function column($column, ...$args): SKArray
```
it always returm SKArray class, containing the same string keys and result for each element's 'column' as:
- If the element is an array, it will try to retrive array value by key $column
- If the element is an object, then:
  - if property named $column exist, it will try to read it.
  - elseif method $column exist, it will try to call it unpacking all the next $args, like $element->$column(...$args)

The returned dataset will only include keys and values successfully retrieved, no nulls, no errors. If yo try to access protected method or property it will be silently passed without returning any result or any kind of problem indication. 

### `setSubarrayItem()`
Helper to work with SKArray elements type of array, do the same as illegal operaton `$stringKeyArray['Level1Key']['Level2key'] = 'value'`
```
public function setSubarrayItem($offset, $value, $key = null)
```
If the element of SKArray collection at `$offset` is not an array or does not exist it will overwrite/create it as an empty array.
Then, it will add to array, if key is given, it will add with `$key`, if null or missed - add as []
```
$arr = new SKArray();

//Equivalent of illegal $arr['level1Key'][] = 'value'
$arr->setSubarrayItem('level1Key', 'value');

//Equivalent of illegal $arr['level1Key']['level2Key'] = 'value'
$arr->setSubarrayItem('level1Key', 'value', 'level2Key');
``` 
## Testing
Was tested with PHPUnit 8.5 under PHP 7.2. The code is very simple so expected to work 7.x and up.

If you have doubts about your environment, install it with `--dev` composer optiion and then run `composer test` from library source root.
