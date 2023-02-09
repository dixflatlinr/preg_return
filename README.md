# preg_return
~ is a convenience PHP library which provides an alternate way to use preg_match. It works exactly like **preg_match**, but returns the matching elements or a subset of them.

# Installation
The recommended way to install **preg_return** is through  [Composer](https://getcomposer.org/).
```bash
composer require dixflatlinr/preg_return
```

## Syntax & usage
The package registers global functions as a convenience, so it can be used such:

```php
preg_return(string $pattern, string $subject, $indexesToReturn = null, int $flags = 0, int $offset = 0)
preg_return_all(string $pattern, string $subject, $indexesToReturn = null, int $flags = 0, int $offset = 0)
preg_return_replace(string $pattern, string $replacement, string &$subject, $indexesToReturn = null, int $flags = 0, int $offset = 0)
```
Or calling it directly:
```php
RX::pregReturn(string $pattern, string $subject, $indexesToReturn = null, int $flags = 0, int $offset = 0)
RX::pregReturnAll(string $pattern, string $subject, $indexesToReturn = null, int $flags = 0, int $offset = 0)
RX::pregReturnReplace(string $pattern, string $replacement, string &$subject, $indexesToReturn = null, int $flags = 0, int $offset = 0)
```
The only difference compared to
```php
preg_match(string $pattern, string $subject, array &$matches = null, int $flags = 0, int $offset = 0): int|false
```
is that instead of the ```&$matches``` parameter, now ```$indexesToReturn``` governs what is returned and how.

## Examples
```php
<?
use DF\App\Helper\RX;
require "vendor/autoload.php";

//$indexesToReturn === null, gives back the preg_match return value
preg_return('~(a)(?<namedindex>b)~is','ab'); //=> 1;
RX::pregReturn('~(a)(?<namedindex>b)~is','ab'); //=> 1;
```

```php
//Using an empty array or string returns the whole results array
preg_return('~(a)(?<namedindex>b)~is','ab', []);
/* =>
array(4) {
  [0]=>
  string(2) "ab"
  [1]=>
  string(1) "a"
  ["namedindex"]=>
  string(1) "b"
  [2]=>
  string(1) "b"
}
*/
```

```php
//=== 0, gives back the text that matched the full pattern
preg_return('~(a)(?<namedindex>b)~is','ab', 0); //=> 'ab'
```

```php
//=== 1, gives back text that matched the first captured parenthesized subpattern
preg_return('~(a)(?<namedindex>b)~is','ab', 1); //=> 'a'
```

```php
//=== 'namedindex', gives back text that matched the first named group
preg_return('~(a)(?<namedindex>b)~is','ab', 'namedindex'); //=> 'b'
```

```php
//Using an array with indexes or named groups returns those keyed accordingly
preg_return('~(a)(?<namedindex>b)~is','ab', [1,'namedindex']);//=> [1 => 'a', 'namedindex' => b]
```

```php
//Using preg_return_replace modifies the provided subject variable and returns the matched element before replacement
$subject = 'first_second_third';
$r = preg_return_replace('~^(first)_(second)_(?<third>third)$~is', '\1_\2_XXX', $subject, 3);
/*
$r = "third";
$subject = "first_second_XXX";
*/

```
