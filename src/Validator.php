<?php declare(strict_types = 1);
namespace Noname\Common;

/**
 * Validator
 *
 * @package Noname\Common
 * @since 0.2.0
 *
 * @method static bool is(string $type, mixed $value, array $rule = []) Checks if value passes as type
 * @method static bool isNull(mixed $value, array $rule = []) Checks if value is null
 * @method static bool isBool(mixed $value, array $rule = []) Checks if value is boolean
 * @method static bool isBoolean(mixed $value, array $rule = []) Checks if value is boolean
 * @method static bool isScalar(mixed $value, array $rule = []) Checks if value is scalar
 * @method static bool isStr(mixed $value, array $rule = []) Checks if value is string
 * @method static bool isString(mixed $value, array $rule = []) Checks if value is string
 * @method static bool isInt(mixed $value, array $rule = []) Checks if value is integer
 * @method static bool isInteger(mixed $value, array $rule = []) Checks if value is integer
 * @method static bool isNum(mixed $value, array $rule = []) Checks if value is numeric
 * @method static bool isNumeric(mixed $value, array $rule = []) Checks if value is numeric
 * @method static bool isFloat(mixed $value, array $rule = []) Checks if value is float
 * @method static bool isDouble(mixed $value, array $rule = []) Checks if value is double
 * @method static bool isAlNum(mixed $value, array $rule = []) Check for alpha-numeric characters only
 * @method static bool isAlphaNumeric(mixed $value, array $rule = []) Check for alpha-numeric characters only
 * @method static bool isAlpha(mixed $value, array $rule = []) Checks if value contains only alpha characters
 * @method static bool isArr(mixed $value, array $rule = []) Checks if value is an array
 * @method static bool isArray(mixed $value, array $rule = []) Checks if value is an array
 * @method static bool isObj(mixed $value, array $rule = []) Checks if value is an object
 * @method static bool isObject(mixed $value, array $rule = []) Checks if value is an object
 * @method static bool isClosure(mixed $value, array $rule = []) Checks if value is instance of \Closure
 * @method static bool isCallable(mixed $value, array $rule = []) Checks if value is callable
 * @method static bool isEmail(mixed $value, array $rule = []) Checks if value is valid email address
 * @method static bool isIP(mixed $value, array $rule = []) Checks if value is valid IPv4 or IPv6
 * @method static bool isIPv4(mixed $value, array $rule = []) Checks if value is valid IPv4
 * @method static bool isIPv6(mixed $value, array $rule = []) Checks if value is valid IPv6
 * @method static bool isDate(mixed $value, array $rule = []) Checks if value is date/datetime
 * @method static bool isDateTime(mixed $value, array $rule = []) Checks if value is date/datetime
 */
class Validator
{
    /**
     * @var Collection
     */
    private $values;

    /**
     * @var Collection
     */
    private $rules;

    /**
     * @var Collection
     */
    private $errors;

    /**
     * Map of types to validate method names.
     *
     * @var array
     */
    private $validateTypeMethodMap = [
        'null' => 'validateNull',
        'bool' => 'validateBoolean',
        'boolean' => 'validateBoolean',
        'scalar' => 'validateScalar',
        'str' => 'validateString',
        'string' => 'validateString',
        'int' => 'validateInteger',
        'integer' => 'validateInteger',
        'num' => 'validateNumeric',
        'numeric' => 'validateNumeric',
        'float' => 'validateFloat',
        'double' => 'validateFloat',
        'alnum' => 'validateAlphaNumeric',
        'alphanumeric' => 'validateAlphaNumeric',
        'alpha' => 'validateAlpha',
        'arr' => 'validateArray',
        'array' => 'validateArray',
        'obj' => 'validateObject',
        'object' => 'validateObject',
        'closure' => 'validateClosure',
        'callable' => 'validateCallable',
        'email' => 'validateEmail',
        'ip' => 'validateIP',
        'ipv4' => 'validateIPv4',
        'ipv6' => 'validateIPv6',
        'date' => 'validateDateTime',
        'datetime' => 'validateDateTime',
    ];

    /**
     * Create Validator
     *
     * @param array $values
     * @param array $rules
     */
    public function __construct(array $values = [], array $rules = [])
    {
        // Create collections for datasets
        $this->values = new Collection($values);
        $this->rules = new Collection($rules);
        $this->errors = new Collection();
    }

    /**
     * Magic method to handle calls to undefined static methods.
     *
     * @param string $method
     * @param array $arguments
     * @throws \InvalidArgumentException, \BadMethodCallException
     * @return bool
     */
    public static function __callStatic($method, $arguments)
    {
        // Split split method name into parts on each uppercased letter
        $parts = preg_split('/(?=[A-Z])/', lcfirst($method));

        $func = array_shift($parts);
        $numArgs = count($arguments);

        if ($func == 'is') {
            if (empty($parts)) {
                // Handle call to Validator::is($type, $value [, $rule])
                if (in_array($numArgs, [2, 3])) {
                    if ($numArgs == 3) {
                        list($type, $value, $rule) = $arguments;
                        if (!is_array($rule)) {
                            $rule = [];
                        }
                    } else {
                        $rule = [];
                        list($type, $value) = $arguments;
                    }
                    return (new self())->validateType($type, $value, $rule);
                }
                throw new \InvalidArgumentException("Too many arguments passed to Validator::{$method}()");
            } else {
                // Handle call to Validator::is{Type}($value [, $rule])
                $type = implode('', $parts);
                $value = $arguments[0];
                $rule = isset($arguments[1]) && is_array($arguments[1]) ? $arguments[1] : [];
                return (new self())->validateType($type, $value, $rule);
            }
        }

        // Undefined method; Throw exception
        throw new \BadMethodCallException("Validator::{$method}() not defined.");
    }

    /**
     * Validate the values based on the rules.
     *
     * @return bool
     */
    public function validate() : bool
    {
        if (!empty($this->rules)) {
            foreach ($this->rules as $name => $rule) {
                if (array_key_exists($name, $this->values->toArray())) {
                    if (is_string($rule)) {
                        // Rebuild $rule into proper format
                        // e.g. 'email' -> ['type' => 'email']
                        $rule = ['type' => $rule];
                    }
                    if (isset($rule['type'])) {
                        if (!$this->validateType($rule['type'], $this->values[$name], $rule)) {
                            // Error: Value for '%s' is invalid
                            $this->setError($name, "Value for '$name' is invalid. Expected {$rule['type']}.");
                        }
                    } else {
                        throw new \InvalidArgumentException("Invalid rule for '$name'");
                    }
                } else {
                    if (isset($rule['required']) && $rule['required']) {
                        // Error: Value for '%s' is required
                        $this->setError($name, "Value for '$name' is required");
                    }
                }
            }
        }

        return $this->hasErrors() ? false : true;
    }

    /**
     * Set a validation error
     *
     * @param string $name
     * @param string $error
     */
    public function setError($name, $error)
    {
        if ($this->errors->has($name)) {
            $errors = $this->errors->get($name);
        } else {
            $errors = [];
        }
        $errors[] = $error;
        $this->errors->set($name, $errors);
    }

    /**
     * Get errors that occurred during validation.
     *
     * @return array
     */
    public function getErrors() : array
    {
        return $this->errors->toArray();
    }

    /**
     * Check if errors occurred during validation.
     *
     * @return bool
     */
    public function hasErrors() : bool
    {
        return (bool) $this->errors->count();
    }

    ///////////////////////////////////
    // Validators

    /**
     * Passes value to appropriate validate method based on $type.
     *
     * @param string $type
     * @param mixed $value
     * @param array $rule
     * @return bool
     */
    public function validateType($type, $value, array $rule = []) : bool
    {
        $type = strtolower($type);
        if (isset($this->validateTypeMethodMap[$type])) {
            return $this->{$this->validateTypeMethodMap[$type]}($value, $rule);
        } else {
            throw new \InvalidArgumentException("Type '$type' is not a valid rule type");
        }
    }

    /**
     * Validate that $value is null.
     *
     * @param mixed $value
     * @param array $rule
     * @return bool
     */
    private function validateNull($value, array $rule = []) : bool
    {
        return is_null($value);
    }

    /**
     * Validate that $value is boolean.
     *
     * @param mixed $value
     * @param array $rule
     * @return bool
     */
    private function validateBoolean($value, array $rule = []) : bool
    {
        return is_bool($value);
    }

    /**
     * Validate that $value is scalar.
     *
     * @param mixed $value
     * @param array $rule
     * @return bool
     */
    private function validateScalar($value, array $rule = []) : bool
    {
        return is_scalar($value);
    }

    /**
     * Validate that $value is an array.
     *
     * @param mixed $value
     * @param array $rule
     * @return bool
     */
    private function validateArray($value, array $rule = []) : bool
    {
        return is_array($value);
    }

    /**
     * Validate that $value is an object.
     *
     * @param mixed $value
     * @param array $rule
     * @return bool
     */
    private function validateObject($value, array $rule = []) : bool
    {
        return is_object($value);
    }

    /**
     * Validate that $value is a string.
     *
     * @param mixed $value
     * @param array $rule
     * @return bool
     */
    private function validateString($value, array $rule = []) : bool
    {
        return is_string($value);
    }

    /**
     * Validate that $value is an integer.
     *
     * @param mixed $value
     * @param array $rule
     * @return bool
     */
    private function validateInteger($value, array $rule = []) : bool
    {
        return is_int($value);
    }

    /**
     * Validate that $value is numeric.
     *
     * @param mixed $value
     * @param array $rule
     * @return bool
     */
    private function validateNumeric($value, array $rule = []) : bool
    {
        return is_numeric($value);
    }

    /**
     * Validate that $value is a float/double/real number.
     *
     * @param mixed $value
     * @param array $rule
     * @return bool
     */
    private function validateFloat($value, array $rule = []) : bool
    {
        return is_float($value);
    }

    /**
     * Validate that $value has alpha numeric characters.
     *
     * @param mixed $value
     * @param array $rule
     * @return bool
     */
    private function validateAlphaNumeric($value, array $rule = []) : bool
    {
        return is_string($value) && ctype_alnum($value);
    }

    /**
     * Validate that $value has alpha characters.
     *
     * @param mixed $value
     * @param array $rule
     * @return bool
     */
    private function validateAlpha($value, array $rule = []) : bool
    {
        return is_string($value) && ctype_alpha($value);
    }

    /**
     * Validate that $value is valid email address.
     *
     * @param mixed $value
     * @param array $rule
     * @return bool
     */
    private function validateEmail($value, array $rule = []) : bool
    {
        return (bool) filter_var($value, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Validate that $value is valid IPv4 or IPv6
     *
     * @param mixed $value
     * @param array $rule
     * @return bool
     */
    private function validateIP($value, array $rule = []) : bool
    {
        return $this->validateIPv4($value, $rule) || $this->validateIPv6($value, $rule);
    }

    /**
     * Validate that $value is valid IPv4
     *
     * @param mixed $value
     * @param array $rule
     * @return bool
     */
    private function validateIPv4($value, array $rule = []) : bool
    {
        return (bool) filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
    }

    /**
     * Validate that $value is valid IPv6
     *
     * @param mixed $value
     * @param array $rule
     * @return bool
     */
    private function validateIPv6($value, array $rule = []) : bool
    {
        return (bool) filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
    }

    /**
     * Validate that $value is instance of \Closure.
     *
     * @param mixed $value
     * @param array $rule
     * @return bool
     */
    private function validateClosure($value, array $rule = []) : bool
    {
        return ($value instanceof \Closure);
    }

    /**
     * Validate that $value is callable.
     *
     * @param mixed $value
     * @param array $rule
     * @return bool
     */
    private function validateCallable($value, array $rule = []) : bool
    {
        return is_callable($value);
    }

    /**
     * Validate that $value is a valid date/time.
     *
     * @param mixed $value
     * @param array $rule
     * @return bool
     */
    private function validateDateTime($value, array $rule = []) : bool
    {
        if (!is_string($value) && !is_int($value)) {
            return false;
        }

        $value = (string) $value;

        // Returns a timestamp on success, FALSE otherwise
        if (($time = strtotime($value)) === false) {
            return false;
        }

        // Returns new \DateTime instance of success, FALSE otherwise
        if (($date = date_create($value)) === false) {
            return false;
        }

        if (isset($rule['format'])) {
            return $date->format($rule['format']) == $value;
        }

        return true;
    }
}
