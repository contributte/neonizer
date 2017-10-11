<?php

namespace Contributte\Neonizer\Decoder;

use Nette\Neon\Neon;

class NeonDecoder implements IDecoder
{

	/**
	 * @param string $value
	 * @return mixed[]
	 */
	public function decode($value)
	{
		return Neon::decode($value);
	}

}
