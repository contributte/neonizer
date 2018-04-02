<?php

namespace Contributte\Neonizer;

use Composer\IO\IOInterface;
use Contributte\Neonizer\Config\FileConfig;
use Contributte\Neonizer\Decoder\IDecoderFactory;

class FileValidator
{

	/** @var IOInterface */
	private $io;

	/** @var IDecoderFactory */
	private $decoderFactory;

	/**
	 * @param IOInterface $io
	 * @param IDecoderFactory $decoderFactory
	 */
	public function __construct(
		IOInterface $io,
		IDecoderFactory $decoderFactory
	)
	{
		$this->io = $io;
		$this->decoderFactory = $decoderFactory;
	}

	/**
	 * @param FileConfig $config
	 * @return string[]
	 */
	public function validate(FileConfig $config)
	{
		$this->io->write(sprintf(
			'<info>Validating the "%s" file</info>',
			$config->getFile()
		));

		$expected = $this->loadDistFile($config);
		$actual = [];

		if ($config->isFileExist()) {
			$existingValues = $this->loadFile($config);
			$actual = array_merge($actual, $existingValues);
		}

		return $this->validateParams($expected, $actual);
	}

	/**
	 * @param FileConfig $config
	 * @return mixed[]
	 */
	protected function loadFile(FileConfig $config)
	{
		$decoder = $this->decoderFactory->create($config->getOutputType());
		return $decoder->decode(file_get_contents($config->getFile()));
	}

	/**
	 * @param FileConfig $config
	 * @return mixed[]
	 */
	protected function loadDistFile(FileConfig $config)
	{
		$decoder = $this->decoderFactory->create($config->getSourceType());
		return $decoder->decode(file_get_contents($config->getDistFile()));
	}

	/**
	 * @param mixed[] $expected
	 * @param mixed[] $actual
	 * @param string|NULL $parentSection
	 * @return string[]
	 */
	protected function validateParams(array $expected, array $actual, $parentSection = NULL)
	{
		$missingKeys = [];

		foreach ($expected as $key => $param) {
			$section = $parentSection ? $parentSection . '.' . $key : $key;
			if (is_array($param)) {
				$actualSection = isset($actual[$key]) ? $actual[$key] : [];
				$missingKeys = array_merge($missingKeys, $this->validateParams($param, $actualSection, $section));
				continue;
			}
			if (!array_key_exists($key, $actual)) {
				$missingKeys[] = $section;
			}
		}

		return array_unique($missingKeys);
	}

}
