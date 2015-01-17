<?php
/**
 * IFilesFilter.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:AssetsLoader!
 * @subpackage	Filters
 * @since		5.0
 *
 * @date		29.12.13
 */

namespace IPub\AssetsLoader\Filters\Files;

interface IFilesFilter
{
	/**
	 * @param $code
	 * @param \IPub\AssetsLoader\Compilers\Compiler $loader
	 * @param $file
	 *
	 * @return string
	 */
	public function __invoke($code, \IPub\AssetsLoader\Compilers\Compiler $loader, $file);
}