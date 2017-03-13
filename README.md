[![Build
Status](https://travis-ci.org/nonamephp/php7-common.svg?branch=master)](https://travis-ci.org/nonamephp/php7-common)

php7-common 
=============

A collection of common libraries for PHP 7.

## Installation

Use Composer to install `php7-common` into your project.

`composer require nonamephp/php7-common`

## Included Libraries

* `Noname\Common\Arr`
* `Noname\Common\Collection`
* `Noname\Common\Str`
* `Noname\Common\Validator`

### `\Noname\Common\Arr`

A helper library for working with arrays.

#### Arr Methods

##### `static flatten(array $array, string $separator = '.') : array`

Flatten an associative array using a custom separator. This method will use a dot (.) for `$separator` by defult.

##### `static dot(array $array) : array`

Alias for `Arr::flatten()` that always uses a dot (.) separator.

##### `static each(array $array, callable $callable) : array`

Recursively assign the callable's return value to each array item. Array keys are preserved.

```php
<?php
use Noname\Common\Arr;

$values = [1, 2, 3, 4, 5];

// @return [2, 4, 6, 8, 10]
$values_doubled = Arr::each($values, function ($value) {
    return $value * 2;
});
```

### `\Noname\Common\Collection`

Create a `Collection` with an associative array to provide helpful methods for working with your data.

`Collection` implements the following interfaces: `Countable`, `ArrayAccess`, `IteratorAggregate`, `Serializable`, `JsonSerializable`

#### Usage Example

```php
<?php
use Noname\Common\Collection;

$userData = [
    'user_id' => 100,
    'user_name' => 'John Doe',
    'user_email' => 'john.doe@example.org'
];

$collection = new Collection($userData);

// output: 'john.doe@example.org'
echo $collection->get('user_email');
```

#### Collection Methods

##### `__construct(array $items = []) : Collection`

Creates an instance of `Collection`. Optionally pass an associative array for `$items` to prefill the collection with items.

##### `static make(...$arrays) : Collection`

Make a collection from one or more arrays.

##### `set(string $key, mixed $value) : void` 

Add an item to the collection. If `$key` already exists in the collection it will be overwritten.

##### `get(string|array $key, mixed $default = null) : mixed`

Get an item from the collection. Returns `$default` if item not found.

Passing an array of item keys for the value of `$key` will result in multiple
items being returned as an array. Keys that are missing from the collection 
will be returned with a value of `$default`.

##### `has(string $key) : bool` 

Check if the collection has an item with same `$key`.

##### `is(string $key, mixed $value, mixed $operater = null) : bool` 

Compare an item's value against `$value`. By default, the method will check if the item's value is equal to `$value`. 
Optionally, you may supply an `$operator` to change the comparison logic.

Supported `$operator` values: `=`, `==`, `===`, `>`, `>=`, `<`, `<=`, `<>`, `!=`

Note: `=` and `==` are the same, but `===` is will perform a strict comparison. `<>` and `!=` are the same.

##### `pluck(string $key, mixed $default = null) : mixed` 

Pluck an item from the collection. If `$key` doesn't exist in the collection then `$default` will be returned.

##### `delete(string $key) : void` 

Remove an item from the collection.

##### `destroy() : void` 

Remove all items from the collection.

##### `count() : int` 

Returns the count of all of the items in the collection.

##### `keys() : array`

Returns an array containing the keys of all of the items in the collection.

##### `values() : array`

Returns an array containing the values of all of the items in the collection.

##### `all() : array` 

Alias for `toArray()`.

##### `toArray() : array` 

Returns an array of all of the items in the collection.

##### `toJson() : string` 

Returns collection as JSON.

##### `flatten() : array`

Flatten all of the items in the collection using dot (.) notation.

### `\Noname\Common\Str`

A helper library for working with strings.

#### Str Methods

##### `static startsWith(string $string, string $prefix, bool $caseSensitive = true) : bool`

Checks if string starts with given prefix. By default this method is case-sensitive.

##### `static endsWith(string $string, string $suffix, bool $caseSensitive = true) : bool`

Checks if string ends with given prefix. By default this method is case-sensitive.

##### `static equals(string $a, string $b, bool $caseSensitive = true) : bool`

Checks if two strings equal each other. By default this method is case-sensitive.

##### `static toArray(string $string) : array`

Splits a string into an array containing each character.

### \Noname\Common\Validator

Use `Validator` to validate your data based on a set of rules.

##### Usage Example

```php
<?php
use Noname\Common\Validator;

// Data to be validated
$data = [
    'customer_id' => 100,
    'customer_email' => 'john.doe@example.org'
];

// Validation rules
$rules = [
    'customer_id' => 'int',  // customer_id MUST be an integer
    'customer_email' => 'email' // customer_email MUST be an email address
];

// Create Validator
$validator = new Validator($data, $rules);

// Validate data using rules
// @return bool
$valid = $validator->validate();

if ($valid) {
    echo 'Data passed validation!';
} else {
    $errors = $validator->getErrors();
    print_r($errors);
}
```    
    
#### Built-in Validation Types

* `*`, `any` Always pass validation for any data type
* `null` Validate that value is null
* `bool`, `boolean` Validate that value is boolean
* `scalar` Validate that value is scalar (integer, float, string or boolean)
* `str`, `string` Validate that value is string
* `num`, `numeric` Validate that value is numeric
* `int`, `integer` Validate that value is integer
* `float`, `double` Validate that value is float/double
* `alnum`, `alphanumeric` Validate that value only contains alpha-numeric characters
* `alpha` Validate that value only contains alpha characters
* `arr`, `array` Validate that value is array
* `object` Validate that value is object
* `callable` Validate that value is callable
* `email` Validate that value is a valid email address
* `ip` Validate that value is either of IPv4 or IPv6
* `ipv4` Validate that value is IPv4
* `ipv6` Validate that value is IPv6
* `date`, `datetime` Validate that value is date/datetime

**Hint:** Adding `[]` to any type (e.g. `int[]`) will validate an array of values.

#### Validator Methods

##### `__construct(array $values, array $rules) : Validator`

Create an instance of `Validator`.

##### `addType(string $typeName, array $typeRule) : void` 

Add a custom validator type. The following example will add a type of `equals_2` which validates that the value is equal to `2` and will set an error otherwise.

```php
<?php
use Noname\Common\Validator;

// Data to be validated
$values = ['a' => 3];

// Validation rules
$rules = ['a' => ['type' => 'equals_2']];

// Create Validator
$validator = new Validator($values, $rules);

// Add custom 'equals_2' type
$validator->addType('equals_2', [
    'validator' => function ($value, $rule, $validator) {
        $valid = $value === 2;
        if (!$valid) {
            $validator->setError($rule['name'], 'Value does not equal 2');
        }
        return $valid;
    }
]);

// Validate data using rules
// @return bool
$valid = $validator->validate();

if ($valid) {
    echo 'Data passed validation!';
} else {
    $errors = $validator->getErrors();
    print_r($errors);
}
```

**Note:** Custom types must be added prior to calling `validate()` or an `InvalidArgumentException` will be thrown.

##### `addValue(string $name, mixed $value) : void` 

Add value to dataset that is to be validated.

##### `addValues(array $values) : void` 

Add multiple values to dataset that is to be validated.

##### `values() : array` 

Returns an array of the values that are set to be validated.

##### `addRule(string $name, mixed $rule) : void` 

Add a rule to the validator.

##### `addRules(array $rules) : void` 

Add multiple rules to the validator. 

##### `rules() : array` 

Returns an array of the rules that are set for validation.

##### `validate() : bool` 

Validate the set data based on the set rules.

##### `hasErrors() : bool`

Checks if any errors were set during validation.

##### `getErrors() : array`

Returns an array of the errors that were set during validation.

##### `static is(string $type, mixed $value) : bool`

Static method to check if `$value` is valid `$type`. You can pass any of the built-in validator types for `$type`.

This method is useful when validating a single value.

```php
<?php
use Noname\Common\Validator;

Validator::is('string', 'Hello world!'); // @return true
Validator::is('integer', 'Hello world!'); // @return false
```

##### `static is{Type}(mixed $value) : bool`

Similar to `is()`, except type is passed in the method name.

```php
<?php
use Noname\Common\Validator;

Validator::isString('Hello world!'); // @return true
Validator::isInteger('Hello world!'); // @return false
```
    
**Note:** These methods are case-sensitive. If you are having issues it is recommended that you use `is()` instead.
