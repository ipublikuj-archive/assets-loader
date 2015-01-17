<?php
/**
 * Path.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:AssetsLoader!
 * @subpackage	Files
 * @since		5.0
 *
 * @date		29.12.13
 */

namespace IPub\AssetsLoader\Files;

class Path
{
	public static function normalize($path)
	{
		$path	= strtr($path, '\\', '/');
		$root	= (strpos($path, '/') === 0) ? '/' : '';
		$pieces	= explode('/', trim($path, '/'));
		$res	= array();

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