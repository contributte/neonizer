<?php declare(strict_types = 1);

namespace Contributte\Neonizer\Decoder;

class JsonDecoder implements IDecoder
{

	/**
	 * @return mixed[]
	 */
	public function decode(string $value): array
	{
		return (array) json_decode($value, true);
	}

}
