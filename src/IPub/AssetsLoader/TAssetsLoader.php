<?php
/**
 * TAssetsLoader.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:AssetsLoader!
 * @subpackage	common
 * @since		5.0
 *
 * @date		27.01.15
 */

namespace IPub\AssetsLoader;

use Nette;
use Nette\Application;

use IPub;

trait TAssetsLoader
{
	/**
	 * @var LoaderFactory
	 */
	protected $assetsLoader;

	/**
	 * @param LoaderFactory $assetsLoader
	 */
	public function injectAssetsLoader(LoaderFactory $assetsLoader) {
		$this->assetsLoader = $assetsLoader;
	}
}