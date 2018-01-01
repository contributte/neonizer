<?php

namespace Contributte\Neonizer;

use Composer\IO\IOInterface;
use Contributte\Neonizer\Config\FileConfig;

class FileValidator
{

	/** @var IOInterface */
	private $io;

	/** @var FileLoader */
	private $fileLoader;

	/**
	 * @param IOInterface $io
	 * @param FileLoader $fileLoader
	 */
	public function __construct(
		IOInterface $io,
		FileLoader $fileLoader
	)
	{
		$this->io = $io;
		$this->fileLoader = $fileLoader;
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

		$expected = $this->fileLoader->loadDistFile($config);
		$actual = [];

		if ($config->isFileExist()) {
			$existingValues = $this->fileLoader->loadFile($config);
			$actual = array_merge($actual, $existingValues);
		}

		return $this->validateParams($expected, $actual);
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
