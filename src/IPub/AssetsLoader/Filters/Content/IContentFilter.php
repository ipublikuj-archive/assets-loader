<?php
/**
 * IContentFilter.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:AssetsLoader!
 * @subpackage	Filters
 * @since		5.0
 *
 * @date		30.12.13
 */

namespace IPub\AssetsLoader\Filters\Content;

interface IContentFilter
{
	/**
	 * @param $code
	 * @param \IPub\AssetsLoader\Compilers\Compiler $loader
	 *
	 * @return string
	 */
	public function __invoke($code, \IPub\AssetsLoader\Compilers\Compiler $loader);
}