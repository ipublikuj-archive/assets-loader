<?php
/**
 * IFilesFilter.php
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:AssetsLoader!
 * @subpackage     Filters
 * @since          1.0.0
 *
 * @date           29.12.13
 */

declare(strict_types = 1);

namespace IPub\AssetsLoader\Filters\Files;

use IPub\AssetsLoader\Compilers;

interface IFilesFilter
{
	/**
	 * @param string $code
	 * @param Compilers\Compiler $compiler
	 * @param string $file
	 *
	 * @return string
	 */
	public function __invoke(string $code, Compilers\Compiler $compiler, string $file) : string;
}
