<?php

namespace Contributte\Neonizer\Encoder;

class JsonEncoder implements IEncoder
{

	/**
	 * @param mixed[] $value
	 * @return string|NULL
	 */
	public function encode($value)
	{
		return json_encode($value, JSON_PRETTY_PRINT);
	}

}
