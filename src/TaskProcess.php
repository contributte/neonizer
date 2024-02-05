<?php declare(strict_types = 1);

namespace Contributte\Neonizer;

use Composer\IO\IOInterface;
use Contributte\Neonizer\Config\FileConfig;
use Contributte\Neonizer\Decoder\DecoderFactory;
use Contributte\Neonizer\Encoder\EncoderFactory;
use Contributte\Neonizer\File\FileManager;
use Nette\Neon\Entity;

class TaskProcess
{

	private IOInterface $io;

	private FileManager $fileManager;

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
	 * @param array<mixed> $expected
	 * @param array<mixed> $actual
	 * @return mixed[]
	 */
	protected function processParams(array $expected, array $actual, ?string $parentSection = null): array
	{
		foreach ($expected as $key => $param) {
			/** @var string $section */
			$section = $parentSection !== null ? $parentSection . '.' . $key : $key;

			// If section is array, step into recursion
			if (is_array($param)) {
				$actualSection = $actual[$key] ?? [];
				assert(is_array($actualSection));
				$actual[$key] = $this->processParams($param, $actualSection, $section);

				continue;
			}

			// If key exist in current file, skip it
			if (array_key_exists($key, $actual)) {
				continue;
			}

			// Ask for value
			$actual[$key] = $this->getParam($section, $param);
		}

		return $actual;
	}

	protected function getParam(string $param, mixed $default = null): mixed
	{
		$displayDefault = $default;

		if (is_bool($default)) {
			$displayDefault = $default ? 'yes' : 'no';
		} elseif ($default === null) {
			$displayDefault = 'null';
		}

		if (!$this->io->isInteractive() || $default instanceof Entity) {
			return $default;
		}

		assert(is_scalar($default) || $default === null);
		assert(is_scalar($displayDefault));

		$question = sprintf('<question>%s</question> (<comment>%s</comment>): ', $param, $displayDefault);

		return is_bool($default)
			? $this->io->askConfirmation($question, $default)
			: $this->io->ask($question, $default);
	}

}
