<?php declare(strict_types = 1);

namespace Contributte\Neonizer\File;

use Contributte\Neonizer\Decoder\IDecoder;
use Contributte\Neonizer\Decoder\IDecoderFactory;
use Contributte\Neonizer\Encoder\IEncoder;
use Contributte\Neonizer\Encoder\IEncoderFactory;
use Contributte\Neonizer\Exception\Logical\InvalidStateException;
use Contributte\Neonizer\Utils;

final class FileLoader
{

	/** @var IEncoderFactory */
	private $encoderFactory;

	/** @var IDecoderFactory */
	private $decoderFactory;

	/**
	 * @param IEncoderFactory $encoderFactory
	 * @param IDecoderFactory $decoderFactory
	 */
	public function __construct(
		IEncoderFactory $encoderFactory,
		IDecoderFactory $decoderFactory
	)
	{
		$this->encoderFactory = $encoderFactory;
		$this->decoderFactory = $decoderFactory;
	}

	/**
	 * @return mixed[]
	 */
	public function loadFile(string $filename): array
	{
		$type = Utils::detectFileType($filename);
		if (!$type) {
			throw new InvalidStateException('Unsupported file type');
		}

		return $this->decodeFile($filename, $this->decoderFactory->create($type));
	}

	/**
	 * @return mixed[]
	 */
	public function decodeFile(string $filename, IDecoder $decoder): array
	{
		return $decoder->decode(file_get_contents($filename));
	}

	/**
	 * @param mixed[] $data
	 */
	public function saveFile(array $data, string $filename): void
	{
		$type = Utils::detectFileType($filename);
		if (!$type) {
			throw new InvalidStateException('Unsupported file type');
		}

		$content = $this->encodeFile($data, $this->encoderFactory->create($type));
		Utils::saveFile($filename, $content);
	}

	/**
	 * @param mixed[] $data
	 */
	public function encodeFile(array $data, IEncoder $decoder): string
	{
		return $decoder->encode($data);
	}

}
