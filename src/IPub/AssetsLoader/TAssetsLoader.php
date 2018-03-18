<?php
/**
 * TAssetsLoader.php
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:AssetsLoader!
 * @subpackage     common
 * @since          1.0.0
 *
 * @date           27.01.15
 */

declare(strict_types = 1);

namespace IPub\AssetsLoader;

trait TAssetsLoader
{
	/**
	 * @var LoaderFactory
	 */
	protected $assetsLoader;

	/**
	 * @param LoaderFactory $assetsLoader
	 */
	public function injectAssetsLoader(LoaderFactory $assetsLoader) : void
	{
		$this->assetsLoader = $assetsLoader;
	}
}
