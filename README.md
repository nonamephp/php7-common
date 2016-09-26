[![Build
Status](https://travis-ci.org/nonamephp/php7-common.svg?branch=master)](https://travis-ci.org/nonamephp/php7-common)

php7-common 
=============

A collection of common libraries for PHP 7

#### \Noname\Common\Collection

##### Methods

* `__construct(array $data)` Create Collection
* `set($key, $value)` Add an item
* `get($key, $default = null)` Get value of item; Returns $default if item doesn't exist
* `has($key) : bool` Check if item exists
* `is($key, $value, $operater = null)` Compare value of an item
* `pluck($key, $default = null)` Pluck an item from the collection; Returns $default if item doesn't exist
* `delete($key)` Delete an item
* `destroy()` Remove all items
* `count()` Get count of items
* `keys() : array` Get item keys
* `values() : array` Get item values
* `all() : array` Alias for `toArray()`
* `toArray() : array` Returns collection as an array

#### \Noname\Common\Validator

    <?php
    use \Noname\Common\Validator;
    $values = ['email' => 'john.doe@example.org'];
    $rules = ['email' => 'email'];
    $validator = new Validator($values, $rules);
    $valid = $validator->validate();
    if(!$valid){
        print_r($validator->getErrors());
    }

##### Methods

* `__construct(array $data, array $rules, array $settings = [])` Create Validator
    * $data : array
    * $rules : array
    * $settings : array
* `validate() : bool` Validate data based on supplied rules
* `hasErrors() : bool` Check if validator has errors
* `getErrors() : array` Return validation errors
* `validateType($type, $value, array $rule = []) : bool` Type-specific validator