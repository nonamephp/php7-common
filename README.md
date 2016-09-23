\Noname\Common
=============

A collection of common libraries for PHP 7

#### \Noname\Common\Collection

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