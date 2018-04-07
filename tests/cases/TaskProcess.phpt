<?php declare(strict_types = 1);

namespace Contributte\Neonizer\Tests;

/**
 * Test: TaskProcess
 *
 * @testCase
 */

use Composer\IO\IOInterface;
use Contributte\Neonizer\Config\FileConfig;
use Contributte\Neonizer\TaskProcess;
use Mockery;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../bootstrap.php';

class TaskProcessTest extends TestCase
{

	protected function tearDown(): void
	{
		Mockery::close();
	}

	/**
	 * @return void
	 */
	public function testNoInteractive(): void
	{
		$generatedFile = TEMP_DIR . '/no-interactive.neon';
		$config = new FileConfig([
			'dist-file' => __DIR__ . '/../fixtures/files/config.neon.dist',
			'file' => $generatedFile,
		]);

		/** @var IOInterface $io */
		$io = Mockery::mock(IOInterface::class);
		$io->shouldReceive('isInteractive')
			->times(4)
			->andReturn(FALSE);
		$io->shouldReceive('write')
			->once();

		$processor = new TaskProcess($io);
		$processor->process($config);

		self::assertFiles(__DIR__ . '/../fixtures/files/no-interactive.neon', $generatedFile);
	}

	/**
	 * @return void
	 */
	public function testInteractive(): void
	{
		/** @var IOInterface $io */
		$io = Mockery::mock(IOInterface::class);
		$io->shouldReceive('isInteractive')
			->times(8)
			->andReturn(TRUE);
		$io->shouldReceive('ask')
			->times(8)
			->andReturn('bar');
		$io->shouldReceive('write')
			->times(2);

		$processor = new TaskProcess($io);

		$generatedFile = TEMP_DIR . '/interactive.neon';
		$processor->process(new FileConfig([
			'dist-file' => __DIR__ . '/../fixtures/files/config.neon.dist',
			'file' => $generatedFile,
		]));
		self::assertFiles(__DIR__ . '/../fixtures/files/interactive.neon', $generatedFile);

		$generatedFile = TEMP_DIR . '/interactive.json';
		$processor->process(new FileConfig([
			'dist-file' => __DIR__ . '/../fixtures/files/config.neon.dist',
			'file' => $generatedFile,
		]));
		self::assertFiles(__DIR__ . '/../fixtures/files/interactive.json', $generatedFile);
	}

	/**
	 * @param string $expected
	 * @param string $actual
	 * @return void
	 */
	private static function assertFiles(string $expected, string $actual): void
	{
		Assert::same(file_get_contents($expected), file_get_contents($actual));
	}

}

(new TaskProcessTest())->run();
