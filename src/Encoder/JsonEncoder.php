<?php declare(strict_types = 1);

namespace Contributte\Neonizer\Encoder;

class JsonEncoder implements IEncoder
{

	/**
	 * @param mixed[] $value
	 */
	public function encode(array $value): ?string
	{
		$output = json_encode($value, JSON_PRETTY_PRINT);

		return ($output !== false) ? $output : null;
	}

}
