<?php declare(strict_types = 1);

namespace Contributte\Neonizer;

use Composer\IO\IOInterface;
use Contributte\Neonizer\Config\FileConfig;
use Contributte\Neonizer\Decoder\DecoderFactory;
use Contributte\Neonizer\Encoder\EncoderFactory;
use Contributte\Neonizer\File\FileManager;

class TaskProcess
{

	/** @var IOInterface */
	private $io;

	/** @var FileManager */
	private $fileManager;

	public function __construct(IOInterface $io)
	{
		$this->io = $io;
		$this->fileManager = new FileManager(new EncoderFactory(), new DecoderFactory());
	}

	public function process(FileConfig $config): void
	{
		$this->io->write(sprintf(
			'<info>%s the "%s" file</info>',
			$config->isFileExist() ? 'Updating' : 'Creating',
			$config->getFile()
		));

		$expected = $this->fileManager->loadDistFile($config);
		$actual = [];

		if ($config->isFileExist()) {
			$existingValues = $this->fileManager->loadFile($config);
			$actual = array_merge($actual, $existingValues);
		}

		$content = $this->processParams($expected, $actual);
		$this->fileManager->processFile($content, $config);
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

			// If section is array, step into recursion
			if (is_array($param)) {
				$actualSection = $actual[$key] ?? [];
				$actual[$key] = $this->processParams($param, $actualSection, $section);
				continue;
			}

			// If key exist in current file, skip it
			if (array_key_exists($key, $actual)) continue;

			// Ask for value
			$actual[$key] = $this->getParam($section, $param);
		}

		return $actual;
	}

	protected function getParam(string $param, ?string $default): ?string
	{
		if (!$this->io->isInteractive()) return $default;

		return $this->io->ask(
			sprintf('<question>%s</question> (<comment>%s</comment>): ', $param, $default),
			$default
		);
	}

}
