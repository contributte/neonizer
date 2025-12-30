<?php declare(strict_types = 1);

namespace Contributte\Neonizer;

use Composer\Script\Event;
use Contributte\Neonizer\Config\FileConfig;

class TaskManager
{

	public function process(Event $event): void
	{
		/** @var array{neonizer: array{files: mixed[]}} $extras */
		$extras = $event->getComposer()->getPackage()->getExtra();
		/** @var array<array{dist-file?: string, file?: string}> $files */
		$files = $extras['neonizer']['files'];

		$task = new TaskProcess($event->getIO());
		foreach ($files as $config) {
			$task->process(new FileConfig($config));
		}
	}

	public function validate(Event $event): void
	{
		/** @var array{neonizer: array{files: mixed[]}} $extras */
		$extras = $event->getComposer()->getPackage()->getExtra();
		/** @var array<array{dist-file?: string, file?: string}> $files */
		$files = $extras['neonizer']['files'];

		$task = new TaskValidate($event->getIO());
		foreach ($files as $config) {
			$task->validate(new FileConfig($config));
		}
	}

	public function set(Event $event): void
	{
		$task = new TaskSet($event->getIO());
		$task->set($event->getArguments());
	}

}
