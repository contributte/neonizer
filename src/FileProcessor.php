<?php declare(strict_types = 1);

namespace Contributte\Neonizer;

use Composer\IO\IOInterface;
use Contributte\Neonizer\Config\FileConfig;
use Contributte\Neonizer\Encoder\IEncoderFactory;

class FileProcessor
{

	/** @var IOInterface */
	private $io;

	/** @var IEncoderFactory */
	private $encoderFactory;

	/** @var FileLoader */
	private $fileLoader;

	/**
	 * @param IOInterface $io
	 * @param IEncoderFactory $encoderFactory
	 * @param FileLoader $fileLoader
	 */
	public function __construct(
		IOInterface $io,
		IEncoderFactory $encoderFactory,
		FileLoader $fileLoader
	)
	{
		$this->io = $io;
		$this->encoderFactory = $encoderFactory;
		$this->fileLoader = $fileLoader;
	}

	/**
	 * @param FileConfig $config
	 * @return void
	 */
	public function process(FileConfig $config): void
	{
		$this->io->write(sprintf(
			'<info>%s the "%s" file</info>',
			$config->isFileExist() ? 'Updating' : 'Creating',
			$config->getFile()
		));

		$expected = $this->fileLoader->loadDistFile($config);
		$actual = [];

		if ($config->isFileExist()) {
			$existingValues = $this->fileLoader->loadFile($config);
			$actual = array_merge($actual, $existingValues);
		}

		$content = $this->processParams($expected, $actual);
		$this->processFile($content, $config);
	}

	/**
	 * @param mixed[] $expected
	 * @param mixed[] $actual
	 * @param string|NULL $parentSection
	 * @return mixed[]
	 */
	protected function processParams(array $expected, array $actual, ?string $parentSection = NULL): array
	{
		foreach ($expected as $key => $param) {
			$section = $parentSection ? $parentSection . '.' . $key : $key;
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
	 * @param string|NULL $default
	 * @return string|NULL
	 */
	protected function getParam(string $param, ?string $default): ?string
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
	 * @param mixed[] $content
	 * @param FileConfig $config
	 * @return void
	 */
	protected function processFile(array $content, FileConfig $config): void
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
	protected function saveFile(string $content, string $filename): void
	{
		$dir = dirname($filename);
		if (!is_dir($dir)) {
			mkdir($dir, 0755, TRUE);
		}
		file_put_contents($filename, $content);
	}

}
