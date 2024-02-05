<?php declare(strict_types = 1);

namespace Contributte\Neonizer;

use Composer\IO\IOInterface;
use Contributte\Neonizer\Config\FileConfig;
use Contributte\Neonizer\Decoder\DecoderFactory;
use Contributte\Neonizer\Encoder\EncoderFactory;
use Contributte\Neonizer\Exception\Runtime\ValidateException;
use Contributte\Neonizer\File\FileManager;

class TaskValidate
{

	private IOInterface $io;

	private FileManager $fileManager;

	public function __construct(IOInterface $io)
	{
		$this->io = $io;
		$this->fileManager = new FileManager(new EncoderFactory(), new DecoderFactory());
	}

	public function validate(FileConfig $config): void
	{
		if (!$config->isFileExist()) {
			$this->io->write(sprintf('Cannot validate "%s", file does not exist', $config->getFile()));

			return;
		}

		$this->io->write(sprintf(
			'<info>Validating "%s" file</info>',
			$config->getFile()
		));

		$expected = $this->fileManager->loadDistFile($config);
		$actual = $this->fileManager->loadFile($config);

		$missingKeys = $this->validateParams($expected, $actual);

		if ($missingKeys !== []) {
			$this->io->write(sprintf(
				'<error>The following keys are missing in configuration file "%s": %s</error>',
				$config->getFile(),
				implode(', ', $missingKeys)
			));

			// throw exception so that the script exits with non-zero code
			throw new ValidateException($missingKeys);
		} else {
			$this->io->write(sprintf(
				'<info>File "%s" OK.</info>',
				$config->getFile()
			));
		}
	}

	/**
	 * @param array<mixed> $expected
	 * @param array<mixed> $actual
	 * @return string[]
	 */
	protected function validateParams(array $expected, array $actual, ?string $parentSection = null): array
	{
		$missingKeys = [];

		foreach ($expected as $key => $param) {
			/** @var string $section */
			$section = $parentSection !== null ? $parentSection . '.' . $key : $key;
			if (is_array($param)) {
				$actualSection = $actual[$key] ?? [];
				assert(is_array($actualSection));
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
