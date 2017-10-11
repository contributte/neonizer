<?php

namespace Contributte\Neonizer;

class Utils
{

	/** @var string[] */
	public static $extensions = ['.dist', '.template', '.tpl'];

	/**
	 * @param string $fileName
	 * @return string|NULL
	 */
	public static function detectFileType($fileName)
	{
		$fileName = self::removeDistExtensions($fileName);
		$parts = explode('.', $fileName);
		if (count($parts) < 2) {
			return NULL;
		}
		return end($parts);
	}

	/**
	 * @param string $fileName
	 * @return string
	 */
	public static function removeDistExtensions($fileName)
	{
		foreach (self::$extensions as $ext) {
			if (self::endsWith($fileName, $ext)) {
				$name = substr($fileName, 0, -strlen($ext));
				if ($name === FALSE) {
					continue;
				}
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
	public static function endsWith($haystack, $needle)
	{
		return strlen($needle) === 0 || substr($haystack, -strlen($needle)) === $needle;
	}

}
