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

	/** @var IOInterface */
	private $io;

	/** @var FileManager */
	private $fileManager;

	/**
	 * @param IOInterface $io
	 */
	public function __construct(IOInterface $io)
	{
		$this->io = $io;
		$this->fileManager = new FileManager(new EncoderFactory(), new DecoderFactory());
	}

	/**
	 * @param FileConfig $config
	 * @return void
	 */
	public function validate(FileConfig $config): void
	{
		if (!$config->isFileExist()) {
			$this->io->write(sprintf('Cannot validate "%s", file does not exist'));
			return;
		}

		$this->io->write(sprintf(
			'<info>Validating "%s" file</info>',
			$config->getFile()
		));

		$expected = $this->fileManager->loadDistFile($config);
		$actual = $this->fileManager->loadFile($config);

		$missingKeys = $this->validateParams($expected, $actual);

		if (!empty($missingKeys)) {
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
	 * @param mixed[] $expected
	 * @param mixed[] $actual
	 * @param string|NULL $parentSection
	 * @return string[]
	 */
	protected function validateParams(array $expected, array $actual, ?string $parentSection = NULL): array
	{
		$missingKeys = [];

		foreach ($expected as $key => $param) {
			$section = $parentSection ? $parentSection . '.' . $key : $key;
			if (is_array($param)) {
				$actualSection = $actual[$key] ?? [];
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
