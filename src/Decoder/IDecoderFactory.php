<?php

namespace Contributte\Neonizer\Decoder;

interface IDecoderFactory
{

	/**
	 * @param string|NULL $type
	 * @return IDecoder
	 */
	public function create($type);

}
