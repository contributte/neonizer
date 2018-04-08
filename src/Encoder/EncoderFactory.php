<?php declare(strict_types = 1);

namespace Contributte\Neonizer\Encoder;

use Contributte\Neonizer\Exception\Logical\InvalidArgumentException;

class EncoderFactory implements IEncoderFactory
{

	/** @var string[] */
	private $encodersMap = [
		'json' => JsonEncoder::class,
		'neon' => NeonEncoder::class,
	];

	/** @var IEncoder[] */
	private $encoders = [];

	public function create(string $type): IEncoder
	{
		if (isset($this->encoders[$type])) {
			return $this->encoders[$type];
		}

		if (isset($this->encodersMap[$type])) {
			$this->encoders[$type] = new $this->encodersMap[$type]();
			return $this->create($type);
		}

		throw new InvalidArgumentException('Unknown encoder type ' . (string) $type);
	}

}
