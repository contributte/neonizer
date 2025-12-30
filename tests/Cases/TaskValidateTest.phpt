<?php declare(strict_types = 1);

namespace Tests\Cases;

use Composer\IO\IOInterface;
use Contributte\Neonizer\Config\FileConfig;
use Contributte\Neonizer\Exception\Runtime\ValidateException;
use Contributte\Neonizer\TaskValidate;
use Contributte\Tester\Toolkit;
use Mockery;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

// testValidator
Toolkit::test(static function (): void {
	$config = new FileConfig([
		'dist-file' => __DIR__ . '/../Fixtures/files/config.neon.dist',
		'file' => __DIR__ . '/../Fixtures/files/invalid.neon',
	]);

	$io = Mockery::mock(IOInterface::class);
	$io->shouldReceive('write')
		->times(2);

	$validator = new TaskValidate($io);

	$e = Assert::throws(static function () use ($validator, $config): void {
		$validator->validate($config);
	}, ValidateException::class);

	Assert::same(['mode', 'database.user', 'database.pass'], $e->missingKeys);
});
