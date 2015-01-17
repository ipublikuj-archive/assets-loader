<?php
/**
 * IFilter.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:AssetsLoader!
 * @subpackage	Filters
 * @since		5.0
 *
 * @date		16.01.15
 */

namespace IPub\AssetsLoader\Filters;

interface IFilter
{
	/**
	 * Define filter types
	 */
	const TYPE_FILES	= 'files';
	const TYPE_CONTENT	= 'content';
}