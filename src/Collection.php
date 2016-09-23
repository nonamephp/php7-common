<?php declare(strict_types=1);
namespace Noname\Common;

use ArrayAccess;
use Countable;
use IteratorAggregate;

/**
 * Collection
 *
 * @package Noname\Common
 */
class Collection implements Countable, ArrayAccess, IteratorAggregate
{
	/**
	 * Array storage for collected items.
	 *
	 * @var array $items
	 */
	protected $items = [];

	/**
	 * @param array $items
	 */
	public function __construct(array $items = [])
	{
		$this->items = $items;
	}

	/**
	 * Adds an item to the collection.
	 *
	 * @param string $key
	 * @param mixed $value
	 */
	public function set($key, $value)
	{
		$this->items[$key] = $value;
	}

	/**
	 * Gets an item from the collection. Returns $default if item cannot be found.
	 *
	 * @param string $key
	 * @param mixed $default
	 * @return mixed Will return $default if cannot find item
	 */
	public function get($key, $default = null)
	{
		if(isset($this->items[$key])){
			return $this->items[$key];
		}
		return $default;
	}

	/**
	 * Gets and deletes an item from the collection.
	 *
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
	 */
	public function pluck($key, $default = null)
	{
		$value = $this->get($key, $default);

		$this->delete($key);

		return $value;
	}

	/**
	 * Gets all of the items in the collection as an array.
	 *
	 * @return array
	 */
	public function all() : array
	{
		return $this->toArray();
	}

	/**
	 * Gets keys for all of the items in the collection.
	 *
	 * @return array
	 */
	public function keys() : array
	{
		return array_keys($this->items);
	}

	/**
	 * Gets values for all of the items in the collection.
	 *
	 * @return array
	 */
	public function values() : array
	{
		return array_values($this->items);
	}

	/**
	 * Checks for existence of an item in the collection.
	 *
	 * @param string $key
	 * @return boolean
	 */
	public function has($key) : bool
	{
		return isset($this->items[$key]);
	}

	/**
	 * Compares item value with $value, with an optional $operator.
	 *
	 * @param string $key
	 * @param mixed $value
	 * @param mixed $operator Supported values: null, =, ==, ===, >, >=, <, <=, <>
	 * @return boolean
	 * @throws \InvalidArgumentException
	 */
	public function is($key, $value, $operator = null) : bool
	{
		$keyValue = $this->get($key);

		if(in_array($operator, [null, '=', '=='])){
			return $keyValue == $value;
		}elseif($operator == '==='){ // strict
			return $keyValue === $value;
		}elseif($operator == '>'){
			return $keyValue > $value;
		}elseif($operator == '>='){
			return $keyValue >= $value;
		}elseif($operator == '<'){
			return $keyValue < $value;
		}elseif($operator == '<='){
			return $keyValue <= $value;
		}elseif(in_array($operator, ['!=', '<>'])){
			return $keyValue != $value;
		}else{
			throw new \InvalidArgumentException('Invalid value supplied for $operator');
		}
	}

	/**
	 * Deletes an item from the collection.
	 *
	 * @param string $key
	 */
	public function delete($key)
	{
		unset($this->items[$key]);
	}

	/**
	 * Destroys the collection by deleting all of the items.
	 */
	public function destroy()
	{
		$this->items = [];
	}

	/**
	 * Gets collection as an array.
	 *
	 * @return array
	 */
	public function toArray() : array
	{
		return $this->items;
	}

	///////////////////////////////////
	// IteratorAggregate Methods

	public function getIterator()
	{
		foreach($this->items as $key => $value){
			yield $key => $value;
		}
	}

	///////////////////////////////////
	// Countable Methods

	public function count()
	{
		return count($this->items);
	}

	///////////////////////////////////
	// Array Access Methods

	public function offsetExists($offset)
	{
		return $this->has($offset);
	}

	public function offsetGet($offset)
	{
		return $this->get($offset);
	}

	public function offsetSet($offset, $value)
	{
		return $this->set($offset, $value);
	}

	public function offsetUnset($offset)
	{
		return $this->delete($offset);
	}
}