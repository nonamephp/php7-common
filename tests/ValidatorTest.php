<?php declare(strict_types=1);
namespace Noname\Common;

class ValidatorTest extends \PHPUnit_Framework_TestCase
{
    protected $resource;
    protected $nullValues;
    protected $booleanValues;
    protected $stringValues;
    protected $integerValues;
    protected $numericValues;
    protected $numericStringValues;
    protected $floatValues;
    protected $alphaNumericValues;
    protected $alphaValues;
    protected $arrayValues;
    protected $objectValues;
    protected $closureValues;
    protected $resourceValues;
    protected $scalarValues;
    protected $nonScalarValues;
    protected $emailValues;
    protected $ipv4Values;
    protected $ipv6Values;
    protected $ipValues;

    protected function setUp()
    {
        // Create resource/stream
        $this->resource = fopen(__DIR__ . '/fixtures/resource.txt', 'r');

        // Set values for each data type
        $this->nullValues = [null];
        $this->booleanValues = [true, false];
        $this->stringValues = ['Hello, World!'];
        $this->integerValues = [1, 0, -1];
        $this->numericValues = array_merge($this->integerValues, [0x539, 0b10100111001]);
        $this->numericStringValues = ['1.0', '1'];
        $this->floatValues = [1.0, 1e7];
        $this->alphaNumericValues = ['abc', '123', 'abc123'];
        $this->alphaValues = ['abc'];
        $this->arrayValues = [['foo' => 'bar'], [1, 2, 3], []];
        $this->objectValues = [new \stdClass, new class{}, (object)[]];
        $this->closureValues = [function(){}];
        $this->resourceValues = [$this->resource];
        $this->scalarValues = array_merge($this->booleanValues, $this->stringValues, $this->integerValues, $this->numericValues, $this->numericStringValues, $this->floatValues, $this->alphaNumericValues, $this->alphaValues);
        $this->nonScalarValues = array_merge($this->arrayValues, $this->objectValues, $this->closureValues, $this->resourceValues, $this->nullValues);
        $this->emailValues = ['john.doe@example.org', 'jane.doe+test@example.org'];
        $this->ipv4Values = ['127.0.0.1', '8.8.8.8'];
        $this->ipv6Values = ['::1', '2001:0db8:85a3:0000:0000:8a2e:0370:7334'];
        $this->ipValues = array_merge($this->ipv4Values, $this->ipv6Values);
    }

    protected function tearDown()
    {
        fclose($this->resource);
    }

    ///////////////////////////////////
	// Tests

	public function testValidateNull()
	{
        $validator = new Validator();

        foreach($this->nullValues as $value){
            $this->assertTrue($validator->validateType('null', $value));
        }

        // Grab all scalar and non-scalar values to test against
        $values = array_merge($this->scalarValues, $this->nonScalarValues);

        // Remove any strict null values (found in non-scalar values array)
        $nullKeys = array_keys($values, null, true);
        foreach($nullKeys as $index){
            unset($values[$index]);
        }
        foreach($values as $value){
            $this->assertFalse($validator->validateType('null', $value));
        }
	}

	public function testValidateBoolean()
	{
        $validator = new Validator();

        foreach($this->booleanValues as $value){
            $this->assertTrue($validator->validateType('bool', $value));
        }

        $nonBooleanValues = array_merge(array_diff($this->scalarValues, $this->booleanValues), $this->nonScalarValues);
        foreach($nonBooleanValues as $value){
            $this->assertFalse($validator->validateType('bool', $value));
        }
	}

	/**
	 * @covers Validator::validateScalar
	 */
	public function testValidateScalar()
	{
        $validator = new Validator();

        foreach($this->scalarValues as $value){
            $this->assertTrue($validator->validateType('scalar', $value));
        }

        foreach($this->nonScalarValues as $value){
            $this->assertFalse($validator->validateType('scalar', $value));
        }
	}

	/**
	 * @covers Validator::validateString
	 */
	public function testValidateString()
	{
        $validator = new Validator();

        $stringValues = array_merge($this->stringValues, $this->numericStringValues, $this->alphaNumericValues, $this->alphaValues);
        foreach($stringValues as $value){
            $this->assertTrue($validator->validateType('str', $value));
        }

        $nonStringValues = array_merge(array_diff($this->scalarValues, $stringValues), $this->nonScalarValues);
        foreach($nonStringValues as $value){
            $this->assertFalse($validator->validateType('str', $value));
        }
	}

	/**
	 * @covers Validator::validateInteger
	 */
	public function testValidateInteger()
	{
        $validator = new Validator();

        foreach($this->integerValues as $value){
            $this->assertTrue($validator->validateType('int', $value));
        }

        $nonIntegerValues = array_merge(array_diff($this->scalarValues, $this->numericValues), $this->nonScalarValues);
        foreach($nonIntegerValues as $value){
            $this->assertFalse($validator->validateType('int', $value));
        }
	}

	/**
	 * @covers Validator::validateNumeric
	 */
	public function testValidateNumeric()
	{
        $validator = new Validator();

        $numericValues = array_merge($this->numericValues, $this->numericStringValues, $this->floatValues);
        foreach($numericValues as $value){
            $this->assertTrue($validator->validateType('num', $value));
        }

        // '123' is part of $this->alphaNumericValues which contains non-numeric values that need to be tested against
        $nonNumericValues = array_merge(array_diff($this->scalarValues, $numericValues, ['123']), $this->nonScalarValues);
        foreach($nonNumericValues as $value){
            $this->assertFalse($validator->validateType('num', $value));
        }
	}

	/**
	 * @covers Validator::validateFloat
	 */
	public function testValidateFloat()
	{
        $validator = new Validator();

        foreach($this->floatValues as $value){
            $this->assertTrue($validator->validateType('float', $value));
        }

        $nonFloatValues = array_merge(array_diff($this->scalarValues, $this->floatValues), $this->nonScalarValues);
        foreach($nonFloatValues as $value){
            $this->assertFalse($validator->validateType('float', $value));
        }
	}

	/**
	 * @covers Validator::validateAlphaNumeric
	 */
	public function testValidateAlphaNumeric()
	{
        $validator = new Validator();

        foreach($this->alphaNumericValues as $value){
            $this->assertTrue($validator->validateType('alnum', $value));
        }

        $nonAlphaNumericValues = array_merge(
            array_diff($this->scalarValues, $this->alphaNumericValues, $this->alphaValues, $this->numericStringValues),
            $this->nonScalarValues
        );
        foreach($nonAlphaNumericValues as $value){
            $this->assertFalse($validator->validateType('alnum', $value));
        }
	}

	/**
	 * @covers Validator::validateAlpha
	 */
	public function testValidateAlpha()
	{
        $validator = new Validator();

        foreach($this->alphaValues as $value){
            $this->assertTrue($validator->validateType('alpha', $value));
        }

        $nonAlphaValues = array_merge(array_diff($this->scalarValues, $this->alphaValues), $this->nonScalarValues);
        foreach($nonAlphaValues as $value){
            $this->assertFalse($validator->validateType('alpha', $value));
        }
	}

	/**
	 * @covers Validator::validateArray
	 */
	public function testValidateArray()
	{
        $validator = new Validator();

        foreach($this->arrayValues as $value){
            $this->assertTrue($validator->validateType('array', $value));
        }

        $nonArrayValues = array_merge(array_udiff($this->nonScalarValues, $this->arrayValues, function($a, $b){ return $a <=> $b; }), $this->scalarValues);
        foreach($nonArrayValues as $value){
            $this->assertFalse($validator->validateType('array', $value));
        }
	}

	/**
	 * @covers Validator::validateObject
	 */
	public function testValidateObject()
	{
        $validator = new Validator();

        foreach($this->objectValues as $value){
            $this->assertTrue($validator->validateType('object', $value));
        }

        $nonObjectValues = array_merge(array_udiff($this->nonScalarValues, array_merge($this->objectValues, $this->closureValues), function($a, $b){ return $a <=> $b; }), $this->scalarValues);
        foreach($nonObjectValues as $index => $value){
           $this->assertFalse($validator->validateType('object', $value));
        }
	}

	/**
	 * @covers Validator::validateClosure, Validator::validateCallable
	 */
	public function testValidateClosure()
	{
        $validator = new Validator();

        foreach($this->closureValues as $value){
            $this->assertTrue($validator->validateType('closure', $value));
            $this->assertTrue($validator->validateType('callable', $value));
        }

        $nonClosureValues = array_merge(array_udiff($this->nonScalarValues, array_merge($this->closureValues), function($a, $b){ return $a <=> $b; }), $this->scalarValues);
        foreach($nonClosureValues as $index => $value){
            $this->assertFalse($validator->validateType('closure', $value));
            $this->assertFalse($validator->validateType('callable', $value));
        }
	}

	/**
	 * @covers Validator::validateEmail
	 */
	public function testValidateEmail()
	{
        $validator = new Validator();

        foreach($this->emailValues as $value){
            $this->assertTrue($validator->validateType('email', $value));
        }

        $nonEmailValues = array_merge($this->scalarValues, $this->nonScalarValues, $this->ipValues);
        foreach($nonEmailValues as $index => $value){
            $this->assertFalse($validator->validateType('email', $value));
        }
	}

	/**
	 * @covers Validator::validateIP, Validator::validateIPv4, Validator::validateIPv6
	 */
	public function testValidateIP()
	{
        $validator = new Validator();

        foreach($this->ipValues as $value){
            $this->assertTrue($validator->validateType('ip', $value));
        }

        $nonIPValues = array_merge($this->scalarValues, $this->nonScalarValues, $this->emailValues);
        foreach($nonIPValues as $index => $value){
            $this->assertFalse($validator->validateType('ip', $value));
        }
	}
}