<?php declare(strict_types = 1);

namespace Contributte\Neonizer;

class Utils
{

	/** @var string[] */
	public static $extensions = ['.dist', '.template', '.tpl'];

	/**
	 * @param string $fileName
	 * @return string|NULL
	 */
	public static function detectFileType(string $fileName): ?string
	{
		$fileName = self::removeDistExtensions($fileName);
		$parts = explode('.', $fileName);
		if (count($parts) < 2) return NULL;

		return end($parts);
	}

	/**
	 * @param string $fileName
	 * @return string
	 */
	public static function removeDistExtensions(string $fileName): string
	{
		foreach (self::$extensions as $ext) {
			if (self::endsWith($fileName, $ext)) {
				$name = substr($fileName, 0, -strlen($ext));
				if ($name === FALSE) continue;
				$fileName = $name;
			}
		}

		return $fileName;
	}

	/**
	 * @param string $haystack
	 * @param string $needle
	 * @return bool
	 */
	public static function endsWith(string $haystack, string $needle): bool
	{
		return strlen($needle) === 0 || substr($haystack, -strlen($needle)) === $needle;
	}

}
