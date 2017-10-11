<?php

namespace Contributte\Neonizer\Decoder;

interface IDecoder
{

	/**
	 * @param string $value
	 * @return mixed[]
	 */
	public function decode($value);

}
