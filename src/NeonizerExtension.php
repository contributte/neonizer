<?php declare(strict_types = 1);

namespace Contributte\Neonizer;

use Composer\Script\Event;
use Contributte\Neonizer\Exception\Logical\InvalidStateException;
use Contributte\Neonizer\Exception\RuntimeException;
use Throwable;

class NeonizerExtension
{

	public static function process(Event $event): void
	{
		self::ensure($event);

		try {
			$tm = new TaskManager();
			$tm->process($event);
		} catch (Throwable $e) {
			self::exception($e);
		}
	}

	public static function validate(Event $event): void
	{
		self::ensure($event);

		try {
			$tm = new TaskManager();
			$tm->validate($event);
		} catch (Throwable $e) {
			self::exception($e);
		}
	}

	public static function set(Event $event): void
	{
		try {
			$tm = new TaskManager();
			$tm->set($event);
		} catch (Throwable $e) {
			self::exception($e);
		}
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

	protected static function exception(Throwable $e): void
	{
		// Exit this script with non-zero status
		// in case of our exception
		if ($e instanceof RuntimeException) {
			$code = $e->getCode() > 0 ? $e->getCode() : 99;
			exit($code);
		}

		throw $e;
	}

}
