<?php declare(strict_types = 1);

namespace Contributte\Neonizer;

class Utils
{

	/** @var string[] */
	public static $extensions = ['.dist', '.template', '.tpl'];

	public static function detectFileType(string $fileName): ?string
	{
		$fileName = self::removeDistExtensions($fileName);
		$parts = explode('.', $fileName);
		if (count($parts) < 2) return null;

		return end($parts);
	}

	public static function removeDistExtensions(string $fileName): string
	{
		foreach (self::$extensions as $ext) {
			if (self::endsWith($fileName, $ext)) {
				$name = substr($fileName, 0, -strlen($ext));
				if ($name === false) continue;
				$fileName = $name;
			}
		}

		return $fileName;
	}

	public static function endsWith(string $haystack, string $needle): bool
	{
		return strlen($needle) === 0 || substr($haystack, -strlen($needle)) === $needle;
	}

	public static function saveFile(string $filename, string $data, int $mode = 0755): void
	{
		$dir = dirname($filename);
		if (!is_dir($dir)) {
			mkdir($dir, $mode, true);
		}
		file_put_contents($filename, $data);
	}

}
