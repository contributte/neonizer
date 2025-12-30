<?php declare(strict_types = 1);

namespace Tests\Cases;

use Composer\IO\IOInterface;
use Contributte\Neonizer\Config\FileConfig;
use Contributte\Neonizer\TaskProcess;
use Contributte\Tester\Environment;
use Contributte\Tester\Toolkit;
use Mockery;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

// testNoInteractive
Toolkit::test(static function (): void {
	$generatedFile = Environment::getTestDir() . '/no-interactive.neon';
	$config = new FileConfig([
		'dist-file' => __DIR__ . '/../Fixtures/files/config.neon.dist',
		'file' => $generatedFile,
	]);

	$io = Mockery::mock(IOInterface::class);
	$io->shouldReceive('isInteractive')
		->times(4)
		->andReturn(false);
	$io->shouldReceive('write')
		->once();

	$processor = new TaskProcess($io);
	$processor->process($config);

	Assert::same(file_get_contents(__DIR__ . '/../Fixtures/files/no-interactive.neon'), file_get_contents($generatedFile));
});

// testInteractive
Toolkit::test(static function (): void {
	$io = Mockery::mock(IOInterface::class);
	$io->shouldReceive('isInteractive')
		->times(8)
		->andReturn(true);
	$io->shouldReceive('ask')
		->times(8)
		->andReturn('bar');
	$io->shouldReceive('write')
		->times(2);

	$processor = new TaskProcess($io);

	$generatedFile = Environment::getTestDir() . '/interactive.neon';
	$processor->process(new FileConfig([
		'dist-file' => __DIR__ . '/../Fixtures/files/config.neon.dist',
		'file' => $generatedFile,
	]));
	Assert::same(file_get_contents(__DIR__ . '/../Fixtures/files/interactive.neon'), file_get_contents($generatedFile));

	$generatedFile = Environment::getTestDir() . '/interactive.json';
	$processor->process(new FileConfig([
		'dist-file' => __DIR__ . '/../Fixtures/files/config.neon.dist',
		'file' => $generatedFile,
	]));
	Assert::same(file_get_contents(__DIR__ . '/../Fixtures/files/interactive.json'), file_get_contents($generatedFile));
});

// testEmptyFile
Toolkit::test(static function (): void {
	$io = Mockery::mock(IOInterface::class);
	$io->shouldReceive('write')
		->times(1);

	$processor = new TaskProcess($io);

	$generatedFile = Environment::getTestDir() . '/empty.neon';
	$processor->process(new FileConfig([
		'dist-file' => __DIR__ . '/../Fixtures/files/empty.neon',
		'file' => $generatedFile,
	]));
	Assert::same(file_get_contents(__DIR__ . '/../Fixtures/files/empty.neon'), file_get_contents($generatedFile));
});
