<?php declare(strict_types = 1);

namespace Contributte\Neonizer\Encoder;

class JsonEncoder implements IEncoder
{

	/**
	 * @param mixed[] $value
	 * @return string|NULL
	 */
	public function encode(array $value): ?string
	{
		return json_encode($value, JSON_PRETTY_PRINT);
	}

}
