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
    protected $dateValues;
    protected $timeValues;
    protected $dateTimeValues;

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
        $this->objectValues = [
            new \stdClass,
            new class
            {
            },
            (object) []
        ];
        $this->closureValues = [
            function () {
            }
        ];
        $this->resourceValues = [$this->resource];
        $this->scalarValues = array_merge(
            $this->booleanValues,
            $this->stringValues,
            $this->integerValues,
            $this->numericValues,
            $this->numericStringValues,
            $this->floatValues,
            $this->alphaNumericValues,
            $this->alphaValues
        );
        $this->nonScalarValues = array_merge(
            $this->arrayValues,
            $this->objectValues,
            $this->closureValues,
            $this->resourceValues,
            $this->nullValues
        );
        $this->emailValues = ['john.doe@example.org', 'jane.doe+test@example.org'];
        $this->ipv4Values = ['127.0.0.1', '8.8.8.8'];
        $this->ipv6Values = ['::1', '2001:0db8:85a3:0000:0000:8a2e:0370:7334'];
        $this->ipValues = array_merge($this->ipv4Values, $this->ipv6Values);
        $this->dateValues = ['1969', '1970-01-01', 'January 1, 2016', '01/01/2016'];
        $this->timeValues = [1451606400, '10:00', '10:00:00', '10:00 AM', '10:00 PM'];
        $this->dateTimeValues = array_merge(
            ['2016-01-01 12:35:00', '2016-01-01T10:35:00+02:00'],
            $this->dateValues,
            $this->timeValues
        );
    }

    protected function tearDown()
    {
        fclose($this->resource);
    }

    ///////////////////////////////////
    // Tests

    /**
     * @covers Validator::addValue, Validator::addValues, Validator::values
     */
    public function testAddValue()
    {
        $validator = new Validator();

        // Add single value
        $validator->addValue('a', 'b');

        // Add multiple values
        $validator->addValues(['b' => 'c', 'c' => 'd']);

        // Get values from validator
        $values = $validator->values();

        // Assertions
        $this->assertTrue(isset($values['a']) && $values['a'] == 'b');
        $this->assertTrue(isset($values['b']) && $values['b'] == 'c');
        $this->assertTrue(isset($values['c']) && $values['c'] == 'd');
    }

    /**
     * @covers Validator::addRule, Validator::addRules, Validator::rules
     */
    public function testAddRule()
    {
        $validator = new Validator();

        // Add single rule
        $validator->addRule('a', 'int');

        // Add multiple rules
        $validator->addRules(['b' => 'str', 'c' => 'bool']);

        // Get rules from validator
        $rules = $validator->rules();

        // Assertions
        $this->assertTrue(isset($rules['a']) && $rules['a'] == 'int');
        $this->assertTrue(isset($rules['b']) && $rules['b'] == 'str');
        $this->assertTrue(isset($rules['c']) && $rules['c'] == 'bool');
    }

    /**
     * @covers Validator::validate, Validator::hasErrors, Validator::getErrors
     */
    public function testValidate()
    {
        // PASS
        $passValues = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.org'
        ];
        $passRules = [
            'first_name' => ['type' => 'string'],
            'last_name' => ['type' => 'string'],
            'email' => ['type' => 'email']
        ];
        $passValidator = new Validator($passValues, $passRules);
        $this->assertTrue($passValidator->validate());
        $this->assertTrue(!$passValidator->hasErrors());
        $this->assertTrue(empty($passValidator->getErrors()));

        // FAIL
        $failValues = [
            'first_name' => 'John',
            'last_name' => null,
            'email' => 'john.doe@example.org'
        ];
        $failRules = [
            'first_name' => ['type' => 'string'],
            'last_name' => ['type' => 'string'],
            'email' => ['type' => 'email']
        ];
        $failValidator = new Validator($failValues, $failRules);
        $this->assertFalse($failValidator->validate());
        $this->assertTrue($failValidator->hasErrors());
        $this->assertArrayHasKey('last_name', $failValidator->getErrors());
    }

    /**
     * @covers Validator::validateNull
     */
    public function testValidateNull()
    {
        $validator = new Validator();

        foreach ($this->nullValues as $value) {
            $this->assertTrue(
                Validator::isNull($value) &&
                Validator::is('null', $value) &&
                $validator->validateType('null', $value)
            );
        }

        // Grab all scalar and non-scalar values to test against
        $values = array_merge($this->scalarValues, $this->nonScalarValues);

        // Remove any strict null values (found in non-scalar values array)
        $nullKeys = array_keys($values, null, true);
        foreach ($nullKeys as $index) {
            unset($values[$index]);
        }
        foreach ($values as $value) {
            $this->assertFalse(
                Validator::isNull($value) &&
                Validator::is('null', $value) &&
                $validator->validateType('null', $value)
            );
        }
    }

    /**
     * @covers Validator::validateBoolean
     */
    public function testValidateBoolean()
    {
        $validator = new Validator();

        foreach ($this->booleanValues as $value) {
            $this->assertTrue(
                Validator::isBool($value) &&
                Validator::isBoolean($value) &&
                Validator::is('bool', $value) &&
                Validator::is('boolean', $value) &&
                $validator->validateType('bool', $value) &&
                $validator->validateType('boolean', $value)
            );
        }

        $nonBooleanValues = array_merge(array_diff($this->scalarValues, $this->booleanValues), $this->nonScalarValues);
        foreach ($nonBooleanValues as $value) {
            $this->assertFalse(
                Validator::isBool($value) &&
                Validator::isBoolean($value) &&
                Validator::is('bool', $value) &&
                Validator::is('boolean', $value) &&
                $validator->validateType('bool', $value) &&
                $validator->validateType('boolean', $value)
            );
        }
    }

    /**
     * @covers Validator::validateScalar
     */
    public function testValidateScalar()
    {
        $validator = new Validator();

        foreach ($this->scalarValues as $value) {
            $this->assertTrue(
                Validator::isScalar($value) &&
                Validator::is('scalar', $value) &&
                $validator->validateType('scalar', $value)
            );
        }

        foreach ($this->nonScalarValues as $value) {
            $this->assertFalse(
                Validator::isScalar($value) &&
                Validator::is('scalar', $value) &&
                $validator->validateType('scalar', $value)
            );
        }
    }

    /**
     * @covers Validator::validateString
     */
    public function testValidateString()
    {
        $validator = new Validator();

        $stringValues = array_merge(
            $this->stringValues,
            $this->numericStringValues,
            $this->alphaNumericValues,
            $this->alphaValues
        );
        foreach ($stringValues as $value) {
            $this->assertTrue(
                Validator::isStr($value) &&
                Validator::isString($value) &&
                Validator::is('str', $value) &&
                Validator::is('string', $value) &&
                $validator->validateType('str', $value) &&
                $validator->validateType('string', $value)
            );
        }

        $nonStringValues = array_merge(array_diff($this->scalarValues, $stringValues), $this->nonScalarValues);
        foreach ($nonStringValues as $value) {
            $this->assertFalse(
                Validator::isStr($value) &&
                Validator::isString($value) &&
                Validator::is('str', $value) &&
                Validator::is('string', $value) &&
                $validator->validateType('str', $value) &&
                $validator->validateType('string', $value)
            );
        }
    }

    /**
     * @covers Validator::validateInteger
     */
    public function testValidateInteger()
    {
        $validator = new Validator();

        foreach ($this->integerValues as $value) {
            $this->assertTrue(
                Validator::isInt($value) &&
                Validator::isInteger($value) &&
                Validator::is('int', $value) &&
                Validator::is('integer', $value) &&
                $validator->validateType('int', $value) &&
                $validator->validateType('integer', $value)
            );
        }

        $nonIntegerValues = array_merge(array_diff($this->scalarValues, $this->numericValues), $this->nonScalarValues);
        foreach ($nonIntegerValues as $value) {
            $this->assertFalse(
                Validator::isInt($value) &&
                Validator::isInteger($value) &&
                Validator::is('int', $value) &&
                Validator::is('integer', $value) &&
                $validator->validateType('int', $value) &&
                $validator->validateType('integer', $value)
            );
        }
    }

    /**
     * @covers Validator::validateNumeric
     */
    public function testValidateNumeric()
    {
        $validator = new Validator();

        $numericValues = array_merge($this->numericValues, $this->numericStringValues, $this->floatValues);
        foreach ($numericValues as $value) {
            $this->assertTrue(
                Validator::isNum($value) &&
                Validator::isNumeric($value) &&
                Validator::is('num', $value) &&
                Validator::is('numeric', $value) &&
                $validator->validateType('num', $value) &&
                $validator->validateType('numeric', $value)
            );
        }

        // '123' is part of $this->alphaNumericValues which contains non-numeric values that need to be tested against
        $nonNumericValues = array_merge(
            array_diff($this->scalarValues, $numericValues, ['123']),
            $this->nonScalarValues
        );
        foreach ($nonNumericValues as $value) {
            $this->assertFalse(
                Validator::isNum($value) &&
                Validator::isNumeric($value) &&
                Validator::is('num', $value) &&
                Validator::is('numeric', $value) &&
                $validator->validateType('num', $value) &&
                $validator->validateType('numeric', $value)
            );
        }
    }

    /**
     * @covers Validator::validateFloat
     */
    public function testValidateFloat()
    {
        $validator = new Validator();

        foreach ($this->floatValues as $value) {
            $this->assertTrue(
                Validator::isFloat($value) &&
                Validator::isDouble($value) &&
                $validator->validateType('float', $value) &&
                $validator->validateType('double', $value)
            );
        }

        $nonFloatValues = array_merge(array_diff($this->scalarValues, $this->floatValues), $this->nonScalarValues);
        foreach ($nonFloatValues as $value) {
            $this->assertFalse(
                Validator::isFloat($value) &&
                Validator::isDouble($value) &&
                $validator->validateType('float', $value) &&
                $validator->validateType('double', $value)
            );
        }
    }

    /**
     * @covers Validator::validateAlphaNumeric
     */
    public function testValidateAlphaNumeric()
    {
        $validator = new Validator();

        foreach ($this->alphaNumericValues as $value) {
            $this->assertTrue(
                Validator::isAlNum($value) &&
                Validator::isAlphaNumeric($value) &&
                $validator->validateType('alnum', $value),
                $validator->validateType('alphanumeric', $value)
            );
        }

        $nonAlphaNumericValues = array_merge(
            array_diff($this->scalarValues, $this->alphaNumericValues, $this->alphaValues, $this->numericStringValues),
            $this->nonScalarValues
        );
        foreach ($nonAlphaNumericValues as $value) {
            $this->assertFalse(
                Validator::isAlNum($value) &&
                Validator::isAlphaNumeric($value) &&
                $validator->validateType('alnum', $value),
                $validator->validateType('alphanumeric', $value)
            );
        }
    }

    /**
     * @covers Validator::validateAlpha
     */
    public function testValidateAlpha()
    {
        $validator = new Validator();

        foreach ($this->alphaValues as $value) {
            $this->assertTrue(
                Validator::isAlpha($value) &&
                $validator->validateType('alpha', $value)
            );
        }

        $nonAlphaValues = array_merge(array_diff($this->scalarValues, $this->alphaValues), $this->nonScalarValues);
        foreach ($nonAlphaValues as $value) {
            $this->assertFalse(
                Validator::isAlpha($value) &&
                $validator->validateType('alpha', $value)
            );
        }
    }

    /**
     * @covers Validator::validateArray
     */
    public function testValidateArray()
    {
        $validator = new Validator();

        foreach ($this->arrayValues as $value) {
            $this->assertTrue(
                Validator::isArr($value) &&
                Validator::isArray($value) &&
                $validator->validateType('arr', $value) &&
                $validator->validateType('array', $value)
            );
        }

        $nonArrayValues = array_merge(array_udiff($this->nonScalarValues, $this->arrayValues, function ($a, $b) {
            return $a <=> $b;
        }), $this->scalarValues);
        foreach ($nonArrayValues as $value) {
            $this->assertFalse(
                Validator::isArr($value) &&
                Validator::isArray($value) &&
                $validator->validateType('arr', $value) &&
                $validator->validateType('array', $value)
            );
        }
    }

    /**
     * @covers Validator::validateObject
     */
    public function testValidateObject()
    {
        $validator = new Validator();

        foreach ($this->objectValues as $value) {
            $this->assertTrue(
                Validator::isObj($value) &&
                Validator::isObject($value) &&
                $validator->validateType('obj', $value) &&
                $validator->validateType('object', $value)
            );
        }

        $nonObjectValues = array_merge(
            array_udiff(
                $this->nonScalarValues,
                array_merge($this->objectValues, $this->closureValues),
                function ($a, $b) {
                    return $a <=> $b;
                }
            ),
            $this->scalarValues
        );
        foreach ($nonObjectValues as $index => $value) {
            $this->assertFalse(
                Validator::isObj($value) &&
                Validator::isObject($value) &&
                $validator->validateType('obj', $value) &&
                $validator->validateType('object', $value)
            );
        }
    }

    /**
     * @covers Validator::validateClosure, Validator::validateCallable
     */
    public function testValidateClosure()
    {
        $validator = new Validator();

        foreach ($this->closureValues as $value) {
            $this->assertTrue(
                Validator::isClosure($value) &&
                Validator::isCallable($value) &&
                $validator->validateType('closure', $value) &&
                $validator->validateType('callable', $value)
            );
        }

        $nonClosureValues = array_merge(
            array_udiff(
                $this->nonScalarValues,
                array_merge($this->closureValues),
                function ($a, $b) {
                    return $a <=> $b;
                }
            ),
            $this->scalarValues
        );
        foreach ($nonClosureValues as $index => $value) {
            $this->assertFalse(
                Validator::isClosure($value) &&
                Validator::isCallable($value) &&
                $validator->validateType('closure', $value) &&
                $validator->validateType('callable', $value)
            );
        }
    }

    /**
     * @covers Validator::validateEmail
     */
    public function testValidateEmail()
    {
        $validator = new Validator();

        foreach ($this->emailValues as $value) {
            $this->assertTrue(
                Validator::isEmail($value),
                $validator->validateType('email', $value)
            );
        }

        $nonEmailValues = array_merge($this->scalarValues, $this->nonScalarValues, $this->ipValues);
        foreach ($nonEmailValues as $index => $value) {
            $this->assertFalse(
                Validator::isEmail($value),
                $validator->validateType('email', $value)
            );
        }
    }

    /**
     * @covers Validator::validateIP, Validator::validateIPv4, Validator::validateIPv6
     */
    public function testValidateIP()
    {
        $validator = new Validator();
        $nonIPValues = array_merge($this->scalarValues, $this->nonScalarValues, $this->emailValues);

        // Validate either IPv4 or IPv6 addresses
        foreach ($this->ipValues as $value) {
            $this->assertTrue(
                Validator::isIP($value) &&
                $validator->validateType('ip', $value)
            );
        }

        // Validate IPv4 addresses
        foreach ($this->ipv4Values as $value) {
            $this->assertTrue(
                Validator::isIPv4($value) &&
                $validator->validateType('ipv4', $value)
            );
        }

        // Validate IPv6 addresses
        foreach ($this->ipv6Values as $value) {
            $this->assertTrue(
                Validator::isIPv6($value) &&
                $validator->validateType('ipv6', $value)
            );
        }

        // Validate non-IP addresses
        foreach ($nonIPValues as $index => $value) {
            $this->assertFalse(
                Validator::isIP($value) &&
                Validator::isIPv4($value) &&
                Validator::isIPv6($value) &&
                $validator->validateType('ip', $value) &&
                $validator->validateType('ipv4', $value) &&
                $validator->validateType('ipv6', $value)
            );
        }
    }

    /**
     * @covers Validator::validateDateTime
     */
    public function testValidateDateTime()
    {
        $validator = new Validator();

        // Validate date & time values
        foreach ($this->dateTimeValues as $value) {
            $this->assertTrue(
                Validator::isDate($value) &&
                Validator::isDateTime($value) &&
                $validator->validateType('date', $value) &&
                $validator->validateType('datetime', $value)
            );
        }

        // Validate date & time w/ valid format
        $formatDateTime = [
            'Ymd' => '20160101',
            'Y-m-d' => '2016-01-01',
            'Y-m-d H:i:s' => '2016-01-01 12:35:00',
            'H:i:s' => '12:35:00',
            'Y' => '2016'
        ];
        foreach ($formatDateTime as $format => $value) {
            $this->assertTrue(
                Validator::isDate($value, ['format' => $format]) &&
                Validator::isDateTime($value, ['format' => $format]) &&
                $validator->validateType('date', $value, ['format' => $format]) &&
                $validator->validateType('datetime', $value, ['format' => $format])
            );
        }

        // Validate invalid date & time values
        $nonDateTimeValues = array_merge($this->nonScalarValues, ['Hello World', null, true, false, '']);
        foreach ($nonDateTimeValues as $value) {
            $this->assertFalse(
                Validator::isDate($value) &&
                Validator::isDateTime($value) &&
                $validator->validateType('date', $value) &&
                $validator->validateType('datetime', $value)
            );
        }

        // Validate date & time w/ invalid format
        $formatDateTime = [
            'Y-m-d' => '20160101',
            'Ymd' => '2016-01-01',
            'm' => '2016'
        ];
        foreach ($formatDateTime as $format => $value) {
            $this->assertFalse(
                Validator::isDate($value, ['format' => $format]) &&
                Validator::isDateTime($value, ['format' => $format]) &&
                $validator->validateType('date', $value, ['format' => $format]) &&
                $validator->validateType('datetime', $value, ['format' => $format])
            );
        }
    }
}
