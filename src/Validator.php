<?php declare(strict_types=1);
namespace Noname\Common;

class Validator
{
	private $values;
	private $rules;

	public function __construct($values, $rules, array $settings = [])
	{
		$this->values = new Collection($values);
		$this->rules = new Collection($rules);
		$this->settings = new Collection($settings);
	}

	public function validate() : bool
	{
		foreach($this->values as $key => $value){
			if(isset($this->rules[$key])){

			}
		}
	}

	///////////////////////////////////
	// Built in validators

	private function validateScalar($value, $rule)
	{

	}

	private function validateArray($value, $rule)
	{

	}

	private function validateObject($value, $rule)
	{

	}

	private function validateBoolean($value, $rule)
	{

	}

	private function validateString($value, $rule)
	{

	}

	private function validateInteger($value, $rule)
	{

	}

	private function validateNumeric($value, $rule)
	{

	}

	private function validateFloat($value, $rule)
	{

	}

	private function validateAlphaNumeric($value, $rule)
	{

	}

	private function validateEmail($value, $rule)
	{

	}

	private function validateIPv4($value, $rule)
	{

	}

	private function validateIPv6($value, $rule)
	{

	}
	private function
}