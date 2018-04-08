<?php declare(strict_types = 1);

namespace Contributte\Neonizer\Decoder;

interface IDecoderFactory
{

	public function create(string $type): IDecoder;

}
