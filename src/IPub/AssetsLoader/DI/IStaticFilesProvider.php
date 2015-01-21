<?php
/**
 * IStaticFilesProvider.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:AssetsLoader!
 * @subpackage	DI
 * @since		5.0
 *
 * @date		30.12.13
 */

namespace IPub\AssetsLoader\DI;

interface IStaticFilesProvider
{
	/**
	 * Return array of static files
	 *
	 * @return array
	 */
	function getStaticFiles();
}
