<?php declare(strict_types = 1);

namespace Contributte\Neonizer;

use Composer\IO\IOInterface;
use Contributte\Neonizer\Decoder\DecoderFactory;
use Contributte\Neonizer\Encoder\EncoderFactory;
use Contributte\Neonizer\Exception\Logical\InvalidArgumentException;
use Contributte\Neonizer\File\FileLoader;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\StringInput;

class TaskSet
{

	/** @var IOInterface */
	private $io;

	/** @var FileLoader */
	private $fileLoader;

	public function __construct(IOInterface $io)
	{
		$this->io = $io;
		$this->fileLoader = new FileLoader(new EncoderFactory(), new DecoderFactory());
	}

	/**
	 * @param mixed[] $args
	 */
	public function set(array $args): void
	{
		if (count($args) <= 0) {
			$this->io->write(sprintf('<error>Configuration file is required, e.q. -- $(pwd)/app/config/config.local.neon</error>'));
			return;
		}

		if (count($args) < 2) {
			$this->io->write(sprintf('<error>Add some parameters, e.q. -- $(pwd)/app/config/config.local.neon --database.host=localhost --database.user=neonizer</error>'));
			return;
		}

		$definition = new InputDefinition();
		$definition->addArgument(new InputArgument('file', InputArgument::REQUIRED, 'Target file'));

		foreach (array_slice($args, 1) as $argument) {
			// Parse --database.host=localhost into key and value
			preg_match('#--(.+)=(.+)#', $argument, $matches);

			// Simple validation of given parameter
			if (!$matches) throw new InvalidArgumentException(sprintf('Invalid argument "%s" given.', $argument));

			// Dynamically added option for better parsing
			$definition->addOption(new InputOption($matches[1], null, InputOption::VALUE_REQUIRED));
		}

		$input = new StringInput(implode(' ', $args));
		$input->setInteractive($this->io->isInteractive());
		$input->bind($definition);

		// Validate input file
		/** @var string $file */
		$file = $input->getArgument('file');
		if (!file_exists($file)) {
			$this->io->write(sprintf('<error>Input file "%s" does not exist</error>', $file));
			return;
		}

		$updated = [];
		foreach ($input->getOptions() as $key => $value) {
			// Black magic. It parse --database.host=localhost into $tmp array.
			// Easy to use! :-)
			parse_str('parameters[' . str_replace('.', '][', $key) . ']=' . $value, $tmp);

			// Merge user inputs
			$updated = array_merge_recursive($updated, $tmp);
		}

		$content = $this->fileLoader->loadFile($file);

		$this->fileLoader->saveFile(self::mergeTree($updated, $content), $file);
	}

	/**
	 * Nette\Arrays::mergeTree
	 *
	 * Recursively appends elements of remaining keys from the second array to the first.
	 *
	 * @param mixed[] $arr1
	 * @param mixed[] $arr2
	 * @return mixed[]
	 */
	public static function mergeTree(array $arr1, array $arr2): array
	{
		$res = $arr1 + $arr2;

		foreach (array_intersect_key($arr1, $arr2) as $k => $v) {
			if (is_array($v) && is_array($arr2[$k])) {
				$res[$k] = self::mergeTree($v, $arr2[$k]);
			}
		}

		return $res;
	}

}
