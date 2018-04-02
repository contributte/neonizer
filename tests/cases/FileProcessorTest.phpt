<?php declare(strict_types = 1);

namespace Contributte\Neonizer\Tests;

/**
 * Test: FileProcessor
 *
 * @testCase
 */

use Composer\IO\IOInterface;
use Contributte\Neonizer\Config\FileConfig;
use Contributte\Neonizer\Decoder\DecoderFactory;
use Contributte\Neonizer\Encoder\EncoderFactory;
use Contributte\Neonizer\FileProcessor;
use Mockery;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../bootstrap.php';

class FileProcessorTest extends TestCase
{

	/**
	 * @return void
	 */
	public function testNoInteractive(): void
	{
		$generatedFile = __DIR__ . '/../tmp/files/no-interactive.neon';
		$config = new FileConfig([
			'dist-file' => __DIR__ . '/files/config.neon.dist',
			'file' => $generatedFile,
		]);

		/** @var IOInterface $io */
		$io = Mockery::mock(IOInterface::class)
			->shouldReceive('isInteractive')
			->andReturn(FALSE)
			->getMock()
			->shouldReceive('write')
			->getMock();

		$processor = new FileProcessor($io, new EncoderFactory(), new DecoderFactory());
		$processor->process($config);

		self::assertFiles(__DIR__ . '/files/no-interactive.neon', $generatedFile);
	}

	/**
	 * @return void
	 */
	public function testInteractive(): void
	{
		/** @var IOInterface $io */
		$io = Mockery::mock(IOInterface::class)
			->shouldReceive('isInteractive')
			->andReturn(TRUE)
			->getMock()
			->shouldReceive('ask')
			->andReturn('bar')
			->getMock()
			->shouldReceive('write')
			->getMock();
		$processor = new FileProcessor($io, new EncoderFactory(), new DecoderFactory());

		$generatedFile = __DIR__ . '/../tmp/files/interactive.neon';
		$processor->process(new FileConfig([
			'dist-file' => __DIR__ . '/files/config.neon.dist',
			'file' => $generatedFile,
		]));
		self::assertFiles(__DIR__ . '/files/interactive.neon', $generatedFile);

		$generatedFile = __DIR__ . '/../tmp/files/interactive.json';
		$processor->process(new FileConfig([
			'dist-file' => __DIR__ . '/files/config.neon.dist',
			'file' => $generatedFile,
		]));
		self::assertFiles(__DIR__ . '/files/interactive.json', $generatedFile);
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

(new FileProcessorTest())->run();
