<?php declare(strict_types = 1);

namespace Contributte\Neonizer;

use Composer\Script\Event;
use Contributte\Neonizer\Config\FileConfig;
use Contributte\Neonizer\Exception\Logical\InvalidStateException;

class TaskManager
{

	public function process(Event $event): void
	{
		$extras = $event->getComposer()->getPackage()->getExtra();
		$files = $extras['neonizer']['files'];

		$task = new TaskProcess($event->getIO());
		foreach ($files as $config) {
			if (!is_array($config)) {
				throw new InvalidStateException('The extra.neonizer.files setting must be an array of configuration objects.');
			}

			$task->process(new FileConfig($config));
		}
	}

	public function validate(Event $event): void
	{
		$extras = $event->getComposer()->getPackage()->getExtra();
		$files = $extras['neonizer']['files'];

		$task = new TaskValidate($event->getIO());
		foreach ($files as $config) {
			if (!is_array($config)) {
				throw new InvalidStateException('The extra.neonizer.files setting must be an array of configuration objects.');
			}

			$task->validate(new FileConfig($config));
		}
	}

	public function set(Event $event): void
	{
		$task = new TaskSet($event->getIO());
		$task->set($event->getArguments());
	}

}
