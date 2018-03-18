<?php
/**
 * IContentFilter.php
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:AssetsLoader!
 * @subpackage     Filters
 * @since          1.0.0
 *
 * @date           30.12.13
 */

declare(strict_types = 1);

namespace IPub\AssetsLoader\Filters\Content;

use IPub\AssetsLoader\Compilers;

interface IContentFilter
{
	/**
	 * @param string $code
	 * @param Compilers\Compiler $loader
	 *
	 * @return string
	 */
	public function __invoke(string $code, Compilers\Compiler $loader) : string;
}
