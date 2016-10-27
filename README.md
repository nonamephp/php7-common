[![Build
Status](https://travis-ci.org/nonamephp/php7-common.svg?branch=master)](https://travis-ci.org/nonamephp/php7-common)

php7-common 
=============

_This project is in development and is not recommended for use in production environments_

A collection of common libraries for PHP 7.

## What's in the box?

### `\Noname\Common\Collection`

Create a `Collection` with an associative array to provide helpful methods for working with your data.

`Collection` implements the following interfaces: `Countable`, `ArrayAccess`, `IteratorAggregate`, `Serializable`, `JsonSerializable`

#### Collection Methods

##### `__construct(array $items = [])`

Create instance of `Collection`.  

##### `set(string $key, mixed $value)` 

Add an item to the collection. If `$key` already exists in the collection it will be overwritten.

##### `get(string $key, mixed $default = null)`

Return the value of an item from the collection. If `$key` doesn't exist in the collection then `$default` will be returned.

##### `has(string $key) : bool` 

Check if the collection has an item with same `$key`.

##### `is(string $key, mixed $value, mixed $operater = null)` 

Compare an item's value against `$value`. By default, the method will check if the item's value is equal to `$value`. 
Optionally, you may supply an `$operator` to change the comparison logic.

Supported `$operator` values: `=`, `==`, `===`, `>`, `>=`, `<`, `<=`, `<>`, `!=`

##### `pluck(string $key, mixed $default = null)` 

Pluck an item from the collection. If `$key` doesn't exist in the collection then `$default` will be returned.

##### `delete(string $key)` 

Remove an item from the collection.

##### `destroy()` 

Delete all items in the collection.

##### `count()` 

Returns the count of all of the items in the collection.

##### `keys() : array`

Returns an array containing keys for all of the items in the collection.

###### `values() : array`

Returns an array containing values for all of the items in the collection.

###### `all() : array` 

Alias for `toArray()`.

###### `toArray() : array` 

Returns all items in the collection as an associative array.

### \Noname\Common\Validator

Use `Validator` to validate your data based on a set of rules.

##### Basic Example

    <?php
    use Noname\Common\Validator;
    
    // $data must be an associative array of user input
    $data = [
        'customer_id'    => 100,
        'customer_email' => 'john.doe@example.org'
    ];
    
    // Define a rule for each field you want to validate
    $rules = [
        'customer_id'    => 'int',  // customer_id MUST be an integer
        'customer_email' => 'email' // customer_email MUST be an email address
    ];
    
    // Create Validator
    $validator = new Validator($data, $rules);
    
    // Validate the data based on the rules
    if(!$validator->validate()){
        // getErrors() will return an array of validation errors
        $errors = $validator->getErrors();
        // handle errors
    }
    
#### Built-in Validation Types

* `null` Validate that value is null
* `bool`, `boolean` Validate that value is boolean
* `scalar` Validate that value is scalar (integer, float, string or boolean)
* `str`, `string` Validate that value is string
* `num`, `numeric` Validate that value is numeric
* `int`, `integer` Validate that value is integer
* `float`, `double` Validate that value is float/double
* `alnum`, `alphanumeric` Validate that value is alpha-numeric only
* `alpha` Validate that value is alpha only
* `arr`, `array` Validate that value is array
* `obj`, `object` Validate that value is object
* `closure` Validate that value is instance of `\Closure`
* `callable` Validate that value is callable
* `email` Validate that value is valid email address
* `ip` Validate that value is either of IPv4 or IPv6
* `ipv4` Validate that value is IPv4
* `ipv6` Validate that value is IPv6

#### Validator Methods

##### `__construct(array $values, array $rules)`

Create instance of `Validator`.

##### `validate() : bool` 

Validate the data based on the rules.

##### `hasErrors() : bool`

Checks if validation has any errors.

##### `getErrors() : array`

Returns an array of validation errors. 


###### `static is($type, $value) : bool`

Static method to check if `$value` is valid `$type`. You can pass any of the built-in validator types for `$type`.

    <?php
    use Noname\Common\Validator;
    
    Validator::is('string', 'Hello world!');
    Validator::is('integer', 100);

##### `static is{Type}($value)`

Similar to `Validator:is()`, except type is passed in the method name.

    <?php
    use Noname\Common\Validator;
    
    Validator::isString('Hello world!');
    Validator::isInteger(100);
    
It's important to note that the type in the method name MUST start with an uppercased letter.

To provide a quick example:

    <?php
    use Noname\Common\Validator;
    
    // This is not valid because 'string' starts with lowercased letter.
    Validator::isstring('Hello world!');
    
    // This is valid because 'String' starts with uppercased letter.
    Validator::isString('Hello world!');