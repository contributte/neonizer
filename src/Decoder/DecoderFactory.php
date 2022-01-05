<?php declare(strict_types = 1);

namespace Contributte\Neonizer\Decoder;

use Contributte\Neonizer\Exception\Logical\InvalidArgumentException;

class DecoderFactory implements IDecoderFactory
{

	/** @var array<string, class-string<IDecoder>> **/
	private $decodersMap = [
		'json' => JsonDecoder::class,
		'neon' => NeonDecoder::class,
	];

	/** @var IDecoder[] */
	private $decoders = [];

	public function create(string $type): IDecoder
	{
		if (isset($this->decoders[$type])) {
			return $this->decoders[$type];
		}

		if (isset($this->decodersMap[$type])) {
			$this->decoders[$type] = new $this->decodersMap[$type]();
			return $this->create($type);
		}

		throw new InvalidArgumentException('Unknown decoder type ' . $type);
	}

}
