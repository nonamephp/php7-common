<?php declare(strict_types=1);
namespace Noname\Common;

class ValidatorTest extends \PHPUnit_Framework_TestCase
{
	///////////////////////////////////
	// Tests

	public function testValidateNull()
	{
		$valid = [
			'key1' => null,
		];

		$invalid = [
			'key1' => true,
			'key2' => false,
			'key3' => 100,
			'key4' => new \stdClass,
			'key5' => [],
			'key6' => 'Hello, World!'
		];

		$rules = [
			'key1' => 'null',
			'key2' => 'null',
			'key3' => 'null',
			'key4' => 'null',
			'key5' => 'null',
			'key6' => 'null',
		];

		// Validate valid data
		$validator1 = new Validator($valid, $rules);
		$this->assertTrue($validator1->validate());

		// Validate invalid data
		$validator2 = new Validator($invalid, $rules);
		$this->assertFalse($validator2->validate());
	}

	public function testValidateBoolean()
	{
		$valid = [
			'key1' => true,
			'key2' => false
		];

		$invalid = [
			'key1' => 'true',
			'key2' => 'false',
			'key3' => 1,
			'key4' => 'Hello, World!',
			'key5' => 0
		];

		$rules = [
			'key1' => 'bool',
			'key2' => 'bool',
			'key3' => 'bool',
			'key4' => 'bool'
		];

		// Validate valid data
		$validator1 = new Validator($valid, $rules);
		$this->assertTrue($validator1->validate());

		// Validate invalid data
		$validator2 = new Validator($invalid, $rules);
		$this->assertFalse($validator2->validate());
	}

	/**
	 * @covers Validator::validateScalar
	 */
	public function testValidateScalar()
	{
		$valid = [
			'key1' => true,
			'key2' => false,
			'key3' => 1,
			'key4' => 0,
			'key5' => 'Hello, World!',
			'key6' => 1.0
		];

		$invalid = [
			'key1' => ['foo' => 'bar'],
			'key2' => new \stdClass
		];

		$rules = [
			'key1' => 'scalar',
			'key2' => 'scalar',
			'key3' => 'scalar',
			'key4' => 'scalar',
			'key5' => 'scalar',
			'key6' => 'scalar'
		];

		// Validate valid data
		$validator1 = new Validator($valid, $rules);
		$this->assertTrue($validator1->validate());

		// Validate invalid data
		$validator2 = new Validator($invalid, $rules);
		$this->assertFalse($validator2->validate());
	}

	/**
	 * @covers Validator::validateString
	 */
	public function testValidateString()
	{
		$valid = [
			'key1' => 'Hello, World!',
			'key2' => '',
			'key3' => '100',
			'key4' => json_encode(['foo' => 'bar'])
		];

		$invalid = [
			'key1' => true,
			'key2' => null,
			'key3' => 100,
			'key4' => new \stdClass
		];

		$rules = [
			'key1' => 'string',
			'key2' => 'string',
			'key3' => 'string',
			'key4' => 'string'
		];

		// Validate valid data
		$validator1 = new Validator($valid, $rules);
		$this->assertTrue($validator1->validate());

		// Validate invalid data
		$validator2 = new Validator($invalid, $rules);
		$this->assertFalse($validator2->validate());
	}

	/**
	 * @covers Validator::validateInteger
	 */
	public function testValidateInteger()
	{
		$valid = [
			'key1' => 1,
			'key2' => 0,
			'key3' => -1,
		];

		$invalid = [
			'key1' => '1',
			'key2' => 1.0,
			'key3' => true,
		];

		$rules = [
			'key1' => 'int',
			'key2' => 'int',
			'key3' => 'int',
		];

		// Validate valid data
		$validator1 = new Validator($valid, $rules);
		$this->assertTrue($validator1->validate());

		// Validate invalid data
		$validator2 = new Validator($invalid, $rules);
		$this->assertFalse($validator2->validate());
	}

	/**
	 * @covers Validator::validateNumeric
	 */
	public function testValidateNumeric()
	{
		$valid = [
			'key1' => 1,
			'key2' => '1',
			'key3' => 1.0,
		];

		$invalid = [
			'key1' => true,
			'key2' => 'Hello, World!',
			'key3' => ['foo' => 'bar']
		];

		$rules = [
			'key1' => 'num',
			'key2' => 'num',
			'key3' => 'num',
		];

		// Validate valid data
		$validator1 = new Validator($valid, $rules);
		$this->assertTrue($validator1->validate());

		// Validate invalid data
		$validator2 = new Validator($invalid, $rules);
		$this->assertFalse($validator2->validate());
	}

	/**
	 * @covers Validator::validateFloat
	 */
	public function testValidateFloat()
	{
		$valid = [
			'key1' => 1.0,
			'key2' => 1e7, // Scientific notation
		];

		$invalid = [
			'key1' => 1,
			'key2' => '1.0'
		];

		$rules = [
			'key1' => 'float',
			'key2' => 'float',
		];

		// Validate valid data
		$validator1 = new Validator($valid, $rules);
		$this->assertTrue($validator1->validate());

		// Validate invalid data
		$validator2 = new Validator($invalid, $rules);
		$this->assertFalse($validator2->validate());
	}

	/**
	 * @covers Validator::validateAlphaNumeric
	 */
	public function testValidateAlphaNumeric()
	{
		$valid = [
			'key1' => 'abc123',
			'key2' => '123abc'
		];

		$invalid = [
			'key1' => 'abc',
			'key2' => '123',
			'key3' => 123,
			'key4' => 'abc 123',
		];

		$rules = [
			'key1' => 'alnum',
			'key2' => 'alnum',
			'key3' => 'alnum',
			'key4' => 'alnum',
		];

		// Validate valid data
		$validator1 = new Validator($valid, $rules);
		$this->assertTrue($validator1->validate());

		// Validate invalid data
		$validator2 = new Validator($invalid, $rules);
		$this->assertFalse($validator2->validate());
	}

	/**
	 * @covers Validator::validateAlpha
	 */
	public function testValidateAlpha()
	{
		// @todo
	}

	/**
	 * @covers Validator::validateArray
	 */
	public function testValidateArray()
	{
		// @todo
	}

	/**
	 * @covers Validator::validateObject
	 */
	public function testValidateObject()
	{
		// @todo
	}

	/**
	 * @covers Validator::validateClass
	 */
	public function testValidateClass()
	{
		// @todo
	}

	/**
	 * @covers Validator::validateClosure
	 */
	public function testValidateClosure()
	{
		// @todo
	}

	/**
	 * @covers Validator::validateCallable
	 */
	public function testValidateCallable()
	{
		// @todo
	}

	/**
	 * @covers Validator::validateEmail
	 */
	public function testValidateEmail()
	{
		// @todo
	}

	/**
	 * @covers Validator::validateDate
	 */
	public function testValidateDate()
	{
		// @todo
	}

	/**
	 * @covers Validator::validateIP, Validator::validateIPv4, Validator::validateIPv6
	 */
	public function testValidateIP()
	{
		// @todo
	}
}