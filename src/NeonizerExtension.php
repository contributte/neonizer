<?php declare(strict_types = 1);

namespace Contributte\Neonizer;

use Composer\IO\IOInterface;
use Composer\Script\Event;
use Contributte\Neonizer\Config\FileConfig;
use Contributte\Neonizer\Decoder\DecoderFactory;
use Contributte\Neonizer\Encoder\EncoderFactory;
use Contributte\Neonizer\Exception\InvalidArgumentException;
use Contributte\Neonizer\Exception\InvalidConfigurationException;

class NeonizerExtension
{

	/**
	 * @param Event $event
	 * @return void
	 */
	public static function process(Event $event): void
	{
		$extras = $event->getComposer()->getPackage()->getExtra();
		$fileConfigs = self::extractFileConfigs($event->getIO(), $extras);

		$processor = new FileProcessor($event->getIO(), new EncoderFactory(), new DecoderFactory());
		foreach ($fileConfigs as $fileConfig) {
			$processor->process($fileConfig);
		}
	}

	/**
	 * @param Event $event
	 * @return void
	 */
	public static function validate(Event $event)
	{
		$extras = $event->getComposer()->getPackage()->getExtra();
		$fileConfigs = self::extractFileConfigs($event->getIO(), $extras);

		$validator = new FileValidator($event->getIO(), new DecoderFactory());
		foreach ($fileConfigs as $fileConfig) {
			$missingKeys = $validator->validate($fileConfig);
			if (!empty($missingKeys)) {
				$event->getIO()->write(sprintf(
					'The following keys are missing in configuration file "%s": %s',
					$fileConfig->getFile(),
					implode(', ', $missingKeys)
				));

				// throw exception so that the script exits with non-zero code
				throw new InvalidConfigurationException();
			}
		}
	}

	/**
	 * @param IOInterface $io
	 * @param array $extras
	 * @return FileConfig[]
	 * @throws InvalidArgumentException
	 */
	private static function extractFileConfigs(IOInterface $io, array $extras)
	{
		if (!isset($extras['neonizer'])) {
			$io->write('Neonizer is active, but configuration missing.');
			return [];
		}
		if (!isset($extras['neonizer']['files'])) {
			$io->write('Neonizer is active, but files configuration missing.');
		}
		$fileConfigs = $extras['neonizer']['files'];
		if (!is_array($fileConfigs)) {
			throw new InvalidArgumentException('The extra.neonizer.files setting must be an array.');
		}

		return array_map(function ($config) {
			if (!is_array($config)) {
				throw new InvalidArgumentException('The extra.neonizer.files setting must be an array of configuration objects.');
			}

			return new FileConfig($config);
		}, $fileConfigs);
	}

}
