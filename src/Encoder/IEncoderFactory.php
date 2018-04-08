<?php declare(strict_types = 1);

namespace Contributte\Neonizer\Encoder;

interface IEncoderFactory
{

	public function create(string $type): IEncoder;

}
