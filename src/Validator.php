<?php declare(strict_types = 1);
namespace Noname\Common;

/**
 * Validator
 *
 * @package Noname\Common
 * @since 0.2.0
 *
 * @method static bool is(string $type, mixed $value, array $rule = []) Checks if value passes as type
 * @method static bool isAny(mixed $value, array $rule = []) Always returns true
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
 * @method static bool isObject(mixed $value, array $rule = []) Checks if value is an object
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
     * Collection of values to be validated.
     *
     * @var Collection
     */
    protected $values;

    /**
     * Collection of validation rules.
     *
     * @var Collection
     */
    protected $rules;

    /**
     * Collection of errors thrown during validation.
     *
     * @var Collection
     */
    protected $errors;

    /**
     * Collection of type validators (e.g. string)
     *
     * @var Collection
     */
    protected $typeValidators;

    /**
     * Flag for when validator is called statically.
     *
     * @var bool
     */
    protected $isStaticMethodCall = false;

    /**
     * Create instance of Validator
     *
     * @param array $values
     * @param array $rules
     */
    public function __construct(array $values = [], array $rules = [])
    {
        // Create collections
        $this->values = new Collection($values);
        $this->rules = new Collection($rules);
        $this->errors = new Collection();

        // Register built-in validator types
        $this->registerBuiltInTypes();
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
                    return (new self())->staticMethodCall()->validateType($type, $value, $rule);
                }
                throw new \InvalidArgumentException("Too many arguments passed to Validator::{$method}()");
            } else {
                // Handle call to Validator::is{Type}($value [, $rule])
                $type = implode('', $parts);
                $value = $arguments[0];
                $rule = isset($arguments[1]) && is_array($arguments[1]) ? $arguments[1] : [];
                return (new self())->staticMethodCall()->validateType($type, $value, $rule);
            }
        }

        // Undefined method; Throw exception
        throw new \BadMethodCallException("Validator::{$method}() not defined.");
    }

    /**
     * Sets flag for when validator is called statically.
     *
     * @return $this
     */
    protected function staticMethodCall()
    {
        $this->isStaticMethodCall = true;
        return $this;
    }

    /**
     * Register built-in type validators.
     */
    protected function registerBuiltInTypes()
    {
        $this->typeValidators = new Collection();

        $this->addType('any', [
            'alias' => '*',
            'validator' => [$this, 'validateAny']
        ]);

        $this->addType('null', [
            'validator' => [$this, 'validateNull']
        ]);

        $this->addType('boolean', [
            'alias' => 'bool',
            'validator' => [$this, 'validateBoolean']
        ]);

        $this->addType('scalar', [
            'validator' => [$this, 'validateScalar']
        ]);

        $this->addType('numeric', [
            'alias' => 'num',
            'validator' => [$this, 'validateNumeric']
        ]);

        $this->addType('integer', [
            'alias' => 'int',
            'extends' => 'numeric',
            'validator' => [$this, 'validateInteger']
        ]);

        $this->addType('float', [
            'alias' => 'double',
            'extends' => 'numeric',
            'validator' => [$this, 'validateFloat']
        ]);

        $this->addType('string', [
            'alias' => 'str',
            'validator' => [$this, 'validateString']
        ]);

        $this->addType('alpha', [
            'validator' => [$this, 'validateAlpha']
        ]);

        $this->addType('alphanumeric', [
            'alias' => 'alnum',
            'validator' => [$this, 'validateAlphaNumeric']
        ]);

        $this->addType('array', [
            'alias' => 'arr',
            'validator' => [$this, 'validateArray']
        ]);

        $this->addType('object', [
            'validator' => [$this, 'validateObject']
        ]);

        $this->addType('callable', [
            'validator' => [$this, 'validateCallable']
        ]);

        $this->addType('email', [
            'extends' => 'string',
            'validator' => [$this, 'validateEmail']
        ]);

        $this->addType('ip', [
            'validator' => [$this, 'validateIP']
        ]);

        $this->addType('ipv4', [
            'validator' => [$this, 'validateIPv4']
        ]);

        $this->addType('ipv6', [
            'validator' => [$this, 'validateIPv6']
        ]);

        $this->addType('date', [
            'alias' => 'datetime',
            'validator' => [$this, 'validateDateTime']
        ]);
    }

    /**
     * Add validator type.
     *
     * @param string $typeName
     * @param array $typeRule
     * @throws \InvalidArgumentException
     */
    public function addType(string $typeName, array $typeRule)
    {
        // Name must be unique
        if (isset($this->typeValidators[$typeName])) {
            throw new \InvalidArgumentException("Cannot add type '$typeName' because it is already defined");
        }

        // Type must have a validator
        if (!isset($typeRule['validator'])) {
            throw new \InvalidArgumentException("Cannot add type '$typeName' because no validator is defined");
        }

        // If type extends another type then the extended type must be defined
        if (isset($typeRule['extends']) && !isset($this->typeValidators[$typeRule['extends']])) {
            throw new \InvalidArgumentException("Cannot extend type '{$typeRule['extends']}' because it is not defined");
        }

        // Alias name must be unique
        if (isset($typeRule['alias']) && isset($this->typeValidators[$typeRule['alias']])) {
            throw new \InvalidArgumentException("Cannot add type alias '{$typeRule['alias']}' because it is already defined");
        }

        // Validator must be callable
        if (!is_callable($typeRule['validator'])) {
            throw new \InvalidArgumentException("Cannot add type '$typeName' because validator is not callable.");
        }

        // Add type to collection with name
        $this->typeValidators[$typeName] = $typeRule;
        if (isset($typeRule['alias'])) {
            // Add type alias to collection as well
            $this->typeValidators[$typeRule['alias']] = $typeRule;
        }
    }

    /**
     * Add value.
     *
     * @param string $name
     * @param mixed $value
     */
    public function addValue(string $name, $value)
    {
        $this->values->set($name, $value);
    }

    /**
     * Add multiple values.
     *
     * @param array $values
     */
    public function addValues(array $values)
    {
        foreach ($values as $name => $value) {
            $this->values->set($name, $value);
        }
    }

    /**
     * Get values.
     *
     * @return array
     */
    public function values(): array
    {
        return $this->values->toArray();
    }

    /**
     * Add rule.
     *
     * @param string $name
     * @param mixed $rule
     */
    public function addRule(string $name, $rule)
    {
        $this->rules->set($name, $rule);
    }

    /**
     * Add multiple rules.
     *
     * @param array $rules
     */
    public function addRules(array $rules)
    {
        foreach ($rules as $name => $rule) {
            $this->rules->set($name, $rule);
        }
    }

    /**
     * Get rules.
     *
     * @return array
     */
    public function rules(): array
    {
        return $this->rules->toArray();
    }

    /**
     * Validate values using rules.
     *
     * @return bool Returns TRUE if there are no errors and FALSE otherwise
     * @throws \InvalidArgumentException
     */
    public function validate(): bool
    {
        if ($this->rules->count()) {
            // Loop through each rule and validate their respective values
            foreach ($this->rules as $name => $rule) {
                // Convert string/closure rules into proper rule format
                if (is_string($rule)) {
                    $rule = ['type' => $rule];
                } elseif (is_callable($rule)) {
                    // Rule uses closure/callable for validation
                    $rule = ['type' => '*', 'validator' => $rule];
                }

                // Make sure rule is formatted correctly
                if (!is_array($rule) || !isset($rule['type'])) {
                    throw new \InvalidArgumentException("Rule format for '$name' is invalid.");
                }

                // Value is required by default
                if (!isset($rule['required'])) {
                    $rule['required'] = true;
                }

                // Add key/name to $rule
                $rule['name'] = $name;

                // Check if value exists in dataset
                if ($value = $this->values->get($rule['name'], false)) {
                    // Validate type
                    if (!$this->validateType($rule['type'], $value, $rule)) {
                        // Value didn't pass validation; Process next rule.
                        continue;
                    }

                    // Validate using callable validator
                    if (isset($rule['validator'])) {
                        if (is_callable($rule['validator'])) {
                            if (!call_user_func_array($rule['validator'], [$value, $rule, $this])) {
                                if (!$this->errors->has($rule['name'])) {
                                    // Set error message for callable validator if not already set
                                    $this->setError($rule['name'], "Value ($value) failed to validate as '{$rule['type']}'");
                                }
                                // Value didn't pass validation; Process next rule
                                continue;
                            }
                        } else {
                            throw new \InvalidArgumentException("Custom validator for {$rule['name']} must be callable");
                        }
                    }
                } else {
                    // Set an error if the undefined value is required
                    if ($rule['required']) {
                        $this->setError($name, "Value for '$name' is required.");
                        continue;
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
    public function getErrors(): array
    {
        return $this->errors->toArray();
    }

    /**
     * Check if errors occurred during validation.
     *
     * @return bool
     */
    public function hasErrors(): bool
    {
        return (bool) $this->errors->count();
    }

    ///////////////////////////////////
    // Validators

    /**
     * Validates value using appropriate type validator.
     *
     * @param string $type
     * @param mixed $value
     * @param array $rule
     * @return bool
     */
    protected function validateType($type, $value, array $rule = []): bool
    {
        $type = strtolower($type);

        // Check for array type (e.g. type[])
        $isArrayType = (substr($type, -2) == '[]');
        if ($isArrayType) {
            $type = substr($type, 0, -2);
        }

        // Load rules for type validator
        $validator = $this->typeValidators->get($type);
        if (!$validator) {
            throw new \InvalidArgumentException("'$type' is not a valid validator type");
        }

        if ($this->isStaticMethodCall) {
            // The name doesn't matter when validator is called statically,
            // but is needed for setting error messages that won't be used
            // other than to check for the existence of errors.
            $rule['name'] = '_';
        }

        if ($isArrayType) {
            // Validate an array of values
            $values = (array) $value;
            foreach ($values as $index => $value) {
                // Validate the extended type first
                if (isset($validator['extends'])) {
                    $this->validateType($validator['extends'], $value, $rule);
                }

                // Validate the value using the type validator
                if (!call_user_func_array($validator['validator'], [$value, $rule, $this])) {
                    if (is_scalar($value)) {
                        // Value is scalar and can be included in the error message
                        $this->setError($rule['name'], "Value ($value) failed to validate as '$type'");
                    } else {
                        // Value is not scalar and cannot be included in the error message
                        $this->setError($rule['name'], "Value failed to validate as '$type'");
                    }
                }
            }
        } else {
            // Validate the value
            if (isset($validator['extends'])) {
                $this->validateType($validator['extends'], $value, $rule);
            }

            // Validate the value using the type's validator
            if (!call_user_func_array($validator['validator'], [$value, $rule, $this])) {
                if (is_scalar($value)) {
                    // Value is scalar and can be included in the error message
                    $this->setError($rule['name'], "Value ($value) failed to validate as '$type'");
                } else {
                    // Value is not scalar and cannot be included in the error message
                    $this->setError($rule['name'], "Value failed to validate as '$type'");
                }
            }
        }

        return $this->hasErrors() ? false : true;
    }

    /**
     * No validation is done. Always returns true.
     *
     * @param $value
     * @param array $rule
     * @return bool
     */
    protected function validateAny($value, array $rule = []): bool
    {
        return true;
    }

    /**
     * Validate that $value is null.
     *
     * @param mixed $value
     * @param array $rule
     * @return bool
     */
    protected function validateNull($value, array $rule = []): bool
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
    protected function validateBoolean($value, array $rule = []): bool
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
    protected function validateScalar($value, array $rule = []): bool
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
    protected function validateArray($value, array $rule = []): bool
    {
        $valid = is_array($value);

        if (isset($rule['allow_empty']) && $rule['allow_empty'] === false) {
            $valid = $valid && !empty($value);
        }

        if (isset($rule['count']) && is_int($rule['count'])) {
            $valid = $valid && (count($value) == $rule['count']);
        }

        if (isset($rule['min_count']) && is_int($rule['min_count'])) {
            $valid = $valid && (count($value) >= $rule['min_count']);
        }

        if (isset($rule['max_count']) && is_int($rule['max_count'])) {
            $valid = $valid && (count($value) <= $rule['max_count']);
        }

        return $valid;
    }

    /**
     * Validate that $value is an object.
     *
     * @param mixed $value
     * @param array $rule
     * @return bool
     */
    protected function validateObject($value, array $rule = []): bool
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
    protected function validateString($value, array $rule = []): bool
    {
        $valid = is_string($value);

        if (isset($rule['equals']) && is_string($rule['equals'])) {
            $valid = $valid && $value == $rule['equals'];
        }

        if (isset($rule['in']) && is_array($rule['in'])) {
            $valid = $valid && in_array($value, $rule['in']);
        }

        if (isset($rule['allow_empty']) && $rule['allow_empty'] === false) {
            $valid = $valid && !empty($value);
        }

        if (isset($rule['allow_null']) && $rule['allow_null'] === true) {
            $valid = $valid || is_null($value);
        }

        if (isset($rule['min_length']) && is_int($rule['min_length'])) {
            $valid = $valid && (strlen($value) >= $rule['min_length']);
        }

        if (isset($rule['max_length']) && is_int($rule['max_length'])) {
            $valid = $valid && (strlen($value) <= $rule['max_length']);
        }

        return $valid;
    }

    /**
     * Validate that $value is an integer.
     *
     * @param mixed $value
     * @param array $rule
     * @return bool
     * @throws \Exception
     */
    protected function validateInteger($value, array $rule = []): bool
    {
        $valid = is_int($value);

        if (isset($rule['unsigned']) && $rule['unsigned'] === true) {
            // Validate that int is unsigned
            if ($valid = $valid && ($value > 0)) {
                // Unsigned integer violation check
                foreach (['>', '>=', '<', '<=', 'equals'] as $r) {
                    if (isset($rule[$r]) && $rule[$r] < 0) {
                        throw new \Exception("'$r' must be >= 0 for unsigned integers.");
                    }
                }
            }
        }

        if (isset($rule['equals']) && is_int($rule['equals'])) {
            $valid = $valid && $value == $rule['equals'];
        }

        if (isset($rule['in']) && is_array($rule['in'])) {
            $valid = $valid && in_array($value, $rule['in']);
        }

        // Validate > or >=, with > getting priority over >=
        if (isset($rule['>']) && is_int($rule['>'])) {
            $valid = $valid && ($value > $rule['>']);
        } elseif (isset($rule['>=']) && is_int($rule['>='])) {
            $valid = $valid && ($value >= $rule['>=']);
        }

        // Validate < or <=, with < getting priority over <=
        if (isset($rule['<']) && is_int($rule['<'])) {
            $valid = $valid && ($value < $rule['<']);
        } elseif (isset($rule['<=']) && is_int($rule['<='])) {
            $valid = $valid && ($value <= $rule['<=']);
        }

        return $valid;
    }

    /**
     * Validate that $value is numeric.
     *
     * @param mixed $value
     * @param array $rule
     * @return bool
     */
    protected function validateNumeric($value, array $rule = []): bool
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
    protected function validateFloat($value, array $rule = []): bool
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
    protected function validateAlphaNumeric($value, array $rule = []): bool
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
    protected function validateAlpha($value, array $rule = []): bool
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
    protected function validateEmail($value, array $rule = []): bool
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
    protected function validateIP($value, array $rule = []): bool
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
    protected function validateIPv4($value, array $rule = []): bool
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
    protected function validateIPv6($value, array $rule = []): bool
    {
        return (bool) filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
    }

    /**
     * Validate that $value is callable.
     *
     * @param mixed $value
     * @param array $rule
     * @return bool
     */
    protected function validateCallable($value, array $rule = []): bool
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
    protected function validateDateTime($value, array $rule = []): bool
    {
        if (!is_string($value) && !is_int($value)) {
            return false;
        }

        $value = (string) $value;
        $compare = $value;

        if(strlen($compare) == 4){
            // Value is presumably a 4-digit year (e.g. 2016) and as-is doesn't
            // play nice with strtotime() or date_create()
            $compare .= "-01-01";
        }

        // Returns a timestamp on success, FALSE otherwise
        if (($time = strtotime($compare)) === false) {
            return false;
        }

        // Returns new \DateTime instance on success, FALSE otherwise
        if (($date = date_create($compare)) === false) {
            return false;
        }

        if (isset($rule['format'])) {
            // Use original value for comparing format
            return $date->format($rule['format']) == $value;
        }

        return true;
    }
}
