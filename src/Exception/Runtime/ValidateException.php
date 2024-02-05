<?php declare(strict_types = 1);

namespace Contributte\Neonizer\Exception\Runtime;

use Contributte\Neonizer\Exception\RuntimeException;

class ValidateException extends RuntimeException
{

	/** @var string[] */
	public array $missingKeys;

	/**
	 * @param string[] $missingKeys
	 */
	public function __construct(array $missingKeys)
	{
		parent::__construct('', 100);

		$this->missingKeys = $missingKeys;
	}

}
