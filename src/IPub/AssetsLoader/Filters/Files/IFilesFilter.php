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

use IPub;
use IPub\AssetsLoader;
use IPub\AssetsLoader\Compilers;

interface IFilesFilter
{
	/**
	 * @param $code
	 * @param Compilers\Compiler $compiler
	 * @param $file
	 *
	 * @return string
	 */
	public function __invoke($code, Compilers\Compiler $compiler, $file);
}