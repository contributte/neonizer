<?php declare(strict_types = 1);

namespace Contributte\Neonizer\Decoder;

interface IDecoder
{

	/**
	 * @return mixed[]
	 */
	public function decode(string $value): array;

}
