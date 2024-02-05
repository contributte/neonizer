<?php declare(strict_types = 1);

namespace Tests\Cases;

use Composer\IO\IOInterface;
use Contributte\Neonizer\Config\FileConfig;
use Contributte\Neonizer\TaskProcess;
use Contributte\Tester\Environment;
use Mockery;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../bootstrap.php';

class TaskProcessTest extends TestCase
{

	public function testNoInteractive(): void
	{
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

		self::assertFiles(__DIR__ . '/../Fixtures/files/no-interactive.neon', $generatedFile);
	}

	public function testInteractive(): void
	{
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
		self::assertFiles(__DIR__ . '/../Fixtures/files/interactive.neon', $generatedFile);

		$generatedFile = Environment::getTestDir() . '/interactive.json';
		$processor->process(new FileConfig([
			'dist-file' => __DIR__ . '/../Fixtures/files/config.neon.dist',
			'file' => $generatedFile,
		]));
		self::assertFiles(__DIR__ . '/../Fixtures/files/interactive.json', $generatedFile);
	}

	public function testEmptyFile(): void
	{
		$io = Mockery::mock(IOInterface::class);
		$io->shouldReceive('write')
			->times(1);

		$processor = new TaskProcess($io);

		$generatedFile = Environment::getTestDir() . '/empty.neon';
		$processor->process(new FileConfig([
			'dist-file' => __DIR__ . '/../Fixtures/files/empty.neon',
			'file' => $generatedFile,
		]));
		self::assertFiles(__DIR__ . '/../Fixtures/files/empty.neon', $generatedFile);
	}

	private static function assertFiles(string $expected, string $actual): void
	{
		Assert::same(file_get_contents($expected), file_get_contents($actual));
	}

}

(new TaskProcessTest())->run();
