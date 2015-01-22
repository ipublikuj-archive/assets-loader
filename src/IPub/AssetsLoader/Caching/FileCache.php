<?php
/**
 * FileCache.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:AssetsLoader!
 * @subpackage	Caching
 * @since		5.0
 *
 * @date		22.01.15
 */

namespace IPub\AssetsLoader\Caching;

use Nette;
use Nette\Caching;

use IPub;
use IPub\AssetsLoader;

class FileCache extends Caching\Cache
{
	/**
	 * Define content constants
	 */
	const CONTENT	= 'content';
	const ETAG		= 'Etag';

	/**
	 * Retrieves the specified item from the cache or NULL if the key is not found.
	 *
	 * @param string $key
	 *
	 * @return array|NULL
	 */
	public function getItem($key)
	{
		// Load item from cache storage
		$item = $this->load($key);

		// Get content string
		$content = $item[self::CONTENT];

		return [
			self::CONTENT	=> $content,
			self::ETAG		=> md5($content),
		];
	}

	/**
	 * Remove all items cached by extension
	 *
	 * @param array $conditions
	 */
	public function clean(array $conditions = NULL)
	{
		parent::clean([self::TAGS => ['ipub.assetsloader']]);
	}
}