<?php declare(strict_types = 1);

namespace Contributte\Neonizer\Config;

use Contributte\Neonizer\Exception\Logical\InvalidArgumentException;
use Contributte\Neonizer\Utils;

class FileConfig
{

	private string $file;

	private string $distFile;

	/** @var string|NULL */
	private ?string $sourceType = null;

	/** @var string|NULL */
	private ?string $outputType = null;

	/**
	 * @param array{dist-file?: string, file?: string} $config
	 */
	public function __construct(array $config)
	{
		// Dist file
		if (!isset($config['dist-file'])) {
			throw new InvalidArgumentException(
				'The dist-file is required'
			);
		}

		$this->distFile = $config['dist-file'];
		if (!is_file($this->distFile)) {
			throw new InvalidArgumentException(
				sprintf(
					'The dist file "%s" does not exist. Check your dist-file.',
					$this->distFile
				)
			);
		}

		// File
		if (isset($config['file'])) {
			$this->file = $config['file'];
		}

		if (!isset($this->file)) {
			$this->file = Utils::removeDistExtensions($this->distFile);
		}

		// Source & output type
		if ($this->sourceType === null) {
			$this->sourceType = Utils::detectFileType($this->distFile);
		}

		if ($this->outputType === null) {
			$this->outputType = Utils::detectFileType($this->file);
		}
	}

	public function getFile(): string
	{
		return $this->file;
	}

	public function getDistFile(): string
	{
		return $this->distFile;
	}

	public function getSourceType(): ?string
	{
		return $this->sourceType;
	}

	public function getOutputType(): ?string
	{
		return $this->outputType;
	}

	public function isFileExist(): bool
	{
		return is_file($this->file);
	}

}
