<?php declare(strict_types = 1);

namespace Contributte\Neonizer\Encoder;

use Nette\Neon\Neon;

class NeonEncoder implements IEncoder
{

	/**
	 * @param mixed[] $value
	 * @return string|NULL
	 */
	public function encode(array $value): ?string
	{
		return '# ' . IEncoder::GENERATED_MESSAGE . "\n" . Neon::encode($value, Neon::BLOCK);
	}

}
