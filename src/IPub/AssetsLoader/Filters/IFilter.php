<?php
/**
 * IFilter.php
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:AssetsLoader!
 * @subpackage     Filters
 * @since          1.0.0
 *
 * @date           16.01.15
 */

declare(strict_types = 1);

namespace IPub\AssetsLoader\Filters;

interface IFilter
{
	/**
	 * Define filter types
	 */
	public const TYPE_FILES = 'files';
	public const TYPE_CONTENT = 'content';
}
