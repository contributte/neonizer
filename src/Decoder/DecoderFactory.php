<?php

namespace Contributte\Neonizer\Decoder;

use Contributte\Neonizer\Exception\InvalidArgumentException;

class DecoderFactory implements IDecoderFactory
{

	/** @var string[] */
	private $decodersMap = [
		'json' => JsonDecoder::class,
		'neon' => NeonDecoder::class,
	];

	/** @var IDecoder[] */
	private $decoders = [];

	/**
	 * @param string|NULL $type
	 * @return IDecoder
	 */
	public function create($type)
	{
		if (isset($this->decoders[$type])) {
			return $this->decoders[$type];
		}

		if (isset($this->decodersMap[$type])) {
			$this->decoders[$type] = new $this->decodersMap[$type];
			return $this->create($type);
		}

		throw new InvalidArgumentException('Missing Decoder type ' . (string) $type);
	}

}
