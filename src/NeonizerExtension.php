<?php declare(strict_types = 1);

namespace Contributte\Neonizer;

use Composer\Script\Event;
use Contributte\Neonizer\Exception\Logical\InvalidStateException;

class NeonizerExtension
{

	/**
	 * @param Event $event
	 * @return void
	 */
	public static function process(Event $event): void
	{
		self::ensure($event);

		$tm = new TaskManager();
		$tm->process($event);
	}

	/**
	 * @param Event $event
	 * @return void
	 */
	public static function validate(Event $event): void
	{
		self::ensure($event);

		$tm = new TaskManager();
		$tm->validate($event);
	}

	protected static function ensure(Event $event): void
	{
		$extras = $event->getComposer()->getPackage()->getExtra();

		if (!isset($extras['neonizer'])) {
			$event->getIO()->write('Missing section extra.neonizer in composer file.');
			return;
		}

		if (!isset($extras['neonizer']['files'])) {
			$event->getIO()->write('Missing files attribute in extra.neonizerin in composer file.');
			return;
		}

		$files = $extras['neonizer']['files'];

		if (!is_array($files)) {
			throw new InvalidStateException('The extra.neonizer.files setting must be an array.');
		}
	}

}
