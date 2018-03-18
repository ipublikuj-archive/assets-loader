<?php
/**
 * CssImportFilter.php
 *
 * Fix position of @import statment in CSS files
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:AssetsLoader!
 * @subpackage     Filters
 * @since          1.0.0
 *
 * @date           31.12.13
 */

declare(strict_types = 1);

namespace IPub\AssetsLoader\Filters\Content;

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
	public function __invoke(string $code, Compilers\Compiler $compiler) : string
	{
		// move @import rules to the top
		$regexp = '/@import[^;]+;/i';

		preg_match_all($regexp, $code, $matches);

		$code = preg_replace($regexp, '', $code);
		$code = implode('', $matches[0]) . $code;

		return $code;
	}
}
