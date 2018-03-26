<?php declare(strict_types = 1);

namespace Contributte\Neonizer\Decoder;

interface IDecoder
{

	/**
	 * @param string $value
	 * @return mixed[]
	 */
	public function decode(string $value): array;

}
