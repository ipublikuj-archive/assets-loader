<?php
/**
 * AssetCache.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:AssetsLoader!
 * @subpackage	Caching
 * @since		5.0
 *
 * @date		16.01.15
 */

namespace IPub\AssetsLoader\Caching;

use Nette;
use Nette\Caching;

use IPub;
use IPub\AssetsLoader;

class AssetCache extends Caching\Cache
{
	/**
	 * Define content constants
	 */
	const CONTENT_TYPE	= 'contentType';
	const CONTENT		= 'content';
	const ETAG			= 'Etag';

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
			self::CONTENT_TYPE	=> $item[self::CONTENT_TYPE],
			self::ETAG			=> md5($content),
			self::CONTENT		=> $content
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