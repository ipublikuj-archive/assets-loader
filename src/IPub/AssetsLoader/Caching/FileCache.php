<?php
/**
 * FileCache.php
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:AssetsLoader!
 * @subpackage     Caching
 * @since          1.0.0
 *
 * @date           22.01.15
 */

declare(strict_types = 1);

namespace IPub\AssetsLoader\Caching;

use Nette\Caching;

class FileCache extends Caching\Cache
{
	/**
	 * Define content constants
	 */
	public const CONTENT = 'content';
	public const ETAG = 'Etag';

	/**
	 * Retrieves the specified item from the cache or NULL if the key is not found.
	 *
	 * @param string $key
	 *
	 * @return array
	 */
	public function getItem(string $key) : array
	{
		// Load item from cache storage
		$item = $this->load($key);

		// Get content string
		$content = $item[self::CONTENT];

		return [
			self::CONTENT => $content,
			self::ETAG    => md5($content),
		];
	}

	/**
	 * Remove all items cached by extension
	 *
	 * @param array $conditions
	 *
	 * @return void
	 */
	public function clean(array $conditions = NULL) : void
	{
		parent::clean([self::TAGS => ['ipub.assetsloader']]);
	}
}