<?php
/**
 * Test: IPub\AssetsLoader\Extension
 * @testCase
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:AssetsLoader!
 * @subpackage     Tests
 * @since          1.0.0
 *
 * @date           22.01.15
 */

declare(strict_types = 1);

namespace IPubTests\AssetsLoader;

use Nette;

use Tester;
use Tester\Assert;

use IPub\AssetsLoader;

require __DIR__ . '/../bootstrap.php';

class ExtensionTest extends Tester\TestCase
{
	public function testCompilersServices() : void
	{
		$dic = $this->createContainer();

		Assert::true($dic->getService('assetsLoader.cssDefaultCompiler') instanceof AssetsLoader\Compilers\CssCompiler);
		Assert::true($dic->getService('assetsLoader.jsDefaultCompiler') instanceof AssetsLoader\Compilers\JsCompiler);
	}

	public function testFactoryService() : void
	{
		$dic = $this->createContainer();

		Assert::true($dic->getService('assetsLoader.factory') instanceof AssetsLoader\LoaderFactory);
	}

	public function testConfiguration() : void
	{
		$dic = $this->createContainer();

		Assert::true($dic->getService('assetsLoader.factory')->getAsset('default' . '.' . AssetsLoader\DI\AssetsLoaderExtension::TYPE_CSS)->getJoinFiles());
		Assert::false($dic->getService('assetsLoader.factory')->getAsset('first' . '.' . AssetsLoader\DI\AssetsLoaderExtension::TYPE_CSS)->getJoinFiles());

		Assert::true($dic->getService('assetsLoader.factory')->getAsset('default' . '.' . AssetsLoader\DI\AssetsLoaderExtension::TYPE_CSS)->getGzip());
		Assert::false($dic->getService('assetsLoader.factory')->getAsset('first' . '.' . AssetsLoader\DI\AssetsLoaderExtension::TYPE_CSS)->getGzip());
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

\run(new ExtensionTest());