<?php declare(strict_types = 1);

namespace Tests\Cases\Contributte\Neonizer;

/**
 * Test: TaskSet
 *
 * @testCase
 */

use Composer\IO\IOInterface;
use Contributte\Neonizer\Exception\Logical\InvalidArgumentException;
use Contributte\Neonizer\TaskSet;
use Mockery;
use Mockery\MockInterface;
use Nette\Neon\Neon;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../bootstrap.php';

class TaskSetTest extends TestCase
{

	protected function tearDown(): void
	{
		Mockery::close();
	}

	public function testSetNoArgs(): void
	{
		/** @var IOInterface|MockInterface $io */
		$io = Mockery::mock(IOInterface::class);
		$io->shouldReceive('write')
			->withArgs(['<error>Configuration file is required, e.q. -- $(pwd)/app/config/config.local.neon</error>'])
			->once()
			->getMock();

		$taskSet = new TaskSet($io);
		$taskSet->set([]);

		// Tester hack
		Assert::true(true);
	}

	public function testSetNoParams(): void
	{
		/** @var IOInterface|MockInterface $io */
		$io = Mockery::mock(IOInterface::class);
		$io->shouldReceive('write')
			->withArgs(['<error>Add some parameters, e.q. -- $(pwd)/app/config/config.local.neon --database.host=localhost --database.user=neonizer</error>'])
			->once()
			->getMock();

		$taskSet = new TaskSet($io);
		$taskSet->set([__DIR__ . '/../fixtures/files/set.neon']);

		// Tester hack
		Assert::true(true);
	}

	public function testSetInvalidArgument(): void
	{
		/** @var IOInterface|MockInterface $io */
		$io = Mockery::mock(IOInterface::class);
		$taskSet = new TaskSet($io);

		Assert::throws(function () use ($taskSet): void {
			$taskSet->set([
				__DIR__ . '/../fixtures/files/set.neon',
				'invalid',
			]);
		}, InvalidArgumentException::class, 'Invalid argument "invalid" given.');
	}

	public function testSetNoChange(): void
	{
		$file = $this->createTestedFile('noChange');
		$expected = '# This file is auto-generated by composer' . PHP_EOL . Neon::encode([
				'parameters' => [
					'database' => [
						'user' => 'bar',
						'pass' => 'bar',
						'dbname' => 'bar',
					],
					'debug' => 'true',
				],
			], Neon::BLOCK);

		/** @var IOInterface|MockInterface $io */
		$io = Mockery::mock(IOInterface::class);
		$io->shouldReceive('isInteractive')
			->andReturn(true)
			->getMock();

		$taskSet = new TaskSet($io);
		$taskSet->set([
			$file,
			'--database.user=bar',
		]);

		$actual = file_get_contents($file);
		Assert::same($expected, $actual);
	}

	public function testSetChangeValue(): void
	{
		$file = $this->createTestedFile('changeValue');
		$expected = '# This file is auto-generated by composer' . PHP_EOL . Neon::encode([
				'parameters' => [
					'database' => [
						'user' => 'foo',
						'pass' => 'bar',
						'dbname' => 'bar',
					],
					'debug' => 'true',
				],
			], Neon::BLOCK);

		/** @var IOInterface|MockInterface $io */
		$io = Mockery::mock(IOInterface::class);
		$io->shouldReceive('isInteractive')
			->andReturn(true)
			->getMock();

		$taskSet = new TaskSet($io);
		$taskSet->set([
			$file,
			'--database.user=foo',
		]);

		$actual = file_get_contents($file);
		Assert::same($expected, $actual);
	}

	public function testSetAddKey(): void
	{
		$file = $this->createTestedFile('addKey');
		$expected = '# This file is auto-generated by composer' . PHP_EOL . Neon::encode([
				'parameters' => [
					'database' => [
						'host' => 'bar',
						'user' => 'bar',
						'pass' => 'bar',
						'dbname' => 'bar',
					],
					'debug' => 'true',
				],
			], Neon::BLOCK);

		/** @var IOInterface|MockInterface $io */
		$io = Mockery::mock(IOInterface::class);
		$io->shouldReceive('isInteractive')
			->andReturn(true)
			->getMock();

		$taskSet = new TaskSet($io);
		$taskSet->set([
			$file,
			'--database.host=bar',
		]);

		$actual = file_get_contents($file);
		Assert::same($expected, $actual);
	}

	public function testSetMultiple(): void
	{
		$file = $this->createTestedFile('multiple');
		$expected = '# This file is auto-generated by composer' . PHP_EOL . Neon::encode([
				'parameters' => [
					'database' => [
						'host' => 'bar',
						'user' => 'foo',
						'pass' => 'bar',
						'dbname' => 'bar',
					],
					'debug' => 'true',
				],
			], Neon::BLOCK);

		/** @var IOInterface|MockInterface $io */
		$io = Mockery::mock(IOInterface::class);
		$io->shouldReceive('isInteractive')
			->andReturn(true)
			->getMock();

		$taskSet = new TaskSet($io);
		$taskSet->set([
			$file,
			'--database.host=bar',
			'--database.user=foo',
		]);

		$actual = file_get_contents($file);
		Assert::same($expected, $actual);
	}

	private function createTestedFile(string $name): string
	{
		$neon = Neon::encode([
			'parameters' => [
				'database' => [
					'user' => 'bar',
					'pass' => 'bar',
					'dbname' => 'bar',
				],
				'debug' => 'true',
			],
		], Neon::BLOCK);
		$content = '# This file is auto-generated by composer' . PHP_EOL . $neon;
		$fileName = __DIR__ . '/../tmp/set_' . $name . '.neon';
		file_put_contents($fileName, $content);
		return $fileName;
	}

}

(new TaskSetTest())->run();
