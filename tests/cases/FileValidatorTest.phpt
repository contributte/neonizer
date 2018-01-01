<?php

namespace Contributte\Neonizer\Tests;

/**
 * Test: FileValidator
 *
 * @testCase
 */

use Composer\IO\IOInterface;
use Contributte\Neonizer\Config\FileConfig;
use Contributte\Neonizer\Decoder\DecoderFactory;
use Contributte\Neonizer\FileLoader;
use Contributte\Neonizer\FileValidator;
use Mockery;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../bootstrap.php';

class FileValidatorTest extends TestCase
{

	/**
	 * @return void
	 */
	public function testValidator()
	{
		$config = new FileConfig([
			'dist-file' => __DIR__ . '/files/config.neon.dist',
			'file' => __DIR__ . '/files/invalid.neon',
		]);

		/** @var IOInterface $io */
		$io = Mockery::mock(IOInterface::class)
			->shouldReceive('isInteractive')
			->andReturn(FALSE)
			->getMock()
			->shouldReceive('write')
			->getMock();

		$validator = new FileValidator($io, new FileLoader(new DecoderFactory()));
		$missingKeys = $validator->validate($config);

		Assert::same(['mode', 'database.user', 'database.pass'], $missingKeys);
	}

}

(new FileValidatorTest())->run();
