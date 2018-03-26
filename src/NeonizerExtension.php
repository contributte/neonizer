<?php declare(strict_types = 1);

namespace Contributte\Neonizer;

use Composer\Script\Event;
use Contributte\Neonizer\Config\FileConfig;
use Contributte\Neonizer\Decoder\DecoderFactory;
use Contributte\Neonizer\Encoder\EncoderFactory;
use Contributte\Neonizer\Exception\InvalidArgumentException;

class NeonizerExtension
{

	/**
	 * @param Event $event
	 * @return void
	 */
	public static function process(Event $event): void
	{
		$extras = $event->getComposer()->getPackage()->getExtra();
		if (!isset($extras['neonizer'])) {
			$event->getIO()->write('Neonizer is active, but configuration missing.');
			return;
		}
		if (!isset($extras['neonizer']['files'])) {
			$event->getIO()->write('Neonizer is active, but files configuration missing.');
			return;
		}
		$fileConfigs = $extras['neonizer']['files'];
		if (!is_array($fileConfigs)) {
			throw new InvalidArgumentException('The extra.neonizer.files setting must be an array.');
		}
		$processor = new FileProcessor($event->getIO(), new EncoderFactory(), new DecoderFactory());
		foreach ($fileConfigs as $config) {
			if (!is_array($config)) {
				throw new InvalidArgumentException('The extra.neonizer.files setting must be an array of configuration objects.');
			}
			$processor->process(new FileConfig($config));
		}
	}

}
