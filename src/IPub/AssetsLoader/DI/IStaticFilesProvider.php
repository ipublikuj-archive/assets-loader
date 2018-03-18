<?php
/**
 * IStaticFilesProvider.php
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:AssetsLoader!
 * @subpackage     DI
 * @since          1.0.0
 *
 * @date           30.12.13
 */

declare(strict_types = 1);

namespace IPub\AssetsLoader\DI;

interface IStaticFilesProvider
{
	/**
	 * Return array of static files
	 *
	 * @return array
	 */
	function getStaticFiles() : array;
}
