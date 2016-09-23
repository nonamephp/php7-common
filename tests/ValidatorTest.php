<?php declare(strict_types=1);
namespace Noname\Common;

class ValidatorTest
{
	public function testValidate()
	{
		$values = [
			'email' => 'john.doe@example.org',
			'password' => 'p@$ssw04d'
		];

		$rules = [
			'email' => 'email',
			'password' => ['type' => 'string']
		];
	}
}