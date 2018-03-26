<?php declare(strict_types = 1);

namespace Contributte\Neonizer\Decoder;

class JsonDecoder implements IDecoder
{

	/**
	 * @param string $value
	 * @return mixed[]
	 */
	public function decode(string $value): array
	{
		return json_decode($value, TRUE);
	}

}
