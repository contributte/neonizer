<?php

namespace Contributte\Neonizer\Encoder;

use Nette\Neon\Neon;

class NeonEncoder implements IEncoder
{

	/**
	 * @param mixed[] $value
	 * @return string|NULL
	 */
	public function encode($value)
	{
		return '# ' . IEncoder::GENERATED_MESSAGE . "\n" . Neon::encode($value, Neon::BLOCK);
	}

}
