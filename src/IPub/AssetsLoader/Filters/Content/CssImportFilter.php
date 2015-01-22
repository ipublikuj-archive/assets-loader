<?php
/**
 * CssImportFilter.php
 *
 * Fix position of @import statment in CSS files
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:AssetsLoader!
 * @subpackage	Filters
 * @since		5.0
 *
 * @date		31.12.13
 */

namespace IPub\AssetsLoader\Filters\Content;

use IPub;
use IPub\AssetsLoader;
use IPub\AssetsLoader\Compilers;
use IPub\AssetsLoader\Filters;

class CssImportFilter implements IContentFilter, Filters\IFilter
{
	/**
	 * Move import to the top of the document
	 *
	 * @param string $code
	 * @param Compilers\Compiler $compiler
	 *
	 * @return string
	 */
	public function __invoke($code, Compilers\Compiler $compiler)
	{
		// move @import rules to the top
		$regexp = '/@import[^;]+;/i';

		preg_match_all($regexp, $code, $matches);

		$code = preg_replace($regexp, '', $code);
		$code = implode('', $matches[0]) . $code;

		return $code;
	}
}