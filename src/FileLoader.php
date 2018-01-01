<?php

namespace Contributte\Neonizer;

use Contributte\Neonizer\Config\FileConfig;
use Contributte\Neonizer\Decoder\IDecoderFactory;

class FileLoader
{

	/** @var IDecoderFactory */
	private $decoderFactory;

	/**
	 * @param IDecoderFactory $decoderFactory
	 */
	public function __construct(IDecoderFactory $decoderFactory)
	{
		$this->decoderFactory = $decoderFactory;
	}

	/**
	 * @param FileConfig $config
	 * @return mixed[]
	 */
	public function loadFile(FileConfig $config)
	{
		$decoder = $this->decoderFactory->create($config->getOutputType());
		return $decoder->decode(file_get_contents($config->getFile()));
	}

	/**
	 * @param FileConfig $config
	 * @return mixed[]
	 */
	public function loadDistFile(FileConfig $config)
	{
		$decoder = $this->decoderFactory->create($config->getSourceType());
		return $decoder->decode(file_get_contents($config->getDistFile()));
	}

}
