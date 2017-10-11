<?php

namespace Contributte\Neonizer\Decoder;

class JsonDecoder implements IDecoder
{

	/**
	 * @param string $value
	 * @return mixed[]
	 */
	public function decode($value)
	{
		return json_decode($value, TRUE);
	}

}
