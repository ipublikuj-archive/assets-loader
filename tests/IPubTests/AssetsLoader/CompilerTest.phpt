<?php
/**
 * Test: IPub\AssetsLoader\Compiler
 * @testCase
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:AssetsLoader!
 * @subpackage     Tests
 * @since          1.0.0
 *
 * @date           27.01.15
 */

declare(strict_types = 1);

namespace IPubTests\AssetsLoader;

use Nette;

use Tester;
use Tester\Assert;

use IPub\AssetsLoader;

require __DIR__ . '/../bootstrap.php';

class CompilerTest extends Tester\TestCase
{
	public function testCompiler() : void
	{
		$dic = $this->createContainer();

		// Get default asset
		$defaultCssAsset = $dic->getService('assetsLoader.factory')->getAsset('default' . '.' . AssetsLoader\DI\AssetsLoaderExtension::TYPE_CSS);
		// Get collection
		$filesCollection = $defaultCssAsset->getFiles();
		// Get default asset compiler
		$defaultCssCompiler = $dic->getService('assetsLoader.factory')->getAssetCompiler('default' . '.' . AssetsLoader\DI\AssetsLoaderExtension::TYPE_CSS);

		// Process compiler
		$generated = $defaultCssCompiler->generate($filesCollection->getFiles(), 'text/css');

		Assert::true($generated instanceof Nette\Utils\ArrayHash);
		Assert::true(isset($generated->hash));

		// Get assets cache service
		$cacheAssets = $dic->getService('assetsLoader.cache.assets');

		Assert::true(is_array($cacheAssets->load($generated->hash)));
	}

	/**
	 * @return Nette\DI\Container
	 */
	protected function createContainer() : Nette\DI\Container
	{
		$config = new Nette\Configurator();
		$config->setTempDirectory(TEMP_DIR);

		$config->addParameters([
			"staticFilesDir" => realpath(__DIR__ . DIRECTORY_SEPARATOR . 'assets'),
		]);

		AssetsLoader\DI\AssetsLoaderExtension::register($config);

		$config->addConfig(__DIR__ . '/files/config.neon');

		return $config->createContainer();
	}
}

\run(new CompilerTest());
