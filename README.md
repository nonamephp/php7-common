\Noname\Common
=============

A collection of common libraries for PHP 7

#### Collection

Basic usage example. 

    <?php
    $data = ['customer_id' => 100, 'email' => 'john.doe@example.org'];
    $collection = new \Noname\Common\Collection($data);
    $customer_id = $collection->get('customer_id');  // @return 100
    $email = $collection->get('email'); // @return john.doe@example.org

##### Methods

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