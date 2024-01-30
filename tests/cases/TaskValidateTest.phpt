<?php declare(strict_types = 1);

namespace Tests\Cases\Contributte\Neonizer;

/**
 * Test: TaskValidate
 *
 * @testCase
 */

use Composer\IO\IOInterface;
use Contributte\Neonizer\Config\FileConfig;
use Contributte\Neonizer\Exception\Runtime\ValidateException;
use Contributte\Neonizer\TaskValidate;
use Mockery;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../bootstrap.php';

class TaskValidateTest extends TestCase
{

	protected function tearDown(): void
	{
		Mockery::close();
	}

	public function testValidator(): void
	{
		$config = new FileConfig([
			'dist-file' => __DIR__ . '/../fixtures/files/config.neon.dist',
			'file' => __DIR__ . '/../fixtures/files/invalid.neon',
		]);

		/** @var IOInterface $io */
		$io = Mockery::mock(IOInterface::class);
		$io->shouldReceive('write')
			->times(2);

		$validator = new TaskValidate($io);

		$e = Assert::throws(function () use ($validator, $config): void {
			$validator->validate($config);
		}, ValidateException::class);

		Assert::same(['debug', 'database.user', 'database.pass'], $e->missingKeys);
	}

}

(new TaskValidateTest())->run();
