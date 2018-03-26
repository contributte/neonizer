<?php declare(strict_types = 1);

namespace Contributte\Neonizer\Decoder;

interface IDecoderFactory
{

	/**
	 * @param string|NULL $type
	 * @return IDecoder
	 */
	public function create(?string $type): IDecoder;

}
