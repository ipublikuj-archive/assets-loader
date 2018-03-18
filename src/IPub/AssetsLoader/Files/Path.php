<?php
/**
 * Path.php
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:AssetsLoader!
 * @subpackage     Files
 * @since          1.0.0
 *
 * @date           29.12.13
 */

declare(strict_types = 1);

namespace IPub\AssetsLoader\Files;

class Path
{
	/**
	 * @param string $path
	 *
	 * @return string
	 */
	public static function normalize(string $path) : string
	{
		$path = strtr($path, '\\', '/');
		$root = (strpos($path, '/') === 0) ? '/' : '';
		$pieces = explode('/', trim($path, '/'));
		$res = [];

		foreach ($pieces as $piece) {
			if ($piece === '.' || empty($piece)) {
				continue;
			}

			if ($piece === '..') {
				array_pop($res);

			} else {
				array_push($res, $piece);
			}
		}

		return $root . implode('/', $res);
	}
}
