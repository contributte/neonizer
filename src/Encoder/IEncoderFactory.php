<?php

namespace Contributte\Neonizer\Encoder;

interface IEncoderFactory
{

	/**
	 * @param string|NULL $type
	 * @return IEncoder
	 */
	public function create($type);

}
