<?php

namespace Contributte\Neonizer;

use Composer\IO\IOInterface;
use Contributte\Neonizer\Config\FileConfig;
use Contributte\Neonizer\Decoder\IDecoderFactory;
use Contributte\Neonizer\Encoder\IEncoderFactory;

class FileProcessor
{

	/** @var IOInterface */
	private $io;

	/** @var IEncoderFactory */
	private $encoderFactory;

	/** @var IDecoderFactory */
	private $decoderFactory;

	/**
	 * @param IOInterface $io
	 * @param IEncoderFactory $encoderFactory
	 * @param IDecoderFactory $decoderFactory
	 */
	public function __construct(
		IOInterface $io,
		IEncoderFactory $encoderFactory,
		IDecoderFactory $decoderFactory
	)
	{
		$this->io = $io;
		$this->encoderFactory = $encoderFactory;
		$this->decoderFactory = $decoderFactory;
	}

	/**
	 * @param FileConfig $config
	 * @return void
	 */
	public function process(FileConfig $config)
	{
		$this->io->write(sprintf(
			'<info>%s the "%s" file</info>',
			$config->isFileExist() ? 'Updating' : 'Creating',
			$config->getFile()
		));

		$expected = $this->loadDistFile($config);
		$actual = [];

		if ($config->isFileExist()) {
			$existingValues = $this->loadFile($config);
			$actual = array_merge($actual, $existingValues);
		}

		$content = $this->processParams($expected, $actual);
		$this->processFile($content, $config);
	}

	/**
	 * @param mixed[] $expected
	 * @param mixed[] $actual
	 * @param string|NULL $section
	 * @return mixed[] mixed
	 */
	protected function processParams(array $expected, array $actual, $section = NULL)
	{
		foreach ($expected as $key => $param) {
			$section = $section ? $section . '.' . $key : $key;
			if (is_array($param)) {
				$actualSection = isset($actual[$key]) ? $actual[$key] : [];
				$actual[$key] = $this->processParams($param, $actualSection, $section);
				continue;
			}
			if (array_key_exists($key, $actual)) {
				continue;
			}
			$actual[$key] = $this->getParam($section, $param);
		}

		return $actual;
	}

	/**
	 * @param string $param
	 * @param string $default
	 * @return string
	 */
	protected function getParam($param, $default)
	{
		if (!$this->io->isInteractive()) {
			return $default;
		}
		return $this->io->ask(
			sprintf('<question>%s</question> (<comment>%s</comment>): ', $param, $default),
			$default
		);
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
	 * @param mixed[] $content
	 * @param FileConfig $config
	 * @return void
	 */
	protected function processFile(array $content, FileConfig $config)
	{
		$encoder = $this->encoderFactory->create($config->getOutputType());
		$content = $encoder->encode($content);
		$this->saveFile((string) $content, $config->getFile());
	}

	/**
	 * @param string $content
	 * @param string $filename
	 * @return void
	 */
	protected function saveFile($content, $filename)
	{
		$dir = dirname($filename);
		if (!is_dir($dir)) {
			mkdir($dir, 0755, TRUE);
		}
		file_put_contents($filename, $content);
	}

}
