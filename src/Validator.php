<?php declare(strict_types = 1);
namespace Noname\Common;

/**
 * Validator
 *
 * @package Noname\Common
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
	private $settings;

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
		'number' => 'validateNumeric',
		'float' => 'validateFloat',
		'double' => 'validateFloat',
		'alnum' => 'validateAlphaNumeric',
		'alphaNumeric' => 'validateAlphaNumeric',
		'alpha' => 'validateAlpha',
		'arr' => 'validateArray',
		'array' => 'validateArray',
		'obj' => 'validateObject',
		'object' => 'validateObject',
		'class' => 'validateClass',
		'closure' => 'validateClosure',
		'callable' => 'validateCallable',
		'email' => 'validateEmail',
		'date' => 'validateDate',
		'ip' => 'validateIP',
		'ipv4' => 'validateIPv4',
		'ipv6' => 'validateIPv6',
	];

	/**
	 * Create Validator
	 *
	 * @param array $values
	 * @param array $rules
	 * @param array $settings
	 */
	public function __construct(array $values = [], array $rules = [], array $settings = [])
	{
		// Create collections for datasets
		$this->values = new Collection($values);
		$this->rules = new Collection($rules);
		$this->settings = new Collection($settings);
		$this->errors = new Collection();
	}

	/**
	 * Validate the values based on the rules.
	 *
	 * @return bool
	 */
	public function validate() : bool
	{
		if(!empty($this->rules)){
			foreach($this->rules as $name => $rule){
				if(isset($this->values[$name])){
					if(is_string($rule)){
						// Rebuild $rule into proper format
						$rule = ['type' => $rule];
					}
					if(!$this->validateType($rule['type'], $this->values[$name], $rule)){
						// Error: Value for '%s' is invalid
						$this->setError($name, "Value for '$name' is invalid. Expected {$rule['type']}.");
					}
				}else{
					if(isset($rule['required']) && $rule['required']){
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
		if($this->errors->has($name)){
			$errors = $this->errors->get($name);
		}else{
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
	private function validateType($type, $value, array $rule) : bool
	{
		$type = strtolower($type);
		if(isset($this->validateTypeMethodMap[$type])){
			return $this->{$this->validateTypeMethodMap[$type]}($value, $rule);
		}else{
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
	private function validateNull($value, array $rule) : bool
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
	private function validateBoolean($value, array $rule) : bool
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
	private function validateScalar($value, array $rule) : bool
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
	private function validateArray($value, array $rule) : bool
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
	private function validateObject($value, array $rule) : bool
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
	private function validateString($value, array $rule) : bool
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
	private function validateInteger($value, array $rule) : bool
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
	private function validateNumeric($value, array $rule) : bool
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
	private function validateFloat($value, array $rule) : bool
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
	private function validateAlphaNumeric($value, array $rule) : bool
	{
		return ctype_alnum($value);
	}

	/**
	 * Validate that $value has alpha characters.
	 *
	 * @param mixed $value
	 * @param array $rule
	 * @return bool
	 */
	private function validateAlpha($value, array $rule) : bool
	{
		return ctype_alpha($value);
	}

	/**
	 * Validate that $value is valid email address.
	 *
	 * @param mixed $value
	 * @param array $rule
	 * @return bool
	 */
	private function validateEmail($value, array $rule) : bool
	{
		return (bool) filter_var($value, FILTER_VALIDATE_EMAIL);

	}

	/**
	 * Validate that $value is a valid date.
	 *
	 * @param mixed $value
	 * @param array $rule
	 * @return bool
	 */
	private function validateDate($value, array $rule) : bool
	{
		return (bool) false; // @todo

	}

	/**
	 * Validate that $value is valid IPv4 or IPv6
	 *
	 * @param mixed $value
	 * @param array $rule
	 * @return bool
	 */
	private function validateIP($value, array $rule) : bool
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
	private function validateIPv4($value, array $rule) : bool
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
	private function validateIPv6($value, array $rule) : bool
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
	private function validateClosure($value, array $rule) : bool
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
	private function validateCallable($value, array $rule) : bool
	{
		return is_callable($value);
	}
}