<?php declare(strict_types = 1);

namespace Contributte\Neonizer\File;

use Contributte\Neonizer\Config\FileConfig;
use Contributte\Neonizer\Decoder\IDecoderFactory;
use Contributte\Neonizer\Encoder\IEncoderFactory;
use Contributte\Neonizer\Exception\Logical\InvalidStateException;
use Contributte\Neonizer\Utils;

final class FileManager
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
	 * @param FileConfig $config
	 * @return mixed[]
	 */
	public function loadFile(FileConfig $config): array
	{
		if (empty($config->getOutputType())) {
			throw new InvalidStateException('Invalid file output type');
		}

		$decoder = $this->decoderFactory->create($config->getOutputType());

		return $decoder->decode(file_get_contents($config->getFile()));
	}

	/**
	 * @param FileConfig $config
	 * @return mixed[]
	 */
	public function loadDistFile(FileConfig $config): array
	{
		if (empty($config->getSourceType())) {
			throw new InvalidStateException('Invalid file source type');
		}

		$decoder = $this->decoderFactory->create($config->getSourceType());

		return $decoder->decode(file_get_contents($config->getDistFile()));
	}

	/**
	 * @param mixed[] $content
	 * @param FileConfig $config
	 * @return void
	 */
	public function processFile(array $content, FileConfig $config): void
	{
		if (empty($config->getOutputType())) {
			throw new InvalidStateException('Invalid file output type');
		}

		$encoder = $this->encoderFactory->create($config->getOutputType());
		$content = $encoder->encode($content);

		Utils::saveFile($config->getFile(), (string) $content);
	}

}
